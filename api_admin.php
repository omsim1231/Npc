<?php
/**
 * api_admin.php — Server-Side API for Admin & Registrar Operations
 * 
 * Handles:
 *  - Dashboard metric aggregations
 *  - Academic calendar (School Years, Semesters, Programs, Sections, Subjects)
 *  - Document requests approval & status updates
 *  - Profile update request verification
 *  - Role management
 *  - Audit log queries & filters
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/supabase_helper.php';

require_admin();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$currentUserEmail = strtolower($_SESSION['email'] ?? '');
$currentUserName = $_SESSION['name'] ?? 'Administrator';

// ─── 1. GET: Admin Dashboard Metrics ───────────────────────────────────────────
if ($method === 'GET' && $action === 'get_dashboard_metrics') {
    // 1. Total Students
    $sQuery = supabaseServiceQuery("/rest/v1/users?role=eq.student&select=id");
    $totalStudents = ($sQuery['status'] === 200 && is_array($sQuery['data'])) ? count($sQuery['data']) : 0;

    // 2. Total Faculty
    $fQuery = supabaseServiceQuery("/rest/v1/users?or=(role.eq.teacher,role.eq.faculty)&select=id");
    $totalFaculty = ($fQuery['status'] === 200 && is_array($fQuery['data'])) ? count($fQuery['data']) : 0;

    // 3. Active Classes
    $cQuery = supabaseServiceQuery("/rest/v1/classes?select=id");
    $totalClasses = ($cQuery['status'] === 200 && is_array($cQuery['data'])) ? count($cQuery['data']) : 0;

    // 4. Pending Document Requests
    $dQuery = supabaseServiceQuery("/rest/v1/document_requests?status=eq.Pending&select=id");
    $pendingDocs = ($dQuery['status'] === 200 && is_array($dQuery['data'])) ? count($dQuery['data']) : 0;

    // 5. Pending Grade Approvals
    $gQuery = supabaseServiceQuery("/rest/v1/grade_submissions?status=eq.Submitted&select=id");
    $pendingGrades = ($gQuery['status'] === 200 && is_array($gQuery['data'])) ? count($gQuery['data']) : 0;

    // 6. Pending Grade Change Requests
    $gcrQuery = supabaseServiceQuery("/rest/v1/grade_change_requests?status=eq.Pending&select=id");
    $pendingGradeChanges = ($gcrQuery['status'] === 200 && is_array($gcrQuery['data'])) ? count($gcrQuery['data']) : 0;

    // 7. Recent Audit Logs
    $aQuery = supabaseServiceQuery("/rest/v1/security_logs?order=created_at.desc&limit=8");
    $recentLogs = ($aQuery['status'] === 200 && is_array($aQuery['data'])) ? $aQuery['data'] : [];

    echo json_encode([
        'success' => true,
        'metrics' => [
            'total_students' => $totalStudents,
            'total_faculty' => $totalFaculty,
            'active_classes' => $totalClasses,
            'pending_docs' => $pendingDocs,
            'pending_grades' => $pendingGrades,
            'pending_grade_changes' => $pendingGradeChanges
        ],
        'recent_logs' => $recentLogs
    ]);
    exit;
}

// ─── 2. GET: List All Document Requests (with filter) ───────────────────────────
if ($method === 'GET' && $action === 'get_all_document_requests') {
    $statusFilter = trim($_GET['status'] ?? '');
    $endpoint = "/rest/v1/document_requests?order=requested_at.desc";
    if (!empty($statusFilter)) {
        $endpoint = "/rest/v1/document_requests?status=eq.$statusFilter&order=requested_at.desc";
    }

    $dQuery = supabaseServiceQuery($endpoint);
    $requests = ($dQuery['status'] === 200 && is_array($dQuery['data'])) ? $dQuery['data'] : [];

    echo json_encode(['success' => true, 'requests' => $requests]);
    exit;
}

// ─── 3. POST: Process Document Request (Status Update) ─────────────────────────
if ($method === 'POST' && $action === 'process_document_request') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $id = trim($input['id'] ?? '');
    $status = trim($input['status'] ?? ''); // 'Processing', 'Ready for Pickup', 'Released', 'Rejected'
    $remarks = trim($input['remarks'] ?? '');

    if (empty($id) || empty($status)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Request ID and new status are required.']);
        exit;
    }

    // Fetch request to get student details
    $rQuery = supabaseServiceQuery("/rest/v1/document_requests?id=eq.$id&limit=1");
    if ($rQuery['status'] === 200 && !empty($rQuery['data'])) {
        $req = $rQuery['data'][0];

        supabaseServiceQuery(
            "/rest/v1/document_requests?id=eq.$id",
            'PATCH',
            [
                'status' => $status,
                'remarks' => $remarks,
                'processed_by' => $currentUserEmail,
                'updated_at' => date('c')
            ]
        );

        // Notify student
        supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
            'user_email' => $req['student_email'],
            'title' => "Document Request Status: $status",
            'message' => "Your request for {$req['document_type']} (Ref: {$req['reference_no']}) has been updated to '$status'." . (!empty($remarks) ? " Note: $remarks" : ''),
            'type' => 'document',
            'link_url' => 'academic.php'
        ]]);

        logSecurityEvent("DOC_PROCESSED: {$req['reference_no']} marked as $status by $currentUserEmail", $currentUserEmail, 'Low');
    }

    echo json_encode(['success' => true, 'message' => "Document request updated to $status."]);
    exit;
}

// ─── 4. GET: List All Grade Submissions & Approvals Queue ──────────────────────
if ($method === 'GET' && $action === 'get_grade_approval_queue') {
    $sQuery = supabaseServiceQuery("/rest/v1/grade_submissions?order=submitted_at.desc");
    $submissions = ($sQuery['status'] === 200 && is_array($sQuery['data'])) ? $sQuery['data'] : [];

    // Also fetch pending grade change requests
    $gcrQuery = supabaseServiceQuery("/rest/v1/grade_change_requests?order=requested_at.desc");
    $changeRequests = ($gcrQuery['status'] === 200 && is_array($gcrQuery['data'])) ? $gcrQuery['data'] : [];

    echo json_encode([
        'success' => true,
        'submissions' => $submissions,
        'change_requests' => $changeRequests
    ]);
    exit;
}

// ─── 5. POST: Update User Role (Role Management) ───────────────────────────────
if ($method === 'POST' && $action === 'update_user_role') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $targetEmail = strtolower(trim($input['email'] ?? ''));
    $newRole = trim($input['role'] ?? '');

    $allowedRoles = ['student', 'teacher', 'admin', 'registrar'];

    if (empty($targetEmail) || !in_array($newRole, $allowedRoles)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Valid email and role required.']);
        exit;
    }

    // Update in users table
    $upRes = supabaseServiceQuery(
        "/rest/v1/users?email=eq." . urlencode($targetEmail),
        'PATCH',
        ['role' => $newRole]
    );

    logSecurityEvent("ROLE_CHANGED: $targetEmail updated to $newRole by $currentUserEmail", $currentUserEmail, 'High');

    echo json_encode(['success' => true, 'message' => "User role for $targetEmail updated to $newRole."]);
    exit;
}

// ─── 6. GET: Security & System Audit Logs ──────────────────────────────────────
if ($method === 'GET' && $action === 'get_audit_logs') {
    $limit = intval($_GET['limit'] ?? 50);
    $severity = trim($_GET['severity'] ?? '');

    $endpoint = "/rest/v1/security_logs?order=created_at.desc&limit=$limit";
    if (!empty($severity)) {
        $endpoint = "/rest/v1/security_logs?severity=eq.$severity&order=created_at.desc&limit=$limit";
    }

    $lQuery = supabaseServiceQuery($endpoint);
    $logs = ($lQuery['status'] === 200 && is_array($lQuery['data'])) ? $lQuery['data'] : [];

    echo json_encode(['success' => true, 'logs' => $logs]);
    exit;
}

// ─── 7. POST: Create or update a class/schedule offering ───────────────────────
if ($method === 'POST' && $action === 'save_class') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $record = [
        'code' => substr(trim($input['code'] ?? ''), 0, 32),
        'title' => substr(trim($input['title'] ?? ''), 0, 160),
        'section' => substr(trim($input['section'] ?? ''), 0, 64),
        'schedule_day' => substr(trim($input['schedule_day'] ?? 'TBA'), 0, 24),
        'start_time' => substr(trim($input['start_time'] ?? 'TBA'), 0, 16),
        'end_time' => substr(trim($input['end_time'] ?? 'TBA'), 0, 16),
        'instructor' => substr(trim($input['instructor'] ?? 'TBA'), 0, 120),
        'room' => substr(trim($input['room'] ?? 'Room TBA'), 0, 64),
        'units' => max(0, min(12, floatval($input['units'] ?? 3.0)))
    ];

    if ($record['code'] === '' || $record['title'] === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Subject code and title are required.']);
        exit;
    }

    $id = trim($input['id'] ?? '');
    if ($id !== '') {
        // Preserve original creator metadata on updates
        $result = supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($id), 'PATCH', $record);
    } else {
        $email = trim($input['created_by_email'] ?? '') ?: 'admin@navotaspolytechniccollege.edu.ph';
        $name = trim($input['created_by_name'] ?? '') ?: $currentUserName;
        $record['created_by_email'] = filter_var($email, FILTER_VALIDATE_EMAIL) ? strtolower($email) : 'admin@navotaspolytechniccollege.edu.ph';
        $record['created_by_name'] = substr($name, 0, 120);
        $result = supabaseServiceQuery("/rest/v1/classes", 'POST', [$record]);
    }

    if ($result['status'] < 200 || $result['status'] >= 300) {
        echo json_encode(['success' => false, 'message' => 'Database rejected the change.', 'detail' => $result['data']]);
        exit;
    }

    logSecurityEvent("CLASS_SAVED: {$record['code']} {$record['section']} by $currentUserEmail", $currentUserEmail, 'Low');
    echo json_encode(['success' => true, 'message' => 'Schedule saved successfully.']);
    exit;
}

// ─── 8. POST: Delete a class/schedule offering ─────────────────────────────────
if ($method === 'POST' && $action === 'delete_class') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);
    $id = trim($input['id'] ?? '');
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Class ID required.']);
        exit;
    }
    supabaseServiceQuery("/rest/v1/classes?id=eq." . rawurlencode($id), 'DELETE');
    logSecurityEvent("CLASS_DELETED: $id by $currentUserEmail", $currentUserEmail, 'High');
    echo json_encode(['success' => true, 'message' => 'Deleted.']);
    exit;
}

// ─── 9. POST: Create a single student record ───────────────────────────────────
if ($method === 'POST' && $action === 'create_student') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $name = substr(trim($input['full_name'] ?? ''), 0, 120);
    $email = strtolower(trim($input['email'] ?? ''));
    $number = substr(trim($input['student_number'] ?? 'N/A'), 0, 24);
    $program = substr(trim($input['program'] ?? 'BSIS'), 0, 32);
    $section = substr(trim($input['section'] ?? '1A'), 0, 16);

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Valid name and email are required.']);
        exit;
    }
    if (!str_ends_with($email, '@navotaspolytechniccollege.edu.ph')) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Email must be an official @navotaspolytechniccollege.edu.ph address.']);
        exit;
    }

    $res = supabaseServiceQuery("/rest/v1/users", 'POST', [[
        'full_name' => $name,
        'email' => $email,
        'student_number' => $number !== '' ? $number : 'N/A',
        'program' => $program,
        'section' => $section,
        'role' => 'student',
        'password_hash' => 'oauth'
    ]]);

    if ($res['status'] < 200 || $res['status'] >= 300) {
        echo json_encode(['success' => false, 'message' => 'Insert failed (email may already exist).']);
        exit;
    }

    logSecurityEvent("STUDENT_CREATED: $email ($program-$section) by $currentUserEmail", $currentUserEmail, 'Low');
    echo json_encode(['success' => true, 'message' => "Student $name added."]);
    exit;
}

// ─── 10. POST: Delete a student record ─────────────────────────────────────────
if ($method === 'POST' && $action === 'delete_student') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);
    $id = trim($input['id'] ?? '');
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Student ID required.']);
        exit;
    }

    // Guard: only student-role rows may be removed here
    $uQuery = supabaseServiceQuery("/rest/v1/users?id=eq." . rawurlencode($id) . "&select=id,email,role&limit=1");
    $user = ($uQuery['status'] === 200 && !empty($uQuery['data'])) ? $uQuery['data'][0] : null;
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }
    if (($user['role'] ?? '') !== 'student') {
        logSecurityEvent("ACCESS_DENIED: Attempt to delete non-student account {$user['email']} by $currentUserEmail", $currentUserEmail, 'High');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Only student accounts can be deleted here. Faculty/admin accounts require Supabase Auth removal.']);
        exit;
    }

    supabaseServiceQuery("/rest/v1/users?id=eq." . rawurlencode($id), 'DELETE');
    logSecurityEvent("STUDENT_DELETED: {$user['email']} by $currentUserEmail", $currentUserEmail, 'High');
    echo json_encode(['success' => true, 'message' => 'Student removed.']);
    exit;
}

// ─── 11. POST: Publish/save an announcement ────────────────────────────────────
if ($method === 'POST' && $action === 'save_announcement') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $title = substr(trim(strip_tags($input['title'] ?? '')), 0, 200);
    $body = trim($input['body'] ?? ''); // Rich HTML body — sanitized on output client-side
    $category = in_array($input['category'] ?? '', ['news', 'academic', 'emergency']) ? $input['category'] : 'news';
    $status = in_array($input['status'] ?? '', ['published', 'draft']) ? $input['status'] : 'draft';
    $department = substr(trim($input['department'] ?? 'all'), 0, 64);

    if ($title === '' || $body === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Title and body content are required.']);
        exit;
    }

    $res = supabaseServiceQuery("/rest/v1/announcements", 'POST', [[
        'title' => $title,
        'body' => $body,
        'category' => $category,
        'status' => $status,
        'department' => $department,
        'audience' => ['students', 'faculty']
    ]]);

    if ($res['status'] < 200 || $res['status'] >= 300) {
        echo json_encode(['success' => false, 'message' => 'Database rejected the announcement.']);
        exit;
    }

    logSecurityEvent("ANNOUNCEMENT_SAVED: '$title' ($status) by $currentUserEmail", $currentUserEmail, 'Low');
    echo json_encode(['success' => true, 'message' => $status === 'published' ? 'Announcement published!' : 'Draft saved!']);
    exit;
}

// ─── 12. POST: Delete an announcement ──────────────────────────────────────────
if ($method === 'POST' && $action === 'delete_announcement') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);
    $id = trim($input['id'] ?? '');
    if (empty($id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Announcement ID required.']);
        exit;
    }
    supabaseServiceQuery("/rest/v1/announcements?id=eq." . rawurlencode($id), 'DELETE');
    logSecurityEvent("ANNOUNCEMENT_DELETED: $id by $currentUserEmail", $currentUserEmail, 'Medium');
    echo json_encode(['success' => true, 'message' => 'Announcement deleted.']);
    exit;
}

// ─── 13. POST: Delete attendance records (dedupe / clear session) ──────────────
// Accepts either explicit ids[] (max 500) or {scope:"all"} to wipe every record.
if ($method === 'POST' && $action === 'delete_attendance_records') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $ids = array_slice(array_filter(array_map('trim', (array)($input['ids'] ?? []))), 0, 500);
    $wipeAll = ($input['scope'] ?? '') === 'all';

    if (!$wipeAll && empty($ids)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Provide record ids[] or scope=all.']);
        exit;
    }

    foreach (array_chunk($ids, 100) as $chunk) {
        $in = implode(',', array_map('rawurlencode', $chunk));
        supabaseServiceQuery("/rest/v1/attendance_records?id=in.($in)", 'DELETE');
    }
    if ($wipeAll) {
        supabaseServiceQuery("/rest/v1/attendance_records?id=neq.00000000-0000-0000-0000-000000000000", 'DELETE');
    }

    logSecurityEvent(
        $wipeAll ? "ATTENDANCE_WIPED: all records cleared by $currentUserEmail" : "ATTENDANCE_DEDUPE: " . count($ids) . " duplicate records removed by $currentUserEmail",
        $currentUserEmail,
        'High'
    );
    echo json_encode(['success' => true, 'deleted' => $wipeAll ? 'all' : count($ids)]);
    exit;
}

// ─── 13b. GET: Paginated audit logs for the audit viewer ───────────────────────
if ($method === 'GET' && $action === 'get_audit_logs') {
    $limit  = min(500, max(1, (int)($_GET['limit'] ?? 500)));
    $offset = max(0, (int)($_GET['offset'] ?? 0));
    $endpoint = "/rest/v1/security_logs?select=id,event,user_email,ip_address,severity,created_at"
              . "&order=created_at.desc&limit={$limit}&offset={$offset}";
    $res = supabaseServiceQuery($endpoint);
    $logs = ($res['status'] === 200 && is_array($res['data'])) ? $res['data'] : [];
    echo json_encode(['success' => true, 'logs' => $logs]);
    exit;
}

// ─── 14. GET: List all user accounts (students, faculty, admins) ───────────────
if ($method === 'GET' && $action === 'list_users') {
    $roleFilter = trim($_GET['role'] ?? '');
    $endpoint = "/rest/v1/users?select=id,full_name,email,student_number,program,section,role,created_at&order=created_at.desc&limit=500";
    if (in_array($roleFilter, ['student', 'teacher', 'admin'])) {
        $endpoint = "/rest/v1/users?role=eq.$roleFilter&select=id,full_name,email,student_number,program,section,role,created_at&order=created_at.desc&limit=500";
    }
    $res = supabaseServiceQuery($endpoint);
    $users = ($res['status'] === 200 && is_array($res['data'])) ? $res['data'] : [];
    echo json_encode(['success' => true, 'users' => $users]);
    exit;
}

// ─── 15. POST: Create a new admin / teacher / student account ──────────────────
// Admin & teacher accounts are provisioned in Supabase Auth by IT via invite;
// the users-table row created here gives them portal access the moment they
// sign in with that NPC Gmail (set_session.php trusts this table's role).
if ($method === 'POST' && $action === 'create_user') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $name  = substr(trim(strip_tags($input['full_name'] ?? '')), 0, 120);
    $email = strtolower(substr(trim($input['email'] ?? ''), 0, 160));
    $role  = trim($input['role'] ?? 'student');
    $number = strtoupper(substr(trim(strip_tags($input['number'] ?? '')), 0, 32));
    $program = substr(trim(strip_tags($input['program'] ?? '')), 0, 64);
    $section = substr(trim(strip_tags($input['section'] ?? '')), 0, 32);
    $sendInvite = !empty($input['send_invite']);

    // ── Validation ──
    if ($name === '' || $email === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Full name and email are required.']);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)
        || !str_ends_with($email, '@navotaspolytechniccollege.edu.ph')) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Email must be an official @navotaspolytechniccollege.edu.ph address.']);
        exit;
    }
    if (!in_array($role, ['student', 'teacher', 'admin'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid role selected.']);
        exit;
    }

    // Guard: hardcoded root admins cannot be overwritten here
    if (isAdminEmail($email) && $role !== 'admin') {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'This email is a protected system administrator and cannot be demoted.']);
        exit;
    }

    // Duplicate check
    $safeEmail = rawurlencode($email);
    $dupe = supabaseServiceQuery("/rest/v1/users?email=eq.$safeEmail&select=id,email&limit=1");
    if ($dupe['status'] === 200 && !empty($dupe['data'])) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'An account with this NPC Gmail already exists.']);
        exit;
    }

    $row = [
        'full_name'      => $name,
        'email'          => $email,
        'student_number' => $number !== '' ? $number : 'N/A',
        'role'           => $role,
        'password_hash'  => 'oauth'
    ];
    if ($role === 'student') {
        $row['program'] = $program !== '' ? $program : 'TBA';
        $row['section'] = $section !== '' ? $section : 'TBA';
    } else {
        // Department field doubles as program for employees
        $row['program'] = $program !== '' ? $program : 'Faculty';
    }

    // Optional: send Supabase Auth invite so the account exists for SSO
    $inviteNote = '';
    if ($sendInvite) {
        $inv = supabaseServiceQuery('/auth/v1/admin/generate_link', 'POST', [
            'type'          => 'signup',
            'email'         => $email,
            'options'       => ['redirect_to' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/auth_callback.php']
        ]);
        if ($inv['status'] >= 200 && $inv['status'] < 300) {
            $inviteNote = ' Invite email sent.';
        }
    }

    $res = supabaseServiceQuery("/rest/v1/users", 'POST', [ $row ]);
    if ($res['status'] < 200 || $res['status'] >= 300) {
        echo json_encode(['success' => false, 'message' => 'Database rejected the new account (email may already exist).']);
        exit;
    }

    logSecurityEvent("USER_CREATED: $role account '$email' created by $currentUserEmail", $currentUserEmail, 'Medium');
    echo json_encode(['success' => true, 'message' => ucfirst($role) . " account for $name added." . $inviteNote]);
    exit;
}

// ─── 16. POST: Change a user's role (promote/demote) ───────────────────────────
if ($method === 'POST' && $action === 'set_user_role') {
    requireCsrf();
    $input = json_decode(file_get_contents('php://input'), true);

    $id = trim($input['id'] ?? '');
    $newRole = trim($input['role'] ?? '');

    if (empty($id) || !in_array($newRole, ['student', 'teacher', 'admin'], true)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'User ID and a valid role are required.']);
        exit;
    }

    $uQuery = supabaseServiceQuery("/rest/v1/users?id=eq." . rawurlencode($id) . "&select=id,email,role&limit=1");
    $user = ($uQuery['status'] === 200 && !empty($uQuery['data'])) ? $uQuery['data'][0] : null;
    if (!$user) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }

    // Protected system admins can never be demoted from the UI
    if (isAdminEmail($user['email'] ?? '')) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Protected system administrator — role cannot be changed here.']);
        exit;
    }

    // Safety: never demote yourself (avoids lock-out)
    if (strcasecmp($user['email'] ?? '', $currentUserEmail) === 0 && $newRole !== 'admin') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'You cannot demote your own account.']);
        exit;
    }

    $res = supabaseServiceQuery("/rest/v1/users?id=eq." . rawurlencode($id), 'PATCH', ['role' => $newRole]);
    if ($res['status'] < 200 || $res['status'] >= 300) {
        echo json_encode(['success' => false, 'message' => 'Database rejected the role change.']);
        exit;
    }

    logSecurityEvent("ROLE_CHANGED: {$user['email']} → $newRole by $currentUserEmail", $currentUserEmail, 'High');
    echo json_encode(['success' => true, 'message' => "{$user['email']} is now a $newRole."]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid action.']);
