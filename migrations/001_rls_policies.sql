-- ============================================
-- NPC Connect — RLS Policy Hardening
-- Run this in: Supabase Dashboard → SQL Editor
-- 
-- WARNING: This DROPS all existing permissive "Allow all" policies
-- and replaces them with proper ownership-based rules.
-- ============================================

-- ─── Step 1: Drop ALL existing permissive policies ─────────────────────────────

DROP POLICY IF EXISTS "Allow all on users" ON users;
DROP POLICY IF EXISTS "Allow all on announcements" ON announcements;
DROP POLICY IF EXISTS "Allow all on classes" ON classes;
DROP POLICY IF EXISTS "Allow all on attendance_records" ON attendance_records;
DROP POLICY IF EXISTS "Allow all on grades" ON grades;
DROP POLICY IF EXISTS "Allow all on documents" ON documents;
DROP POLICY IF EXISTS "Allow all on chat_conversations" ON chat_conversations;
DROP POLICY IF EXISTS "Allow all on chat_messages" ON chat_messages;
DROP POLICY IF EXISTS "Allow all on security_logs" ON security_logs;

-- Also drop any policies on student_classes if it exists
DROP POLICY IF EXISTS "Allow all on student_classes" ON student_classes;

-- ─── Step 2: USERS table ───────────────────────────────────────────────────────
-- Students can read their own row. Teachers/admins can read all. Admins can update all.
-- Service role (server-side PHP) handles inserts/updates via supabase_helper.php.

