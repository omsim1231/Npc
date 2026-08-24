<?php
/**
 * api_gradebook.php — Server-Side API for Faculty Grade Encoding & Admin Approval
 * 
 * Handles:
 *  - Fetching assigned classes & enrolled students
 *  - Saving draft grades per period / component
 *  - Computing raw, weighted, and transmuted grades (NPC 1.00 - 5.00 scale)
 *  - Submitting grade sheets for review
 *  - Admin reviewing, approving, locking, and publishing grades
 *  - Grade Change Requests (submission & review)
 *  - Audit logging of all grade activities
 */

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/supabase_helper.php';

require_login();
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

$currentUserEmail = strtolower($_SESSION['email'] ?? '');
$currentUserName = $_SESSION['name'] ?? 'Staff';
$currentUserRole = $_SESSION['role'] ?? 'student';
$isAdmin = ($currentUserRole === 'admin' || $currentUserRole === 'registrar');
$isTeacher = ($currentUserRole === 'teacher' || $currentUserRole === 'faculty' || $isAdmin);

// ─── HELPER: NPC Grade Transmutation Scale ──────────────────────────────────────
function transmuteToNpcGrade(float $percentage): array {
    if ($percentage >= 97.0) return ['grade' => 1.00, 'remark' => 'Passed', 'description' => 'Excellent'];
    if ($percentage >= 94.0) return ['grade' => 1.25, 'remark' => 'Passed', 'description' => 'Superior'];
    if ($percentage >= 91.0) return ['grade' => 1.50, 'remark' => 'Passed', 'description' => 'Very Good'];
    if ($percentage >= 88.0) return ['grade' => 1.75, 'remark' => 'Passed', 'description' => 'Good'];
    if ($percentage >= 85.0) return ['grade' => 2.00, 'remark' => 'Passed', 'description' => 'Meritorious'];
    if ($percentage >= 82.0) return ['grade' => 2.25, 'remark' => 'Passed', 'description' => 'Satisfactory'];
    if ($percentage >= 79.0) return ['grade' => 2.50, 'remark' => 'Passed', 'description' => 'Fair'];
    if ($percentage >= 76.0) return ['grade' => 2.75, 'remark' => 'Passed', 'description' => 'Passing'];
    if ($percentage >= 75.0) return ['grade' => 3.00, 'remark' => 'Passed', 'description' => 'Passing'];
    if ($percentage > 0)     return ['grade' => 5.00, 'remark' => 'Failed', 'description' => 'Failed'];
    return ['grade' => 0.00, 'remark' => 'INC', 'description' => 'Incomplete'];
}

