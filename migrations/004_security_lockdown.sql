-- ============================================================
-- NPC Connect — SECURITY LOCKDOWN (Migration 004)
-- Run this in: Supabase Dashboard → SQL Editor
--
-- WHAT THIS DOES:
--   1. Enables RLS on EVERY table in the public schema.
--   2. Drops ALL existing policies (including the dangerous
--      "Allow all" demo policies and the holey 001 policies).
--   3. Grants READ-ONLY access to logged-in users via a minimal,
--      explicit policy set below.
--   4. Blocks ALL client-side writes (INSERT/UPDATE/DELETE) —
--      every write must go through the server-side PHP APIs
--      (api_student.php / api_faculty.php / api_admin.php),
--      which use the service-role key.
--   5. Defense-in-depth: revokes write privileges from the
--      anon/authenticated Postgres roles entirely.
--
-- AFTER RUNNING THIS: all pages keep working because reads go
-- through the user's Supabase JWT (restored automatically by
-- supabase-js from localStorage), and every mutation now goes
-- through the secured PHP endpoints.
-- ============================================================

-- ─── Step 0: Helper functions (SECURITY DEFINER avoids RLS self-recursion) ─────

CREATE OR REPLACE FUNCTION public.app_current_role() RETURNS TEXT
LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
    SELECT role FROM public.users WHERE id::text = auth.uid()::text LIMIT 1;
$$;

CREATE OR REPLACE FUNCTION public.app_is_staff() RETURNS BOOLEAN
LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
    SELECT COALESCE((SELECT role FROM public.users WHERE id::text = auth.uid()::text LIMIT 1) IN ('teacher','faculty','admin'), false);
$$;

CREATE OR REPLACE FUNCTION public.app_is_admin() RETURNS BOOLEAN
LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
    SELECT COALESCE((SELECT role FROM public.users WHERE id::text = auth.uid()::text LIMIT 1) = 'admin', false);
$$;

CREATE OR REPLACE FUNCTION public.app_my_student_number_ref() RETURNS TEXT
LANGUAGE sql STABLE SECURITY DEFINER SET search_path = public AS $$
    SELECT student_number FROM public.users WHERE id::text = auth.uid()::text LIMIT 1;
$$;

-- ─── Step 1: Enable RLS everywhere ─────────────────────────────────────────────

DO $$
DECLARE t RECORD;
BEGIN
    FOR t IN SELECT tablename FROM pg_tables WHERE schemaname = 'public'
    LOOP
        EXECUTE format('ALTER TABLE public.%I ENABLE ROW LEVEL SECURITY', t.tablename);
    END LOOP;
END $$;

-- ─── Step 2: Drop EVERY existing policy on every table ────────────────────────
-- (Removes "Allow all" demo policies + old 001 policies with their holes)

DO $$
DECLARE p RECORD;
BEGIN
    FOR p IN SELECT schemename, tablename, policyname FROM pg_policies WHERE schemename = 'public'
    LOOP
        EXECUTE format('DROP POLICY IF EXISTS %I ON public.%I', p.policyname, p.tablename);
    END LOOP;
END $$;

-- ─── Step 3: Minimal READ-ONLY policy set ──────────────────────────────────────
-- NOTE: There are deliberately NO INSERT/UPDATE/DELETE policies anywhere.
-- All mutations happen server-side via the service-role key.

-- USERS: read your own row (by id or email claim); staff can read the directory.
CREATE POLICY "users_select_own_or_staff" ON public.users FOR SELECT
    USING (
        auth.uid()::text = id::text
        OR lower(email) = lower(COALESCE(auth.jwt() ->> 'email', ''))
        OR public.app_is_staff()
    );

-- CLASSES (schedules): any registered user may read.
CREATE POLICY "classes_select_authenticated" ON public.classes FOR SELECT
    USING (auth.uid() IS NOT NULL);

-- ANNOUNCEMENTS: published only, unless admin (drafts/archived stay private).
CREATE POLICY "announcements_select_published_or_admin" ON public.announcements FOR SELECT
    USING (status = 'published' OR public.app_is_admin());

-- DOCUMENTS metadata: any registered user may list.
CREATE POLICY "documents_select_authenticated" ON public.documents FOR SELECT
    USING (auth.uid() IS NOT NULL);

-- ATTENDANCE RECORDS: your own rows, or staff (live roster polling).
CREATE POLICY "attendance_select_own_or_staff" ON public.attendance_records FOR SELECT
    USING (
        student_id = public.app_my_student_number_ref()
        OR student_number = public.app_my_student_number_ref()
        OR public.app_is_staff()
    );

-- GRADES: your own rows, or staff.
CREATE POLICY "grades_select_own_or_staff" ON public.grades FOR SELECT
    USING (
        student_id = public.app_my_student_number_ref()
        OR student_number = public.app_my_student_number_ref()
        OR public.app_is_staff()
    );

-- CHAT: strictly your own conversations/messages.
CREATE POLICY "chat_conv_select_own" ON public.chat_conversations FOR SELECT
    USING (user_id = auth.uid()::text);
CREATE POLICY "chat_msg_select_own" ON public.chat_messages FOR SELECT
    USING (
        EXISTS (
            SELECT 1 FROM public.chat_conversations cc
            WHERE cc.id = chat_messages.conversation_id
              AND cc.user_id = auth.uid()::text
        )
    );

-- SECURITY LOGS: admins read-only.
CREATE POLICY "security_logs_select_admin" ON public.security_logs FOR SELECT
    USING (public.app_is_admin());

-- EVERYTHING ELSE (attendance_sessions, notifications, document_requests,
-- profile_update_requests, consultation_appointments, grade_submissions,
-- grade_change_requests, grade_components, student_grades, faculty_materials,
-- enrollments, audit_logs, etc.) has RLS enabled and ZERO policies:
-- fully invisible to clients, accessible ONLY through the PHP service APIs.

-- ─── Step 4: Defense-in-depth — revoke client write privileges at the role level

REVOKE INSERT, UPDATE, DELETE, TRUNCATE ON ALL TABLES IN SCHEMA public FROM anon, authenticated;
REVOKE USAGE ON ALL SEQUENCES IN SCHEMA public FROM anon, authenticated;
ALTER DEFAULT PRIVILEGES IN SCHEMA public
    REVOKE INSERT, UPDATE, DELETE, TRUNCATE ON TABLES FROM anon, authenticated;
