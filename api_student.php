<?php
/**
 * api_student.php — Server-Side API for Student Portal Services
 * 
 * Handles:
 *  - My Grades (only approved & published)
 *  - Enrolled subjects & schedule
 *  - Attendance history & attendance rate % per subject
 *  - Notifications & mark read
 *  - Document requests (COR, COE, Good Moral, Transcript)
 *  - Profile update request workflow
 *  - Faculty consultation booking
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/supabase_helper.php';

require_login();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$currentUserEmail = strtolower($_SESSION['email'] ?? '');
$currentUserName = $_SESSION['name'] ?? 'Student';
$currentStudentNumber = $_SESSION['student_number'] ?? '';
$currentUserRole = $_SESSION['role'] ?? 'student';

// ─── 1. GET: Fetch Published Grades & Breakdown ────────────────────────────────
if ($method === 'GET' && $action === 'get_my_grades') {
    if (empty($currentStudentNumber)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Student number not found in session.']);
        exit;
    }

    // Query official `grades` table (published records)
    $gQuery = supabaseServiceQuery("/rest/v1/grades?student_number=eq." . urlencode($currentStudentNumber) . "&order=subject_code.asc");
    $grades = ($gQuery['status'] === 200 && is_array($gQuery['data'])) ? $gQuery['data'] : [];

    // Also fetch detailed period breakdown from student_grades where is_published = true
    $sgQuery = supabaseServiceQuery("/rest/v1/student_grades?student_number=eq." . urlencode($currentStudentNumber) . "&is_published=eq.true");
    $detailedGrades = ($sgQuery['status'] === 200 && is_array($sgQuery['data'])) ? $sgQuery['data'] : [];

    // Index detailed by class_id
    $detailsMap = [];
    foreach ($detailedGrades as $dg) {
        $detailsMap[$dg['class_id']] = $dg;
    }

    // Compute cumulative GPA
    $totalUnits = 0;
    $weightedSum = 0;
    $passedCount = 0;
    $failedCount = 0;
    $incCount = 0;

    foreach ($grades as &$g) {
        $units = floatval($g['units'] ?? 3.0);
        $gradeVal = floatval($g['grade'] ?? 0);
        $status = $g['status'] ?? 'Ongoing';

        if ($gradeVal > 0) {
            $totalUnits += $units;
            $weightedSum += ($units * $gradeVal);
            if ($gradeVal <= 3.00 && $gradeVal >= 1.00) $passedCount++;
            else if ($gradeVal > 3.00) $failedCount++;
        }
        if (strtoupper($status) === 'INC') $incCount++;
    }

    $gpa = $totalUnits > 0 ? round($weightedSum / $totalUnits, 2) : 0;

    echo json_encode([
        'success' => true,
        'grades' => $grades,
        'detailed' => $detailsMap,
        'summary' => [
            'gpa' => $gpa > 0 ? number_format($gpa, 2) : '—',
            'total_units' => $totalUnits,
            'passed' => $passedCount,
            'failed' => $failedCount,
            'inc' => $incCount,
            'term' => '1st Semester, 2026-2027'
        ]
    ]);
    exit;
}

// ─── 2. GET: Fetch Enrolled Classes & Schedules ────────────────────────────────
if ($method === 'GET' && $action === 'get_enrolled_schedule') {
    // Get user details
    $uQuery = supabaseServiceQuery("/rest/v1/users?email=eq." . urlencode($currentUserEmail) . "&limit=1");
    $user = ($uQuery['status'] === 200 && !empty($uQuery['data'])) ? $uQuery['data'][0] : null;

    $section = trim(($user['program'] ?? 'AIS') . ' ' . ($user['section'] ?? '2A'));

    // Fetch classes for this section
    $cQuery = supabaseServiceQuery("/rest/v1/classes?order=code.asc");
    $allClasses = ($cQuery['status'] === 200 && is_array($cQuery['data'])) ? $cQuery['data'] : [];

    $myClasses = array_filter($allClasses, function($c) use ($section) {
        $cSec = trim($c['section'] ?? 'AIS 2A');
        return str_contains(strtoupper($cSec), strtoupper($section)) 
            || str_contains(strtoupper($section), strtoupper($cSec));
    });

    echo json_encode([
        'success' => true,
        'section' => $section,
        'classes' => array_values($myClasses)
    ]);
    exit;
}

// ─── 3. GET: Attendance History & Subject Percentage ───────────────────────────
if ($method === 'GET' && $action === 'get_attendance_metrics') {
    $attQuery = supabaseServiceQuery("/rest/v1/attendance_records?student_number=eq." . urlencode($currentStudentNumber) . "&order=check_in_at.desc");
    $records = ($attQuery['status'] === 200 && is_array($attQuery['data'])) ? $attQuery['data'] : [];

    $presentCount = 0;
    $lateCount = 0;
    $absentCount = 0;

    foreach ($records as $r) {
        $st = strtolower($r['status'] ?? 'present');
        if ($st === 'present') $presentCount++;
        elseif ($st === 'late') $lateCount++;
        elseif ($st === 'absent') $absentCount++;
    }

    $totalScans = count($records);
    $rate = $totalScans > 0 ? round((($presentCount + ($lateCount * 0.8)) / $totalScans) * 100, 1) : 100.0;

    echo json_encode([
        'success' => true,
        'records' => $records,
        'stats' => [
            'rate' => $rate . '%',
            'total_checkins' => $totalScans,
            'present' => $presentCount,
            'late' => $lateCount,
            'absent' => $absentCount
        ]
    ]);
    exit;
}

// ─── 4. POST: Request Official Document ─────────────────────────────────────────
if ($method === 'POST' && $action === 'request_document') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $docType = trim($input['document_type'] ?? '');
    $purpose = trim($input['purpose'] ?? '');

    $allowedDocs = [
        'Certificate of Registration (COR)',
        'Certificate of Enrollment (COE)',
        'Certificate of Good Moral Character',
        'Official Transcript of Records (OTR)',
        'Certified True Copy of Grades / Grade Slip'
    ];

    if (empty($docType) || empty($purpose)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Document type and purpose are required.']);
        exit;
    }

    // Generate unique reference number: NPC-DOC-[YEAR]-[RANDOM6]
    $refNo = 'NPC-DOC-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

    $docReq = [
        'student_number' => $currentStudentNumber,
        'student_name' => $currentUserName,
        'student_email' => $currentUserEmail,
        'document_type' => $docType,
        'purpose' => $purpose,
        'reference_no' => $refNo,
        'status' => 'Pending'
    ];

    $res = supabaseServiceQuery("/rest/v1/document_requests", 'POST', [$docReq]);

    // Send confirmation notification
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
        'user_email' => $currentUserEmail,
        'title' => 'Document Request Submitted',
        'message' => "Your request for $docType (Ref: $refNo) has been received by the Registrar. Status: Pending.",
        'type' => 'document',
        'link_url' => 'academic.php'
    ]]);

    logSecurityEvent("DOC_REQUESTED: $docType (Ref: $refNo) by $currentUserEmail", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'reference_no' => $refNo,
        'message' => "Document request successfully submitted! Tracking Ref: $refNo"
    ]);
    exit;
}

// ─── 5. GET: Fetch Document Requests History ───────────────────────────────────
if ($method === 'GET' && $action === 'get_document_requests') {
    $dQuery = supabaseServiceQuery("/rest/v1/document_requests?student_number=eq." . urlencode($currentStudentNumber) . "&order=requested_at.desc");
    $requests = ($dQuery['status'] === 200 && is_array($dQuery['data'])) ? $dQuery['data'] : [];

    echo json_encode(['success' => true, 'requests' => $requests]);
    exit;
}

// ─── 6. POST: Request Profile Update (Sensitive Record Security) ────────────────
if ($method === 'POST' && $action === 'request_profile_update') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $changes = $input['requested_changes'] ?? [];
    $reason = trim($input['reason'] ?? '');

    if (empty($changes) || empty($reason)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Please provide the requested changes and reason.']);
        exit;
    }

    $reqData = [
        'student_number' => $currentStudentNumber,
        'student_name' => $currentUserName,
        'student_email' => $currentUserEmail,
        'requested_changes' => $changes,
        'reason' => $reason,
        'status' => 'Pending'
    ];

    supabaseServiceQuery("/rest/v1/profile_update_requests", 'POST', [$reqData]);

    // Notify admin
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
        'user_email' => 'admin@navotaspolytechniccollege.edu.ph',
        'title' => 'Profile Update Request',
        'message' => "Student $currentUserName ($currentStudentNumber) requested a profile change: $reason",
        'type' => 'system',
        'link_url' => 'admin_students.php'
    ]]);

    logSecurityEvent("PROFILE_UPDATE_REQUESTED: $currentStudentNumber by $currentUserEmail", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'message' => 'Profile update request submitted to Registrar for verification.'
    ]);
    exit;
}

// ─── 7. GET: Notifications List ────────────────────────────────────────────────
if ($method === 'GET' && $action === 'get_notifications') {
    $nQuery = supabaseServiceQuery("/rest/v1/notifications?user_email=eq." . urlencode($currentUserEmail) . "&order=created_at.desc&limit=20");
    $notifs = ($nQuery['status'] === 200 && is_array($nQuery['data'])) ? $nQuery['data'] : [];

    $unreadCount = count(array_filter($notifs, fn($n) => !($n['is_read'] ?? false)));

    echo json_encode([
        'success' => true,
        'notifications' => $notifs,
        'unread_count' => $unreadCount
    ]);
    exit;
}

// ─── 8. POST: Mark Notifications as Read ───────────────────────────────────────
if ($method === 'POST' && $action === 'mark_notifs_read') {
    requireCsrf();
    supabaseServiceQuery(
        "/rest/v1/notifications?user_email=eq." . urlencode($currentUserEmail),
        'PATCH',
        ['is_read' => true]
    );

    echo json_encode(['success' => true]);
    exit;
}

// ─── 9. POST: Book Faculty Consultation ────────────────────────────────────────
if ($method === 'POST' && $action === 'book_consultation') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $facultyEmail = strtolower(trim($input['faculty_email'] ?? ''));
    $facultyName = trim($input['faculty_name'] ?? 'Faculty');
    $subjectCode = trim($input['subject_code'] ?? '');
    $reqDate = trim($input['date'] ?? '');
    $reqTime = trim($input['time'] ?? '');
    $topic = trim($input['topic'] ?? '');

    if (empty($facultyEmail) || empty($reqDate) || empty($reqTime) || empty($topic)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All consultation booking fields are required.']);
        exit;
    }

    $apptData = [
        'faculty_email' => $facultyEmail,
        'faculty_name' => $facultyName,
        'student_number' => $currentStudentNumber,
        'student_name' => $currentUserName,
        'student_email' => $currentUserEmail,
        'subject_code' => $subjectCode,
        'requested_date' => $reqDate,
        'requested_time' => $reqTime,
        'topic' => $topic,
        'status' => 'Pending'
    ];

    supabaseServiceQuery("/rest/v1/consultation_appointments", 'POST', [$apptData]);

    // Notify faculty
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
        'user_email' => $facultyEmail,
        'title' => 'New Consultation Request',
        'message' => "$currentUserName ($currentStudentNumber) requested a consultation for $subjectCode on $reqDate at $reqTime: $topic",
        'type' => 'system',
        'link_url' => 'teacher.php'
    ]]);

    logSecurityEvent("CONSULTATION_BOOKED: Student $currentStudentNumber with $facultyEmail", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'message' => 'Consultation appointment request submitted to your professor!'
    ]);
    exit;
}

// ─── 10. POST: Secure QR / Manual Attendance Check-in ──────────────────────────
// Identity is taken from the SERVER session — never from the request body.
if ($method === 'POST' && $action === 'checkin_attendance') {
    requireCsrf();

    // Rate limit: max 10 verification attempts per minute per session
    $now = time();
    $attempts = array_values(array_filter($_SESSION['checkin_attempts'] ?? [], fn($t) => $t > $now - 60));
    if (count($attempts) >= 10) {
        http_response_code(429);
        echo json_encode(['success' => false, 'message' => 'Too many attempts. Please wait a minute and try again.']);
        exit;
    }
    $attempts[] = $now;
    $_SESSION['checkin_attempts'] = $attempts;

    if (empty($currentStudentNumber) || $currentStudentNumber === 'GUEST') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'No verified student identity in session.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $rawToken = trim($input['code'] ?? '');
    $checkMethod = ($input['method'] ?? 'manual') === 'qr_code' ? 'qr_code' : 'manual';

    // Normalize the code (mirrors legacy client logic)
    $code = strtoupper($rawToken);
    $parsed = json_decode($rawToken, true);
    if (is_array($parsed) && !empty($parsed['session'])) {
        $code = strtoupper(trim($parsed['session']));
    }
    $code = trim($code);

    if ($code === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Session code cannot be empty.']);
        exit;
    }

    // Strip rotating salt suffix (e.g. NPC-DM103-2026-08-21-3F8A -> NPC-DM103-2026-08-21)
    $segments = explode('-', $code);
    $baseCode = count($segments) > 5 ? implode('-', array_slice($segments, 0, 5)) : $code;

    foreach ([$code, $baseCode] as $candidate) {
        if (!preg_match('/^[A-Z0-9\-]{3,64}$/', $candidate)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Malformed session code.']);
            exit;
        }
    }

    // Look up the active faculty-created session
    $c1 = rawurlencode($code);
    $c2 = rawurlencode($baseCode);
    $sQuery = supabaseServiceQuery("/rest/v1/attendance_sessions?or=(session_code.eq.$c1,session_code.eq.$c2)&order=created_at.desc&limit=1");
    $session = ($sQuery['status'] === 200 && is_array($sQuery['data']) && !empty($sQuery['data'])) ? $sQuery['data'][0] : null;

    if (!$session) {
        logSecurityEvent("CHECKIN_REJECTED: Invalid code '$code' by $currentUserEmail", $currentUserEmail, 'Medium');
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => "Invalid session code: \"$code\" does not match any active attendance session."]);
        exit;
    }

    if (($session['is_active'] ?? true) === false) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => "The attendance session for {$session['class_code']} has been closed."]);
        exit;
    }

    // Time windows are evaluated with SERVER time — client clock irrelevant
    $nowTs = time();
    $computedStatus = 'present';
    if (!empty($session['present_until']) && $nowTs > strtotime($session['present_until'])) {
        $computedStatus = 'late';
    }
    if (!empty($session['late_until']) && $nowTs > strtotime($session['late_until'])) {
        http_response_code(410);
        echo json_encode(['success' => false, 'message' => "The check-in window for {$session['class_code']} has ended."]);
        exit;
    }

    // Duplicate check for this session
    $sn = rawurlencode($currentStudentNumber);
    $sc = rawurlencode($session['session_code']);
    $dupQuery = supabaseServiceQuery("/rest/v1/attendance_records?student_number=eq.$sn&session_code=eq.$sc&select=id,check_in_at,status&limit=1");
    if ($dupQuery['status'] === 200 && is_array($dupQuery['data']) && !empty($dupQuery['data'])) {
        $prev = $dupQuery['data'][0];
        echo json_encode([
            'success' => false,
            'duplicate' => true,
            'message' => 'You have already checked in for ' . ($session['class_code'] ?? 'this class') .
                         ' at ' . date('g:i:s A', strtotime($prev['check_in_at'])) . '.'
        ]);
        exit;
    }

    $insertRes = supabaseServiceQuery("/rest/v1/attendance_records", 'POST', [[
        'student_id' => $currentStudentNumber,
        'student_name' => $currentUserName,
        'student_number' => $currentStudentNumber,
        'session_code' => $session['session_code'],
        'check_in_at' => date('c'),
        'method' => $checkMethod,
        'status' => $computedStatus
    ]]);

    if ($insertRes['status'] < 200 || $insertRes['status'] >= 300) {
        http_response_code(502);
        echo json_encode(['success' => false, 'message' => 'Failed to record attendance. Please try again.']);
        exit;
    }

    logSecurityEvent("ATTENDANCE_CHECKIN: $currentStudentNumber -> {$session['session_code']} ($computedStatus)", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'status' => $computedStatus,
        'server_time' => date('c'),
        'session' => [
            'class_code' => $session['class_code'] ?? '',
            'section' => $session['section'] ?? '',
            'instructor' => $session['instructor'] ?? ''
        ]
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
