-- ============================================================================
-- NPC Connect — Comprehensive Academic, Gradebook & Portal Migration
-- Run this in: Supabase Dashboard → SQL Editor
-- ============================================================================

-- ─── 1. ACADEMIC STRUCTURE TABLES ──────────────────────────────────────────

CREATE TABLE IF NOT EXISTS school_years (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    year_label TEXT NOT NULL UNIQUE, -- e.g. '2026-2027'
    is_active BOOLEAN DEFAULT false,
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS semesters (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    school_year_id UUID REFERENCES school_years(id) ON DELETE CASCADE,
    name TEXT NOT NULL, -- '1st Semester', '2nd Semester', 'Summer'
    is_current BOOLEAN DEFAULT false,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS programs (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    code TEXT NOT NULL UNIQUE, -- e.g. 'BSIS', 'BSAIS', 'BSBA', 'BSED'
    name TEXT NOT NULL,
    department TEXT DEFAULT 'College of Computer Studies',
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS sections (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    program_id UUID REFERENCES programs(id) ON DELETE CASCADE,
    section_name TEXT NOT NULL, -- e.g. '1A', '2A', '3B'
    year_level INT DEFAULT 1,
    created_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(program_id, section_name)
);

CREATE TABLE IF NOT EXISTS subjects (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    code TEXT NOT NULL UNIQUE, -- e.g. 'DM103', 'CS101'
    title TEXT NOT NULL,
    units NUMERIC DEFAULT 3.0,
    lecture_hours INT DEFAULT 3,
    lab_hours INT DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT now()
);

-- Ensure classes table has all columns
DO $$ BEGIN
    -- Add columns if not existing
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='classes' AND column_name='instructor_email') THEN
        ALTER TABLE classes ADD COLUMN instructor_email TEXT;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='classes' AND column_name='school_year') THEN
        ALTER TABLE classes ADD COLUMN school_year TEXT DEFAULT '2026-2027';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='classes' AND column_name='semester') THEN
        ALTER TABLE classes ADD COLUMN semester TEXT DEFAULT '1st Semester, 2026-2027';
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='classes' AND column_name='created_by_email') THEN
        ALTER TABLE classes ADD COLUMN created_by_email TEXT;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM information_schema.columns WHERE table_name='classes' AND column_name='created_by_name') THEN
        ALTER TABLE classes ADD COLUMN created_by_name TEXT;
    END IF;
END $$;

CREATE TABLE IF NOT EXISTS faculty_assignments (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    faculty_email TEXT NOT NULL,
    faculty_name TEXT NOT NULL,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    academic_year TEXT DEFAULT '2026-2027',
    semester TEXT DEFAULT '1st Semester',
    created_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(faculty_email, class_id)
);

-- ─── 2. ENROLLMENT & ROSTER ────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS enrollments (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    student_id TEXT, -- User ID or Student Number
    student_number TEXT NOT NULL,
    student_name TEXT,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    semester TEXT DEFAULT '1st Semester, 2026-2027',
    school_year TEXT DEFAULT '2026-2027',
    status TEXT DEFAULT 'Enrolled', -- 'Enrolled', 'Dropped', 'Completed'
    enrolled_at TIMESTAMPTZ DEFAULT now()
);

-- ─── 3. GRADEBOOK & GRADING SYSTEM ──────────────────────────────────────────