// ─── 1. GET: Fetch Class Gradebook Details ──────────────────────────────────────
if ($method === 'GET' && $action === 'get_class_grades') {
    if (!$isTeacher) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Faculty or Admin access required.']);
        exit;
    }

    $classId = trim($_GET['class_id'] ?? '');
    if (empty($classId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'class_id is required.']);
        exit;
    }

    // 1. Verify Class Access (Faculty can only access assigned classes unless admin)
    $classQuery = supabaseServiceQuery("/rest/v1/classes?id=eq.$classId&limit=1");
    if ($classQuery['status'] !== 200 || empty($classQuery['data'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Class not found.']);
        exit;
    }

    $classInfo = $classQuery['data'][0];
    if (!$isAdmin) {
        $cEmail = strtolower(trim($classInfo['instructor_email'] ?? $classInfo['created_by_email'] ?? ''));
        $cInst = strtolower(trim($classInfo['instructor'] ?? ''));
        if ($cEmail !== $currentUserEmail && (!empty($cInst) && !str_contains($cInst, strtolower($currentUserName)))) {
            // Also check faculty_assignments table
            $faQuery = supabaseServiceQuery("/rest/v1/faculty_assignments?class_id=eq.$classId&faculty_email=eq." . urlencode($currentUserEmail) . "&limit=1");
            if ($faQuery['status'] !== 200 || empty($faQuery['data'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'You are not authorized to access this class gradebook.']);
                exit;
            }
        }
    }

    // 2. Fetch Submission & Lock Status
    $subQuery = supabaseServiceQuery("/rest/v1/grade_submissions?class_id=eq.$classId&order=created_at.desc&limit=1");
    $submission = ($subQuery['status'] === 200 && !empty($subQuery['data'])) ? $subQuery['data'][0] : null;

    // 3. Fetch Enrolled Students & Stored Grades
    $gradesQuery = supabaseServiceQuery("/rest/v1/student_grades?class_id=eq.$classId&order=student_number.asc");
    $grades = ($gradesQuery['status'] === 200 && is_array($gradesQuery['data'])) ? $gradesQuery['data'] : [];

    // 4. Fetch Enrolled Students from Users or Enrollments table
    $section = trim($classInfo['section'] ?? 'AIS 2A');
    $studentsQuery = supabaseServiceQuery("/rest/v1/users?role=eq.student&order=full_name.asc");
    $allStudents = ($studentsQuery['status'] === 200 && is_array($studentsQuery['data'])) ? $studentsQuery['data'] : [];

    // Filter students by section
    $sectionStudents = array_filter($allStudents, function($s) use ($section) {
        $sSec = trim(($s['program'] ?? '') . ' ' . ($s['section'] ?? ''));
        return str_contains(strtoupper($sSec), strtoupper($section)) 
            || str_contains(strtoupper($section), strtoupper($sSec))
            || str_contains(strtoupper($s['section'] ?? ''), strtoupper($section));
    });

    // 5. Fetch Grade Components / Default Weights
    $compQuery = supabaseServiceQuery("/rest/v1/grade_components?class_id=eq.$classId&order=component_name.asc");
    $components = ($compQuery['status'] === 200 && !empty($compQuery['data'])) ? $compQuery['data'] : [];

    echo json_encode([
        'success' => true,
        'class' => $classInfo,
        'submission' => $submission,
        'grades' => array_values($grades),
        'students' => array_values($sectionStudents),
        'components' => $components,
        'is_locked' => ($submission['lock_state'] ?? false) || ($submission['status'] ?? '') === 'Approved' || ($submission['status'] ?? '') === 'Published'
    ]);
    exit;
}

// ─── 2. POST: Save Draft Grades ────────────────────────────────────────────────
if ($method === 'POST' && $action === 'save_draft') {
    requireCsrf();
    if (!$isTeacher) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Faculty or Admin access required.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $classId = trim($input['class_id'] ?? '');
    $gradesData = $input['grades'] ?? [];

    if (empty($classId) || !is_array($gradesData)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid payload.']);
        exit;
    }

    // Check if class is locked
    $subQuery = supabaseServiceQuery("/rest/v1/grade_submissions?class_id=eq.$classId&limit=1");
    if ($subQuery['status'] === 200 && !empty($subQuery['data'])) {
        $sub = $subQuery['data'][0];
        if (($sub['lock_state'] ?? false) || in_array($sub['status'] ?? '', ['Approved', 'Published']) && !$isAdmin) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'This grade sheet is locked after approval. Please submit a Grade Change Request to modify.']);
            exit;
        }
    }

    // Prepare rows for upsert in student_grades
    $rowsToUpsert = [];
    foreach ($gradesData as $g) {
        $sNum = trim($g['student_number'] ?? '');
        if (empty($sNum)) continue;

        $prelim = floatval($g['prelim'] ?? 0);
        $midterm = floatval($g['midterm'] ?? 0);
        $prefinal = floatval($g['prefinal'] ?? 0);
        $final = floatval($g['final'] ?? 0);

        // Compute weighted / equivalent
        $pWeight = floatval($input['weight_prelim'] ?? 30) / 100;
        $mWeight = floatval($input['weight_midterm'] ?? 30) / 100;
        $fWeight = floatval($input['weight_final'] ?? 40) / 100;

        $computedRaw = 0;
        if ($prelim > 0 && $midterm > 0 && $final > 0) {
            $computedRaw = ($prelim * $pWeight) + ($midterm * $mWeight) + ($final * $fWeight);
        } elseif ($final > 0) {
            $computedRaw = $final;
        } elseif ($midterm > 0) {
            $computedRaw = ($prelim > 0) ? ($prelim * 0.5 + $midterm * 0.5) : $midterm;
        } else {
            $computedRaw = $prelim;
        }

        $trans = transmuteToNpcGrade($computedRaw);
        $customRemark = trim($g['remarks'] ?? '');
        $finalRemark = !empty($customRemark) ? $customRemark : $trans['remark'];

        $rowsToUpsert[] = [
            'class_id' => $classId,
            'student_number' => $sNum,
            'student_name' => trim($g['student_name'] ?? ''),
            'prelim' => $prelim,
            'midterm' => $midterm,
            'prefinal' => $prefinal,
            'final' => $final,
            'raw_grade' => round($computedRaw, 2),
            'weighted_grade' => round($computedRaw, 2),
            'equivalent_grade' => $trans['grade'],
            'final_rating' => $trans['grade'],
            'remarks' => $finalRemark,
            'is_locked' => false,
            'is_published' => false,
            'updated_at' => date('c')
        ];
    }

    if (empty($rowsToUpsert)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No valid student grade records to save.']);
        exit;
    }

    // Upsert into student_grades
    $upsertRes = supabaseServiceQuery(
        "/rest/v1/student_grades",
        'POST',
        $rowsToUpsert,
        ["Prefer: resolution=merge-duplicates"]
    );

    // Update or insert draft submission state
    supabaseServiceQuery(
        "/rest/v1/grade_submissions",
        'POST',
        [[
            'class_id' => $classId,
            'class_code' => trim($input['class_code'] ?? 'DM103'),
            'section' => trim($input['section'] ?? 'AIS 2A'),
            'faculty_email' => $currentUserEmail,
            'faculty_name' => $currentUserName,
            'grading_period' => 'Final',
            'status' => 'Draft',
            'lock_state' => false,
            'remarks' => 'Draft saved by faculty'
        ]],
        ["Prefer: resolution=merge-duplicates"]
    );

    // Audit log
    logSecurityEvent("GRADE_DRAFT_SAVED: " . count($rowsToUpsert) . " grades saved for class $classId by $currentUserEmail", $currentUserEmail, 'Low');

    echo json_encode([
        'success' => true,
        'message' => 'Draft grades saved successfully!',
        'count' => count($rowsToUpsert)
    ]);
    exit;
}

