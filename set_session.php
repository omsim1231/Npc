<?php
/**
 * set_session.php — Secure Server-Side Session Creator
 * 
 * ACCEPTS ONLY: { "access_token": "eyJ..." }
 * 
 * Flow:
 *  1. Receives the Supabase access_token from the browser
 *  2. Verifies it server-side via Supabase Auth API
 *  3. Looks up the user's role from the users table (service key)
 *  4. Creates a hardened PHP session with VERIFIED data only
 * 
 * NEVER trusts role, name, or user_id from the browser.
 */

require_once __DIR__ . '/supabase_helper.php';

// ─── Session Hardening ─────────────────────────────────────────────────────────
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

// Enable secure cookies when on HTTPS
if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') 
    || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
    ini_set('session.cookie_secure', '1');
}

session_start();

header('Content-Type: application/json');

// ─── Only accept POST ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// ─── Parse Request ─────────────────────────────────────────────────────────────
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['access_token']) || empty(trim($data['access_token']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Missing access token']);
    exit();
}

$accessToken = trim($data['access_token']);

// ─── Step 1: Verify Token Server-Side ──────────────────────────────────────────
$user = verifySupabaseToken($accessToken);

if (!$user) {
    http_response_code(401);
    logSecurityEvent('AUTH_FAIL: Invalid or expired token presented', '', 'Medium');
    echo json_encode(['success' => false, 'message' => 'Invalid or expired authentication token']);
    exit();
}

$email = strtolower(trim($user['email']));
$userId = $user['id'];

// ─── Step 2: Verify Email Domain ───────────────────────────────────────────────
if (!str_ends_with($email, '@navotaspolytechniccollege.edu.ph')) {
    http_response_code(403);
    logSecurityEvent("AUTH_DENIED: Non-NPC email attempted login: $email", $email, 'High');
    echo json_encode(['success' => false, 'message' => 'Only @navotaspolytechniccollege.edu.ph accounts are allowed']);
    exit();
}

// ─── Step 3: Get User Info (Server-Side Only) ──────────────────────────────────
$fullName = $user['user_metadata']['full_name'] 
    ?? $user['user_metadata']['name'] 
    ?? $email;

// Extract student number from email prefix
$emailPrefix = explode('@', $email)[0];
preg_match('/\d+/', $emailPrefix, $matches);
$studentNumber = !empty($matches) ? $matches[0] : 'N/A';

// ─── Step 4: Get Role from Database (NEVER from browser) ───────────────────────
$role = 'student'; // Safe default

// Check if this is a hardcoded admin email
if (isAdminEmail($email)) {
    $role = 'admin';
}

// Check the users table for an existing role (overrides default but not admin emails)
$dbRole = getUserRoleFromDB($email);
if ($dbRole !== 'student' || !isAdminEmail($email)) {
    // Use DB role if it's not the default, OR if they're not a hardcoded admin
    if (isAdminEmail($email)) {
        $role = 'admin'; // Admin emails always stay admin
    } else {
        $role = $dbRole;
    }
}

// ─── Step 5: Sync User to Database ─────────────────────────────────────────────
upsertUserRecord([
    'id' => $userId,
    'email' => $email,
    'full_name' => $fullName,
    'student_number' => $studentNumber,
    'role' => $role,
    'password_hash' => 'oauth'
]);

// ─── Step 6: Create Hardened PHP Session ────────────────────────────────────────
// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

$_SESSION['user_id'] = $userId;
$_SESSION['email'] = $email;
$_SESSION['name'] = $fullName;
$_SESSION['role'] = $role;
$_SESSION['student_number'] = $studentNumber;
$_SESSION['login_time'] = time();
$_SESSION['last_activity'] = time();
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

// Generate CSRF token for this session
getCsrfToken();

// ─── Step 7: Log Successful Login ──────────────────────────────────────────────
logSecurityEvent("LOGIN_SUCCESS: $email logged in as $role", $email, 'Low');

http_response_code(200);
echo json_encode([
    'success' => true,
    'role' => $role,
    'csrf_token' => $_SESSION['csrf_token']
]);
?>