CREATE TABLE IF NOT EXISTS grade_components (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    component_name TEXT NOT NULL, -- 'Quizzes', 'Activities', 'Recitation', 'Attendance', 'Projects', 'Major Exam'
    percentage_weight NUMERIC NOT NULL, -- e.g. 20 (for 20%)
    max_score NUMERIC DEFAULT 100,
    grading_period TEXT DEFAULT 'All', -- 'Prelim', 'Midterm', 'Pre-Final', 'Final', 'All'
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS student_grades (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    student_id TEXT,
    student_number TEXT NOT NULL,
    student_name TEXT,
    prelim NUMERIC DEFAULT 0,
    midterm NUMERIC DEFAULT 0,
    prefinal NUMERIC DEFAULT 0,
    final NUMERIC DEFAULT 0,
    raw_grade NUMERIC DEFAULT 0,
    weighted_grade NUMERIC DEFAULT 0,
    equivalent_grade NUMERIC DEFAULT 0, -- 1.00, 1.25, ..., 5.00
    final_rating NUMERIC DEFAULT 0,
    remarks TEXT DEFAULT 'Ongoing', -- 'Passed', 'Failed', 'INC', 'Dropped', 'NG', 'Ongoing'
    is_locked BOOLEAN DEFAULT false,
    is_published BOOLEAN DEFAULT false,
    submitted_at TIMESTAMPTZ,
    approved_at TIMESTAMPTZ,
    published_at TIMESTAMPTZ,
    updated_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(class_id, student_number)
);

CREATE TABLE IF NOT EXISTS grade_submissions (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    class_code TEXT NOT NULL,
    section TEXT NOT NULL,
    faculty_email TEXT NOT NULL,
    faculty_name TEXT NOT NULL,
    grading_period TEXT DEFAULT 'Final', -- 'Prelim', 'Midterm', 'Pre-Final', 'Final'
    status TEXT DEFAULT 'Draft', -- 'Draft', 'Submitted', 'Approved', 'Published', 'Returned'
    lock_state BOOLEAN DEFAULT false,
    submitted_at TIMESTAMPTZ,
    reviewed_by TEXT,
    reviewed_at TIMESTAMPTZ,
    approved_at TIMESTAMPTZ,
    published_at TIMESTAMPTZ,
    remarks TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    UNIQUE(class_id, grading_period)
);

CREATE TABLE IF NOT EXISTS grade_change_requests (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    class_code TEXT NOT NULL,
    student_number TEXT NOT NULL,
    student_name TEXT NOT NULL,
    faculty_email TEXT NOT NULL,
    faculty_name TEXT NOT NULL,
    original_grade NUMERIC NOT NULL,
    proposed_grade NUMERIC NOT NULL,
    reason TEXT NOT NULL,
    status TEXT DEFAULT 'Pending', -- 'Pending', 'Approved', 'Rejected'
    requested_at TIMESTAMPTZ DEFAULT now(),
    reviewed_by TEXT,
    reviewed_at TIMESTAMPTZ,
    admin_remarks TEXT
);

-- ─── 4. ATTENDANCE SYSTEM ──────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS attendance_sessions (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    class_code TEXT NOT NULL,
    section TEXT NOT NULL DEFAULT 'AIS 2A',
    instructor TEXT,
    session_code TEXT NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT true,
    present_until TIMESTAMPTZ,
    late_until TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS attendance_records (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    student_id TEXT NOT NULL,
    student_name TEXT,
    student_number TEXT NOT NULL,
    session_code TEXT NOT NULL,
    check_in_at TIMESTAMPTZ DEFAULT now(),
    method TEXT DEFAULT 'qr_code', -- 'qr_code', 'manual'
    status TEXT DEFAULT 'present', -- 'present', 'late', 'absent', 'excused'
    remarks TEXT,
    corrected_by TEXT,
    corrected_reason TEXT
);

-- ─── 5. STUDENT SERVICES & REQUESTS ────────────────────────────────────────

CREATE TABLE IF NOT EXISTS document_requests (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    student_number TEXT NOT NULL,
    student_name TEXT NOT NULL,
    student_email TEXT NOT NULL,
    document_type TEXT NOT NULL, -- 'Certificate of Registration', 'Certificate of Enrollment', 'Certificate of Good Moral', 'Official Transcript of Records (OTR)', 'Grade Slip'
    purpose TEXT NOT NULL,
    reference_no TEXT NOT NULL UNIQUE,
    status TEXT DEFAULT 'Pending', -- 'Pending', 'Processing', 'Ready for Pickup', 'Released', 'Rejected'
    file_url TEXT,
    processed_by TEXT,
    remarks TEXT,
    requested_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS profile_update_requests (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    student_number TEXT NOT NULL,
    student_name TEXT NOT NULL,
    student_email TEXT NOT NULL,
    current_data JSONB,
    requested_changes JSONB NOT NULL,
    reason TEXT,
    status TEXT DEFAULT 'Pending', -- 'Pending', 'Approved', 'Rejected'
    reviewed_by TEXT,
    reviewed_at TIMESTAMPTZ,
    remarks TEXT,
    requested_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS notifications (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    user_email TEXT NOT NULL,
    user_id TEXT,
    title TEXT NOT NULL,
    message TEXT NOT NULL,
    type TEXT DEFAULT 'info', -- 'grade', 'attendance', 'announcement', 'document', 'system'
    is_read BOOLEAN DEFAULT false,
    link_url TEXT,
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS consultation_appointments (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    faculty_email TEXT NOT NULL,
    faculty_name TEXT NOT NULL,
    student_number TEXT NOT NULL,
    student_name TEXT NOT NULL,
    student_email TEXT NOT NULL,
    subject_code TEXT,
    requested_date DATE NOT NULL,
    requested_time TEXT NOT NULL,
    topic TEXT NOT NULL,
    status TEXT DEFAULT 'Pending', -- 'Pending', 'Confirmed', 'Declined', 'Completed'
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS faculty_materials (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    faculty_email TEXT NOT NULL,
    class_id UUID REFERENCES classes(id) ON DELETE CASCADE,
    title TEXT NOT NULL,
    description TEXT,
    file_name TEXT NOT NULL,
    file_url TEXT,
    file_size TEXT,
    category TEXT DEFAULT 'Syllabus', -- 'Syllabus', 'Lecture Notes', 'Assignment', 'Exam Guide'
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id UUID DEFAULT gen_random_uuid() PRIMARY KEY,
    action TEXT NOT NULL, -- e.g. 'GRADE_SUBMIT', 'GRADE_APPROVE', 'GRADE_PUBLISH', 'GRADE_CHANGE', 'ATTENDANCE_CORRECT'
    table_name TEXT NOT NULL,
    record_id TEXT,
    old_data JSONB,
    new_data JSONB,
    performed_by TEXT NOT NULL,
    ip_address TEXT DEFAULT '127.0.0.1',
    performed_at TIMESTAMPTZ DEFAULT now()
);

-- ─── 6. PERFORMANCE INDEXES ────────────────────────────────────────────────

CREATE INDEX IF NOT EXISTS idx_classes_code ON classes(code);
CREATE INDEX IF NOT EXISTS idx_classes_instructor_email ON classes(instructor_email);
CREATE INDEX IF NOT EXISTS idx_enrollments_student_number ON enrollments(student_number);
CREATE INDEX IF NOT EXISTS idx_enrollments_class_id ON enrollments(class_id);
CREATE INDEX IF NOT EXISTS idx_student_grades_class_id ON student_grades(class_id);
CREATE INDEX IF NOT EXISTS idx_student_grades_student_num ON student_grades(student_number);
CREATE INDEX IF NOT EXISTS idx_attendance_records_student_num ON attendance_records(student_number);
CREATE INDEX IF NOT EXISTS idx_attendance_records_session ON attendance_records(session_code);
CREATE INDEX IF NOT EXISTS idx_attendance_sessions_code ON attendance_sessions(session_code);
CREATE INDEX IF NOT EXISTS idx_document_requests_student ON document_requests(student_number);
CREATE INDEX IF NOT EXISTS idx_notifications_email ON notifications(user_email);
CREATE INDEX IF NOT EXISTS idx_audit_logs_action ON audit_logs(action);

-- ─── 7. ROW LEVEL SECURITY (RLS) POLICIES ──────────────────────────────────

ALTER TABLE school_years ENABLE ROW LEVEL SECURITY;
ALTER TABLE semesters ENABLE ROW LEVEL SECURITY;
ALTER TABLE programs ENABLE ROW LEVEL SECURITY;
ALTER TABLE sections ENABLE ROW LEVEL SECURITY;
ALTER TABLE subjects ENABLE ROW LEVEL SECURITY;
ALTER TABLE faculty_assignments ENABLE ROW LEVEL SECURITY;
ALTER TABLE enrollments ENABLE ROW LEVEL SECURITY;
ALTER TABLE grade_components ENABLE ROW LEVEL SECURITY;
ALTER TABLE student_grades ENABLE ROW LEVEL SECURITY;
ALTER TABLE grade_submissions ENABLE ROW LEVEL SECURITY;
ALTER TABLE grade_change_requests ENABLE ROW LEVEL SECURITY;
ALTER TABLE attendance_sessions ENABLE ROW LEVEL SECURITY;
ALTER TABLE attendance_records ENABLE ROW LEVEL SECURITY;
ALTER TABLE document_requests ENABLE ROW LEVEL SECURITY;
ALTER TABLE profile_update_requests ENABLE ROW LEVEL SECURITY;
ALTER TABLE notifications ENABLE ROW LEVEL SECURITY;
ALTER TABLE consultation_appointments ENABLE ROW LEVEL SECURITY;
ALTER TABLE faculty_materials ENABLE ROW LEVEL SECURITY;
ALTER TABLE audit_logs ENABLE ROW LEVEL SECURITY;

-- Drop old policies to prevent duplication
DROP POLICY IF EXISTS "sy_select" ON school_years;
DROP POLICY IF EXISTS "sem_select" ON semesters;
DROP POLICY IF EXISTS "prog_select" ON programs;
DROP POLICY IF EXISTS "sec_select" ON sections;
DROP POLICY IF EXISTS "sub_select" ON subjects;
DROP POLICY IF EXISTS "enroll_select" ON enrollments;
DROP POLICY IF EXISTS "sg_select" ON student_grades;
DROP POLICY IF EXISTS "notif_select" ON notifications;
DROP POLICY IF EXISTS "docreq_select" ON document_requests;

-- Basic Academic Structure (Readable by all authenticated users, editable by admin)
CREATE POLICY "sy_select" ON school_years FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "sem_select" ON semesters FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "prog_select" ON programs FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "sec_select" ON sections FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "sub_select" ON subjects FOR SELECT USING (auth.uid() IS NOT NULL);
CREATE POLICY "mat_select" ON faculty_materials FOR SELECT USING (auth.uid() IS NOT NULL);

-- Enrollments: Students see their own enrollments, faculty & admin see class rosters
CREATE POLICY "enroll_select" ON enrollments FOR SELECT
USING (
    student_number = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
    OR EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar'))
);

-- Student Grades:
-- 1. Students can only see their OWN grades AND ONLY IF is_published = true
-- 2. Faculty can see grades for classes they teach
-- 3. Admin & Registrar can see and manage all
CREATE POLICY "student_grades_select" ON student_grades FOR SELECT
USING (
    (
        is_published = true 
        AND student_number = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
    )
    OR EXISTS (
        SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar')
    )
);

CREATE POLICY "student_grades_insert_staff" ON student_grades FOR INSERT
WITH CHECK (
    EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar'))
);

CREATE POLICY "student_grades_update_staff" ON student_grades FOR UPDATE
USING (
    EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar'))
);

-- Grade Submissions & Requests (Faculty and Admin only)
CREATE POLICY "gsub_select" ON grade_submissions FOR SELECT
USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar')));

CREATE POLICY "gcr_select" ON grade_change_requests FOR SELECT
USING (EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar')));

-- Document Requests: Student sees own requests, staff sees all
CREATE POLICY "docreq_select" ON document_requests FOR SELECT
USING (
    student_number = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
    OR EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role IN ('admin', 'teacher', 'faculty', 'registrar'))
);

CREATE POLICY "docreq_insert_student" ON document_requests FOR INSERT
WITH CHECK (auth.uid() IS NOT NULL);

-- Notifications: User sees only own notifications
CREATE POLICY "notif_select" ON notifications FOR SELECT
USING (
    user_email = (SELECT email FROM users WHERE id::text = auth.uid()::text)
    OR user_id = auth.uid()::text
);

-- Consultations: Student or assigned faculty
CREATE POLICY "consult_select" ON consultation_appointments FOR SELECT
USING (
    student_number = (SELECT student_number FROM users WHERE id::text = auth.uid()::text)
    OR faculty_email = (SELECT email FROM users WHERE id::text = auth.uid()::text)
    OR EXISTS (SELECT 1 FROM users u WHERE u.id::text = auth.uid()::text AND u.role = 'admin')
);

CREATE POLICY "consult_insert" ON consultation_appointments FOR INSERT
WITH CHECK (auth.uid() IS NOT NULL);

-- ─── 8. DEFAULT SEED DATA ──────────────────────────────────────────────────

INSERT INTO school_years (year_label, is_active) VALUES
('2026-2027', true),
('2025-2026', false)
ON CONFLICT (year_label) DO NOTHING;

INSERT INTO programs (code, name, department) VALUES
('BSIS', 'Bachelor of Science in Information Systems', 'College of Computer Studies'),
('BSAIS', 'Bachelor of Science in Accounting Information System', 'College of Business & Management'),
('BSBA', 'Bachelor of Science in Business Administration', 'College of Business & Management'),
('BSED', 'Bachelor of Secondary Education', 'College of Education')
ON CONFLICT (code) DO NOTHING;

INSERT INTO subjects (code, title, units, lecture_hours, lab_hours) VALUES
('DM103', 'Business Process Management', 3.0, 3, 0),
('CS101', 'Introduction to Computing', 3.0, 2, 3),
('IT201', 'Database Management Systems', 3.0, 2, 3),
('GE101', 'Understanding the Self', 3.0, 3, 0),
('MATH201', 'Quantitative Methods', 3.0, 3, 0),
('ENG102', 'Technical Communication', 3.0, 3, 0)
ON CONFLICT (code) DO NOTHING;