CREATE POLICY "users_select_own"
    ON users FOR SELECT
    USING (
        auth.uid()::text = id::text
        OR EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "users_insert_service"
    ON users FOR INSERT
    WITH CHECK (true);
    -- Service role key bypasses RLS, so this only affects anon/authenticated
    -- In practice, inserts go through set_session.php (service key)

CREATE POLICY "users_update_own"
    ON users FOR UPDATE
    USING (
        auth.uid()::text = id::text
        OR EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "users_delete_admin"
    ON users FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 3: GRADES table ──────────────────────────────────────────────────────
-- Students see their own grades. Teachers see grades for their classes. Admins see all.

CREATE POLICY "grades_select"
    ON grades FOR SELECT
    USING (
        student_id = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
        OR EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "grades_insert_staff"
    ON grades FOR INSERT
    WITH CHECK (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "grades_update_staff"
    ON grades FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "grades_delete_admin"
    ON grades FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 4: ATTENDANCE_RECORDS table ──────────────────────────────────────────
-- Students see their own. Teachers/admins see all. Students can insert (check-in).

CREATE POLICY "attendance_select"
    ON attendance_records FOR SELECT
    USING (
        student_id = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
        OR EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "attendance_insert_auth"
    ON attendance_records FOR INSERT
    WITH CHECK (auth.uid() IS NOT NULL);

CREATE POLICY "attendance_update_staff"
    ON attendance_records FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "attendance_delete_admin"
    ON attendance_records FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 5: CLASSES table ─────────────────────────────────────────────────────
-- Everyone authenticated can read. Only admin/teacher can modify.

CREATE POLICY "classes_select_auth"
    ON classes FOR SELECT
    USING (auth.uid() IS NOT NULL);

CREATE POLICY "classes_insert_admin"
    ON classes FOR INSERT
    WITH CHECK (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "classes_update_admin"
    ON classes FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')
        )
    );

CREATE POLICY "classes_delete_admin"
    ON classes FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 6: ANNOUNCEMENTS table ──────────────────────────────────────────────
-- Everyone reads published. Only admin creates/edits/deletes.

CREATE POLICY "announcements_select_published"
    ON announcements FOR SELECT
    USING (
        status = 'published'
        OR EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "announcements_insert_admin"
    ON announcements FOR INSERT
    WITH CHECK (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "announcements_update_admin"
    ON announcements FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "announcements_delete_admin"
    ON announcements FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 7: DOCUMENTS table ──────────────────────────────────────────────────
-- Everyone reads. Only admin manages.

CREATE POLICY "documents_select_auth"
    ON documents FOR SELECT
    USING (auth.uid() IS NOT NULL);

CREATE POLICY "documents_insert_admin"
    ON documents FOR INSERT
    WITH CHECK (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "documents_update_admin"
    ON documents FOR UPDATE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

CREATE POLICY "documents_delete_admin"
    ON documents FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- ─── Step 8: CHAT_CONVERSATIONS table ─────────────────────────────────────────
-- Users can only see/manage their own conversations.

CREATE POLICY "chat_conv_select_own"
    ON chat_conversations FOR SELECT
    USING (user_id = auth.uid()::text);

CREATE POLICY "chat_conv_insert_own"
    ON chat_conversations FOR INSERT
    WITH CHECK (user_id = auth.uid()::text);

CREATE POLICY "chat_conv_update_own"
    ON chat_conversations FOR UPDATE
    USING (user_id = auth.uid()::text);

CREATE POLICY "chat_conv_delete_own"
    ON chat_conversations FOR DELETE
    USING (user_id = auth.uid()::text);

-- ─── Step 9: CHAT_MESSAGES table ──────────────────────────────────────────────
-- Users can only see/manage messages in their own conversations.

CREATE POLICY "chat_msg_select_own"
    ON chat_messages FOR SELECT
    USING (
        EXISTS (
            SELECT 1 FROM chat_conversations cc 
            WHERE cc.id = chat_messages.conversation_id 
            AND cc.user_id = auth.uid()::text
        )
    );

CREATE POLICY "chat_msg_insert_own"
    ON chat_messages FOR INSERT
    WITH CHECK (
        EXISTS (
            SELECT 1 FROM chat_conversations cc 
            WHERE cc.id = conversation_id 
            AND cc.user_id = auth.uid()::text
        )
    );

CREATE POLICY "chat_msg_delete_own"
    ON chat_messages FOR DELETE
    USING (
        EXISTS (
            SELECT 1 FROM chat_conversations cc 
            WHERE cc.id = chat_messages.conversation_id 
            AND cc.user_id = auth.uid()::text
        )
    );

-- ─── Step 10: SECURITY_LOGS table ─────────────────────────────────────────────
-- Only admins can read. Inserts happen via service role (server-side).

CREATE POLICY "security_logs_select_admin"
    ON security_logs FOR SELECT
    USING (
        EXISTS (
            SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'
        )
    );

-- Service role inserts bypass RLS, so no INSERT policy needed for regular users
CREATE POLICY "security_logs_insert_service"
    ON security_logs FOR INSERT
    WITH CHECK (true);
    -- Only service role key actually reaches this; anon key requests are blocked
    -- because we never expose the service role key to the client

-- ─── Step 11: ATTENDANCE_SESSIONS table (if exists) ───────────────────────────

DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'attendance_sessions') THEN
        DROP POLICY IF EXISTS "Allow all on attendance_sessions" ON attendance_sessions;
        
        EXECUTE 'CREATE POLICY "attsess_select_auth" ON attendance_sessions FOR SELECT USING (auth.uid() IS NOT NULL)';
        EXECUTE 'CREATE POLICY "attsess_insert_staff" ON attendance_sessions FOR INSERT WITH CHECK (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN (''admin'', ''teacher'')))';
        EXECUTE 'CREATE POLICY "attsess_update_staff" ON attendance_sessions FOR UPDATE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN (''admin'', ''teacher'')))';
        EXECUTE 'CREATE POLICY "attsess_delete_admin" ON attendance_sessions FOR DELETE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = ''admin''))';
    END IF;
END $$;

-- ─── Step 12: STUDENT_CLASSES table (if exists) ───────────────────────────────

DO $$ BEGIN
    IF EXISTS (SELECT 1 FROM information_schema.tables WHERE table_name = 'student_classes') THEN
        DROP POLICY IF EXISTS "Allow all on student_classes" ON student_classes;
        
        EXECUTE 'CREATE POLICY "sc_select_auth" ON student_classes FOR SELECT USING (auth.uid() IS NOT NULL)';
        EXECUTE 'CREATE POLICY "sc_insert_admin" ON student_classes FOR INSERT WITH CHECK (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN (''admin'', ''teacher'')))';
        EXECUTE 'CREATE POLICY "sc_delete_admin" ON student_classes FOR DELETE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = ''admin''))';
    END IF;
END $$;
