<?php
/**
 * api_faculty.php — Server-Side API for Faculty/Professor Operations
 * 
 * Handles:
 *  - Manual attendance correction with reason & audit logging
 *  - Exportable / printable attendance summary
 *  - Consultation appointment status management
 *  - Secure class materials upload
 *  - Section-specific announcements
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/supabase_helper.php';

require_teacher();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$currentUserEmail = strtolower($_SESSION['email'] ?? '');
$currentUserName = $_SESSION['name'] ?? 'Faculty';
$currentUserRole = $_SESSION['role'] ?? 'teacher';
$isAdmin = ($currentUserRole === 'admin' || $currentUserRole === 'registrar');

// ─── 1. POST: Manual Attendance Correction with Required Reason ────────────────
if ($method === 'POST' && $action === 'correct_attendance') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $recordId = trim($input['record_id'] ?? '');
    $studentNumber = trim($input['student_number'] ?? '');
    $studentName = trim($input['student_name'] ?? '');
    $sessionCode = trim($input['session_code'] ?? '');
    $newStatus = trim($input['status'] ?? 'present'); // 'present', 'late', 'absent', 'excused'
    $reason = trim($input['reason'] ?? '');

    if (empty($reason) || empty($studentNumber) || empty($sessionCode)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Student number, session code, and correction reason are required.']);
        exit;
    }

    if (!empty($recordId)) {
        // Update existing attendance record
        supabaseServiceQuery(
            "/rest/v1/attendance_records?id=eq.$recordId",
            'PATCH',
            [
                'status' => $newStatus,
                'corrected_by' => $currentUserEmail,
                'corrected_reason' => $reason,
                'remarks' => "Status manually updated to $newStatus by Prof. $currentUserName"
            ]
        );
    } else {
        // Insert corrected record if student had no prior scan
        supabaseServiceQuery(
            "/rest/v1/attendance_records",
            'POST',
            [[
                'student_id' => $studentNumber,
                'student_name' => $studentName,
                'student_number' => $studentNumber,
                'session_code' => $sessionCode,
                'check_in_at' => date('c'),
                'method' => 'manual',
                'status' => $newStatus,
                'corrected_by' => $currentUserEmail,
                'corrected_reason' => $reason,
                'remarks' => "Manual entry by Prof. $currentUserName"
            ]]
        );
    }

    logSecurityEvent("ATTENDANCE_CORRECTED: Student $studentNumber in $sessionCode -> $newStatus by $currentUserEmail. Reason: $reason", $currentUserEmail, 'Medium');

    echo json_encode([
        'success' => true,
        'message' => "Attendance status updated to $newStatus!"
    ]);
    exit;
}

// ─── 2. GET: Faculty Consultation Appointments List ────────────────────────────
if ($method === 'GET' && $action === 'get_consultations') {
    $cQuery = supabaseServiceQuery("/rest/v1/consultation_appointments?faculty_email=eq." . urlencode($currentUserEmail) . "&order=requested_date.desc");
    $appts = ($cQuery['status'] === 200 && is_array($cQuery['data'])) ? $cQuery['data'] : [];

    echo json_encode(['success' => true, 'appointments' => $appts]);
    exit;
}

// ─── 3. POST: Update Consultation Appointment Status ───────────────────────────
if ($method === 'POST' && $action === 'update_consultation_status') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $id = trim($input['appointment_id'] ?? '');
    $status = trim($input['status'] ?? ''); // 'Confirmed', 'Declined', 'Completed'
    $notes = trim($input['notes'] ?? '');

    if (empty($id) || !in_array($status, ['Confirmed', 'Declined', 'Completed'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Valid appointment ID and status required.']);
        exit;
    }

    // Fetch appointment
    $aQuery = supabaseServiceQuery("/rest/v1/consultation_appointments?id=eq.$id&limit=1");
    if ($aQuery['status'] === 200 && !empty($aQuery['data'])) {
        $appt = $aQuery['data'][0];

        supabaseServiceQuery(
            "/rest/v1/consultation_appointments?id=eq.$id",
            'PATCH',
            [
                'status' => $status,
                'notes' => $notes
            ]
        );

        // Notify student
        supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
            'user_email' => $appt['student_email'],
            'title' => "Consultation Request $status",
            'message' => "Prof. $currentUserName has marked your consultation request for {$appt['subject_code']} on {$appt['requested_date']} as $status." . (!empty($notes) ? " Notes: $notes" : ''),
            'type' => 'system',
            'link_url' => 'academic.php'
        ]]);
    }

    echo json_encode(['success' => true, 'message' => "Consultation request marked as $status."]);
    exit;
}

// ─── 4. POST: Secure Upload Class Material / Syllabus ──────────────────────────
if ($method === 'POST' && $action === 'upload_material') {
    requireCsrf();

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No file uploaded or upload error occurred.']);
        exit;
    }

    $file = $_FILES['file'];
    $classId = trim($_POST['class_id'] ?? '');
    $title = trim($_POST['title'] ?? pathinfo($file['name'], PATHINFO_FILENAME));
    $category = trim($_POST['category'] ?? 'Lecture Notes');

    // Validate size (15MB max)
    if ($file['size'] > 15 * 1024 * 1024) {
        http_response_code(413);
        echo json_encode(['success' => false, 'message' => 'File exceeds 15MB maximum size.']);
        exit;
    }

    // Validate MIME types
    $allowedMimeTypes = [
        'application/pdf',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'image/jpeg',
        'image/png'
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowedMimeTypes)) {
        http_response_code(415);
        echo json_encode(['success' => false, 'message' => 'File type not permitted. Allowed: PDF, Word, PowerPoint, Excel, Images.']);
        exit;
    }

    // Storage destination
    $destDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'documents';
    if (!is_dir($destDir)) mkdir($destDir, 0755, true);

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $safeFilename = 'mat_' . substr(hash('sha256', uniqid('', true) . $file['name']), 0, 16) . '.' . $ext;
    $targetPath = $destDir . DIRECTORY_SEPARATOR . $safeFilename;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Record in faculty_materials table
        $matData = [
            'faculty_email' => $currentUserEmail,
            'class_id' => !empty($classId) ? $classId : null,
            'title' => $title,
            'file_name' => $safeFilename,
            'file_size' => number_format($file['size'] / 1048576, 1) . ' MB',
            'category' => $category
        ];

        supabaseServiceQuery("/rest/v1/faculty_materials", 'POST', [$matData]);

        logSecurityEvent("MATERIAL_UPLOADED: $title ($safeFilename) by $currentUserEmail", $currentUserEmail, 'Low');

        echo json_encode([
            'success' => true,
            'filename' => $safeFilename,
            'title' => $title,
            'message' => 'Class material uploaded securely!'
        ]);
        exit;
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Failed to save file to server storage.']);
        exit;
    }
}

// ─── 5. GET: List Class Materials ──────────────────────────────────────────────
if ($method === 'GET' && $action === 'get_materials') {
    $classId = trim($_GET['class_id'] ?? '');
    $endpoint = "/rest/v1/faculty_materials?order=created_at.desc";
    if (!empty($classId)) {
        $endpoint = "/rest/v1/faculty_materials?class_id=eq.$classId&order=created_at.desc";
    }

    $mQuery = supabaseServiceQuery($endpoint);
    $materials = ($mQuery['status'] === 200 && is_array($mQuery['data'])) ? $mQuery['data'] : [];

    echo json_encode(['success' => true, 'materials' => $materials]);
    exit;
}

// ─── Shared: verify a class belongs to the requesting faculty ──────────────────
function facultyOwnsClass(array $class, string $email, bool $isAdmin): bool {
    if ($isAdmin) return true;
    $cEmail = strtolower(trim($class['created_by_email'] ?? ''));
    $myEmail = strtolower(trim($email));
    return $cEmail !== '' && $myEmail !== '' && $cEmail === $myEmail;
}

function jsonFail(int $code, string $msg): void {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

// ─── 6. POST: Start Live Attendance Session (server-authorized) ────────────────
if ($method === 'POST' && $action === 'start_session') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $classId = trim($input['class_id'] ?? '');
    if (empty($classId)) jsonFail(400, 'Class ID is required.');

    $cQuery = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($classId) . "&limit=1");
    $class = ($cQuery['status'] === 200 && !empty($cQuery['data'])) ? $cQuery['data'][0] : null;
    if (!$class) jsonFail(404, 'Class not found.');
    if (!facultyOwnsClass($class, $currentUserEmail, $isAdmin)) {
        logSecurityEvent("ACCESS_DENIED: $currentUserEmail tried to start session for class $classId", $currentUserEmail, 'High');
        jsonFail(403, 'You do not own this class.');
    }

    $presentMins = max(1, min(180, intval($input['present_mins'] ?? 10)));
    $lateMins = max(0, min(180, intval($input['late_mins'] ?? 5)));

    // Session code built server-side from verified class data
    $safeCodePart = preg_replace('/[^A-Za-z0-9]/', '', $class['code'] ?? 'CLASS');
    $sessionCode = strtoupper("NPC-$safeCodePart-" . date('Y-m-d'));
    $nowTs = time();
    $presentUntil = date('c', $nowTs + $presentMins * 60);
    $lateUntil = date('c', $nowTs + ($presentMins + $lateMins) * 60);

    supabaseServiceQuery("/rest/v1/attendance_sessions", 'POST',
        [[
            'session_code' => $sessionCode,
            'class_id' => $classId,
            'class_code' => $class['code'] ?? '',
            'section' => $class['section'] ?? '',
            'instructor' => $class['instructor'] ?? $currentUserName,
            'is_active' => true,
            'present_until' => $presentUntil,
            'late_until' => $lateUntil,
            'created_at' => date('c')
        ]],
        ["Prefer: resolution=merge-duplicates"]
    );

    logSecurityEvent("SESSION_STARTED: $sessionCode by $currentUserEmail", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'session_code' => $sessionCode,
        'present_until' => $presentUntil,
        'late_until' => $lateUntil,
        'message' => 'Live attendance session started.'
    ]);
    exit;
}

// ─── 7. POST: End Live Attendance Session ──────────────────────────────────────
if ($method === 'POST' && $action === 'end_session') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);
    $sessionCode = trim($input['session_code'] ?? '');
    if (empty($sessionCode)) jsonFail(400, 'Session code required.');

    $sQuery = supabaseServiceQuery("/rest/v1/attendance_sessions?session_code=eq." . rawurlencode($sessionCode) . "&order=created_at.desc&limit=1");
    $session = ($sQuery['status'] === 200 && !empty($sQuery['data'])) ? $sQuery['data'][0] : null;
    if (!$session) jsonFail(404, 'Session not found.');

    if (!$isAdmin) {
        $cid = $session['class_id'] ?? '';
        $cQuery = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($cid) . "&limit=1");
        $class = ($cQuery['status'] === 200 && !empty($cQuery['data'])) ? $cQuery['data'][0] : null;
        if (!$class || !facultyOwnsClass($class, $currentUserEmail, false)) {
            logSecurityEvent("ACCESS_DENIED: $currentUserEmail tried to end foreign session $sessionCode", $currentUserEmail, 'High');
            jsonFail(403, 'You do not own this session.');
        }
    }

    supabaseServiceQuery(
        "/rest/v1/attendance_sessions?session_code=eq." . rawurlencode($sessionCode),
        'PATCH',
        ['is_active' => false]
    );

    echo json_encode(['success' => true, 'message' => 'Session closed.']);
    exit;
}

// ─── 8. GET: Live roster for one of your sessions ──────────────────────────────
if ($method === 'GET' && $action === 'get_live_roster') {
    $sessionCode = trim($_GET['session_code'] ?? '');
    if (empty($sessionCode)) jsonFail(400, 'Session code required.');

    $sQuery = supabaseServiceQuery("/rest/v1/attendance_sessions?session_code=eq." . rawurlencode($sessionCode) . "&order=created_at.desc&limit=1");
    $session = ($sQuery['status'] === 200 && !empty($sQuery['data'])) ? $sQuery['data'][0] : null;

    // If no session row exists yet (started before first upsert), allow staff read
    if ($session && !$isAdmin) {
        $cid = $session['class_id'] ?? '';
        $cQuery = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($cid) . "&limit=1");
        $class = ($cQuery['status'] === 200 && !empty($cQuery['data'])) ? $cQuery['data'][0] : null;
        if (!$class || !facultyOwnsClass($class, $currentUserEmail, false)) {
            jsonFail(403, 'You do not own this session.');
        }
    }

    $sc = rawurlencode($sessionCode);
    $rQuery = supabaseServiceQuery("/rest/v1/attendance_records?session_code=eq.$sc&order=check_in_at.asc&select=id,student_id,student_name,student_number,session_code,check_in_at,status,method");
    $records = ($rQuery['status'] === 200 && is_array($rQuery['data'])) ? $rQuery['data'] : [];

    echo json_encode(['success' => true, 'records' => $records]);
    exit;
}

// ─── 9. GET: Students enrolled in one of your sections ─────────────────────────
if ($method === 'GET' && $action === 'get_section_students') {
    $section = trim($_GET['section'] ?? '');
    if (empty($section)) jsonFail(400, 'Section required.');

    $uQuery = supabaseServiceQuery("/rest/v1/users?role=eq.student&select=full_name,student_number,program,section");
    $students = ($uQuery['status'] === 200 && is_array($uQuery['data'])) ? $uQuery['data'] : [];

    $targetSec = strtoupper(trim($section));
    $matched = array_values(array_filter($students, function ($s) use ($targetSec) {
        $sSec = strtoupper(trim(($s['program'] ?? '') . ' ' . ($s['section'] ?? '')));
        return $sSec === $targetSec
            || (strpos($sSec, $targetSec) !== false)
            || (strpos($targetSec, strtoupper(trim($s['section'] ?? ''))) !== false && trim($s['section'] ?? '') !== '');
    }));

    usort($matched, fn($a, $b) => strcmp($a['full_name'] ?? '', $b['full_name'] ?? ''));
    echo json_encode(['success' => true, 'students' => $matched]);
    exit;
}

// ─── 10. POST: Mark all enrolled students present (bulk override) ──────────────
if ($method === 'POST' && $action === 'mark_all_present') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $classId = trim($input['class_id'] ?? '');
    $sessionCode = trim($input['session_code'] ?? '');
    if (empty($classId) || empty($sessionCode)) jsonFail(400, 'Class ID and session code are required.');

    $cQuery = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($classId) . "&limit=1");
    $class = ($cQuery['status'] === 200 && !empty($cQuery['data'])) ? $cQuery['data'][0] : null;
    if (!$class) jsonFail(404, 'Class not found.');
    if (!facultyOwnsClass($class, $currentUserEmail, $isAdmin)) {
        logSecurityEvent("ACCESS_DENIED: $currentUserEmail attempted bulk override on $classId", $currentUserEmail, 'High');
        jsonFail(403, 'You do not own this class.');
    }

    // Enrolled students resolved server-side from section data
    $targetSec = strtoupper(trim($class['section'] ?? ''));
    $uQuery = supabaseServiceQuery("/rest/v1/users?role=eq.student&select=full_name,student_number,program,section");
    $allStudents = ($uQuery['status'] === 200 && is_array($uQuery['data'])) ? $uQuery['data'] : [];
    $enrolled = array_filter($allStudents, function ($s) use ($targetSec) {
        $sSec = strtoupper(trim(($s['program'] ?? '') . ' ' . ($s['section'] ?? '')));
        return $sSec === $targetSec || strpos($sSec, $targetSec) !== false;
    });

    $sc = rawurlencode($sessionCode);
    $existingQuery = supabaseServiceQuery("/rest/v1/attendance_records?session_code=eq.$sc&select=student_number");
    $existingNums = [];
    if ($existingQuery['status'] === 200 && is_array($existingQuery['data'])) {
        foreach ($existingQuery['data'] as $row) $existingNums[$row['student_number']] = true;
    }

    $toInsert = [];
    foreach ($enrolled as $s) {
        $num = $s['student_number'] ?? '';
        if ($num === '' || isset($existingNums[$num])) continue;
        $toInsert[] = [
            'student_id' => $num,
            'student_name' => $s['full_name'],
            'student_number' => $num,
            'session_code' => $sessionCode,
            'check_in_at' => date('c'),
            'method' => 'bulk_override',
            'status' => 'present',
            'corrected_by' => $currentUserEmail,
            'remarks' => "Bulk marked present by Prof. $currentUserName"
        ];
    }

    $inserted = 0;
    if (!empty($toInsert)) {
        $res = supabaseServiceQuery("/rest/v1/attendance_records", 'POST', $toInsert, ["Prefer: return=minimal"]);
        if ($res['status'] >= 200 && $res['status'] < 300) $inserted = count($toInsert);
    }

    logSecurityEvent("ATTENDANCE_BULK: $inserted students marked present in $sessionCode by $currentUserEmail", $currentUserEmail, 'Medium');

    echo json_encode(['success' => true, 'inserted' => $inserted, 'message' => "$inserted students marked present."]);
    exit;
}

// ─── 11. POST: Create a class (faculty-owned) ──────────────────────────────────
if ($method === 'POST' && $action === 'create_class') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $record = [
        'code' => substr(trim($input['code'] ?? ''), 0, 32),
        'title' => substr(trim($input['title'] ?? ''), 0, 160),
        'section' => substr(trim($input['section'] ?? ''), 0, 64),
        'schedule_day' => substr(trim($input['schedule_day'] ?? 'TBA'), 0, 24),
        'start_time' => substr(trim($input['start_time'] ?? 'TBA'), 0, 16),
        'end_time' => substr(trim($input['end_time'] ?? 'TBA'), 0, 16),
        'instructor' => substr(trim($input['instructor'] ?? $currentUserName), 0, 120),
        'room' => substr(trim($input['room'] ?? 'Room TBA'), 0, 64),
        'units' => max(0, min(12, floatval($input['units'] ?? 3.0))),
        'created_by_name' => $currentUserName,
        'created_by_email' => $currentUserEmail
    ];

    if ($record['code'] === '' || $record['title'] === '') jsonFail(400, 'Subject code and title are required.');

    $res = supabaseServiceQuery("/rest/v1/classes", 'POST', [$record]);
    if ($res['status'] < 200 || $res['status'] >= 300) jsonFail(502, 'Database rejected the new class.');

    logSecurityEvent("CLASS_CREATED: {$record['code']} {$record['section']} by $currentUserEmail", $currentUserEmail, 'Low');
    echo json_encode(['success' => true, 'message' => 'Class created.']);
    exit;
}

// ─── 12. POST: Delete a class you own ──────────────────────────────────────────
if ($method === 'POST' && $action === 'delete_class') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);
    $id = trim($input['id'] ?? '');
    if (empty($id)) jsonFail(400, 'Class ID required.');

    $cQuery = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($id) . "&limit=1");
    $class = ($cQuery['status'] === 200 && !empty($cQuery['data'])) ? $cQuery['data'][0] : null;
    if (!$class) jsonFail(404, 'Class not found.');
    if (!facultyOwnsClass($class, $currentUserEmail, $isAdmin)) {
        logSecurityEvent("ACCESS_DENIED: $currentUserEmail tried deleting class $id", $currentUserEmail, 'High');
        jsonFail(403, 'You do not own this class.');
    }

    supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($id), 'DELETE');
    logSecurityEvent("CLASS_DELETED: {$class['code']} {$class['section']} by $currentUserEmail", $currentUserEmail, 'High');

    echo json_encode(['success' => true, 'message' => 'Class deleted.']);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