// ─── 3. POST: Submit Grade Sheet for Approval ──────────────────────────────────
if ($method === 'POST' && $action === 'submit_grades') {
    requireCsrf();
    if (!$isTeacher) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Faculty or Admin access required.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $classId = trim($input['class_id'] ?? '');

    if (empty($classId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'class_id is required.']);
        exit;
    }

    // Update submission status to 'Submitted'
    $subData = [
        'class_id' => $classId,
        'class_code' => trim($input['class_code'] ?? 'DM103'),
        'section' => trim($input['section'] ?? 'AIS 2A'),
        'faculty_email' => $currentUserEmail,
        'faculty_name' => $currentUserName,
        'grading_period' => 'Final',
        'status' => 'Submitted',
        'lock_state' => true, // Locked for faculty edits while under review
        'submitted_at' => date('c'),
        'remarks' => 'Submitted for Registrar/Admin review'
    ];

    $subRes = supabaseServiceQuery(
        "/rest/v1/grade_submissions",
        'POST',
        [$subData],
        ["Prefer: resolution=merge-duplicates"]
    );

    // Notify admins
    $adminNotif = [
        'user_email' => 'admin@navotaspolytechniccollege.edu.ph',
        'title' => 'New Grade Sheet Submitted',
        'message' => "$currentUserName has submitted the official grade sheet for {$input['class_code']} ({$input['section']}) for review.",
        'type' => 'grade',
        'link_url' => "admin_grades.php?class_id=$classId"
    ];
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [$adminNotif]);

    logSecurityEvent("GRADE_SUBMITTED: Class $classId submitted for approval by $currentUserEmail", $currentUserEmail, 'Medium');

    echo json_encode([
        'success' => true,
        'message' => 'Grade sheet successfully submitted to the Registrar/Admin for approval!'
    ]);
    exit;
}

