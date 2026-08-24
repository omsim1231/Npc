-- ============================================
-- NPC Connect — Missing Tables and Constraints
-- Run this in: Supabase Dashboard → SQL Editor
-- ============================================

-- ─── Step 1: Attendance Sessions Table ───────────────────────────────────────
-- Required for the secure QR attendance flow
CREATE TABLE IF NOT EXISTS attendance_sessions (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    teacher_id TEXT NOT NULL,
    session_code TEXT NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT true,
    expires_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT now()
);

-- Apply RLS policies for attendance_sessions
ALTER TABLE attendance_sessions ENABLE ROW LEVEL SECURITY;

CREATE POLICY "attsess_select_auth" ON attendance_sessions FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "attsess_insert_staff" ON attendance_sessions FOR INSERT WITH CHECK (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')));
CREATE POLICY "attsess_update_staff" ON attendance_sessions FOR UPDATE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')));
CREATE POLICY "attsess_delete_admin" ON attendance_sessions FOR DELETE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'));

-- ─── Step 2: Audit Logs Table ────────────────────────────────────────────────
-- Required for tracking sensitive actions like grade changes and deletions
CREATE TABLE IF NOT EXISTS audit_logs (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    action TEXT NOT NULL,
    table_name TEXT NOT NULL,
    record_id TEXT,
    old_data JSONB,
    new_data JSONB,
    performed_by TEXT NOT NULL, -- Email or ID of the user
    performed_at TIMESTAMPTZ DEFAULT now()
);

-- Apply RLS policies for audit_logs (Admins read, Service Role inserts)
ALTER TABLE audit_logs ENABLE ROW LEVEL SECURITY;

CREATE POLICY "audit_select_admin" ON audit_logs FOR SELECT USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'));
CREATE POLICY "audit_insert_service" ON audit_logs FOR INSERT WITH CHECK (true); -- Service key only

-- ─── Step 3: Enrollments Table ───────────────────────────────────────────────
-- Robust relationship between students and classes (replacing student_classes if needed)
CREATE TABLE IF NOT EXISTS enrollments (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    student_id TEXT NOT NULL, -- Matches users.student_number
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    semester TEXT DEFAULT '1st Semester, 2026-2027',
    status TEXT DEFAULT 'Enrolled', -- 'Enrolled', 'Dropped', 'Completed'
    created_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(student_id, class_id, semester)
);

-- Apply RLS policies for enrollments
ALTER TABLE enrollments ENABLE ROW LEVEL SECURITY;

CREATE POLICY "enroll_select_auth" ON enrollments FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "enroll_insert_admin" ON enrollments FOR INSERT WITH CHECK (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')));
CREATE POLICY "enroll_update_admin" ON enrollments FOR UPDATE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher')));
CREATE POLICY "enroll_delete_admin" ON enrollments FOR DELETE USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin'));

-- ─── Step 4: Constraints & Integrity ─────────────────────────────────────────

-- Ensure role is valid
DO $$ BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_constraint WHERE conname = 'users_role_check') THEN
        ALTER TABLE users ADD CONSTRAINT users_role_check CHECK (role IN ('student', 'teacher', 'admin', 'faculty'));
    END IF;
END $$;