// ─── 4. POST: Admin Approve, Lock & Publish Grades ──────────────────────────────
if ($method === 'POST' && $action === 'approve_and_publish') {
    requireCsrf();
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin or Registrar access required.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $classId = trim($input['class_id'] ?? '');

    if (empty($classId)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'class_id is required.']);
        exit;
    }

    // 1. Lock and set published in student_grades
    supabaseServiceQuery(
        "/rest/v1/student_grades?class_id=eq.$classId",
        'PATCH',
        [
            'is_locked' => true,
            'is_published' => true,
            'approved_at' => date('c'),
            'published_at' => date('c')
        ]
    );

    // 2. Also mirror into official `grades` table so student portal immediately displays them
    $sgQuery = supabaseServiceQuery("/rest/v1/student_grades?class_id=eq.$classId");
    $clQuery = supabaseServiceQuery("/rest/v1/classes?id=eq.$classId&limit=1");
    $classObj = ($clQuery['status'] === 200 && !empty($clQuery['data'])) ? $clQuery['data'][0] : null;

    if ($sgQuery['status'] === 200 && is_array($sgQuery['data']) && $classObj) {
        $gradesMirror = [];
        foreach ($sgQuery['data'] as $sg) {
            $gradesMirror[] = [
                'student_id' => $sg['student_number'],
                'student_number' => $sg['student_number'],
                'subject_code' => $classObj['code'],
                'description' => $classObj['title'],
                'units' => $classObj['units'] ?? 3.0,
                'grade' => $sg['equivalent_grade'],
                'status' => $sg['remarks'],
                'semester' => $classObj['semester'] ?? '1st Semester, 2026-2027'
            ];

            // Send notification to each enrolled student
            supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
                'user_email' => strtolower($sg['student_number']) . '@navotaspolytechniccollege.edu.ph',
                'title' => 'Official Grades Published',
                'message' => "Your final grade for {$classObj['code']} ({$classObj['title']}) has been approved and published: {$sg['equivalent_grade']} ({$sg['remarks']}).",
                'type' => 'grade',
                'link_url' => 'academic.php'
            ]], ["Prefer: return=minimal"]);
        }

        if (!empty($gradesMirror)) {
            supabaseServiceQuery(
                "/rest/v1/grades",
                'POST',
                $gradesMirror,
                ["Prefer: resolution=merge-duplicates"]
            );
        }
    }

    // 3. Update Grade Submission status
    supabaseServiceQuery(
        "/rest/v1/grade_submissions?class_id=eq.$classId",
        'PATCH',
        [
            'status' => 'Published',
            'lock_state' => true,
            'approved_at' => date('c'),
            'published_at' => date('c'),
            'reviewed_by' => $currentUserEmail,
            'reviewed_at' => date('c')
        ]
    );

    logSecurityEvent("GRADE_PUBLISHED: Class $classId approved and published to students by $currentUserEmail", $currentUserEmail, 'High');

    echo json_encode([
        'success' => true,
        'message' => 'Grades officially approved, locked, and published to students!'
    ]);
    exit;
}

// ─── 5. POST: Faculty Request Grade Change ──────────────────────────────────────
if ($method === 'POST' && $action === 'request_grade_change') {
    requireCsrf();
    if (!$isTeacher) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Faculty or Admin access required.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $classId = trim($input['class_id'] ?? '');
    $studentNumber = trim($input['student_number'] ?? '');
    $studentName = trim($input['student_name'] ?? '');
    $originalGrade = floatval($input['original_grade'] ?? 0);
    $proposedGrade = floatval($input['proposed_grade'] ?? 0);
    $reason = trim($input['reason'] ?? '');

    if (empty($classId) || empty($studentNumber) || empty($reason) || $proposedGrade <= 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'All fields including valid proposed grade and reason are required.']);
        exit;
    }

    $reqData = [
        'class_id' => $classId,
        'class_code' => trim($input['class_code'] ?? 'DM103'),
        'student_number' => $studentNumber,
        'student_name' => $studentName,
        'faculty_email' => $currentUserEmail,
        'faculty_name' => $currentUserName,
        'original_grade' => $originalGrade,
        'proposed_grade' => $proposedGrade,
        'reason' => $reason,
        'status' => 'Pending'
    ];

    $insertRes = supabaseServiceQuery("/rest/v1/grade_change_requests", 'POST', [$reqData]);

    // Notify admin
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
        'user_email' => 'admin@navotaspolytechniccollege.edu.ph',
        'title' => 'Grade Change Request Submitted',
        'message' => "Prof. $currentUserName requested a grade change for $studentName ($studentNumber) in {$input['class_code']}: $originalGrade -> $proposedGrade.",
        'type' => 'grade',
        'link_url' => 'admin_grades.php'
    ]]);

    logSecurityEvent("GRADE_CHANGE_REQUESTED: $studentNumber ($originalGrade -> $proposedGrade) by $currentUserEmail. Reason: $reason", $currentUserEmail, 'Medium');

    echo json_encode([
        'success' => true,
        'message' => 'Grade change request submitted successfully to the Registrar for review.'
    ]);
    exit;
}

// ─── 6. POST: Admin Review Grade Change Request ─────────────────────────────────
if ($method === 'POST' && $action === 'review_grade_change') {
    requireCsrf();
    if (!$isAdmin) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Admin or Registrar access required.']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $requestId = trim($input['request_id'] ?? '');
    $decision = trim($input['decision'] ?? ''); // 'Approved' | 'Rejected'
    $adminRemarks = trim($input['remarks'] ?? '');

    if (empty($requestId) || !in_array($decision, ['Approved', 'Rejected'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid request ID or decision.']);
        exit;
    }

    // Fetch request
    $gcrQuery = supabaseServiceQuery("/rest/v1/grade_change_requests?id=eq.$requestId&limit=1");
    if ($gcrQuery['status'] !== 200 || empty($gcrQuery['data'])) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Grade change request not found.']);
        exit;
    }

    $req = $gcrQuery['data'][0];

    // If Approved, update student_grades & grades table
    if ($decision === 'Approved') {
        $trans = transmuteToNpcGrade($req['proposed_grade']);
        
        // Update student_grades
        supabaseServiceQuery(
            "/rest/v1/student_grades?class_id=eq.{$req['class_id']}&student_number=eq.{$req['student_number']}",
            'PATCH',
            [
                'equivalent_grade' => $req['proposed_grade'],
                'final_rating' => $req['proposed_grade'],
                'remarks' => $trans['remark'],
                'updated_at' => date('c')
            ]
        );

        // Update grades table
        supabaseServiceQuery(
            "/rest/v1/grades?student_number=eq.{$req['student_number']}&subject_code=eq.{$req['class_code']}",
            'PATCH',
            [
                'grade' => $req['proposed_grade'],
                'status' => $trans['remark']
            ]
        );
    }

    // Update request status
    supabaseServiceQuery(
        "/rest/v1/grade_change_requests?id=eq.$requestId",
        'PATCH',
        [
            'status' => $decision,
            'reviewed_by' => $currentUserEmail,
            'reviewed_at' => date('c'),
            'admin_remarks' => $adminRemarks
        ]
    );

    // Notify faculty
    supabaseServiceQuery("/rest/v1/notifications", 'POST', [[
        'user_email' => $req['faculty_email'],
        'title' => "Grade Change Request $decision",
        'message' => "Your grade change request for {$req['student_name']} in {$req['class_code']} was $decision by the Registrar." . (!empty($adminRemarks) ? " Remarks: $adminRemarks" : ''),
        'type' => 'grade',
        'link_url' => "teacher_grades.php?class_id={$req['class_id']}"
    ]]);

    logSecurityEvent("GRADE_CHANGE_REVIEWED: Request $requestId $decision by $currentUserEmail", $currentUserEmail, 'High');

    echo json_encode([
        'success' => true,
        'message' => "Grade change request has been $decision."
    ]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Invalid or missing action.']);
