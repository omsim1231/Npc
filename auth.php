<?php
/**
 * auth.php — Session Guard & Authorization Middleware
 * 
 * Provides:
 *  - Hardened session configuration
 *  - Session expiry (8 hours max, 2 hours idle)
 *  - Role-based access control: require_login(), require_admin(), require_teacher()
 *  - CSRF helpers via supabase_helper.php
 */

require_once __DIR__ . '/supabase_helper.php';

// ─── Session Configuration ─────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
        ini_set('session.cookie_secure', '1');
    }

    session_start();
}

// ─── Session Expiry Constants ──────────────────────────────────────────────────
define('SESSION_MAX_LIFETIME', 8 * 60 * 60);  // 8 hours absolute max
define('SESSION_IDLE_TIMEOUT', 2 * 60 * 60);   // 2 hours idle timeout

// ─── Session Validity Check ────────────────────────────────────────────────────

function isSessionValid(): bool {
    if (!isset($_SESSION['user_id'])) {
        return false;
    }

    $now = time();

    // Gracefully handle existing sessions that didn't have login_time set yet
    if (!isset($_SESSION['login_time'])) {
        $_SESSION['login_time'] = $now;
        $_SESSION['last_activity'] = $now;
    }

    // Check absolute session lifetime
    if (($now - $_SESSION['login_time']) > SESSION_MAX_LIFETIME) {
        destroySession();
        return false;
    }

    // Check idle timeout
    if (isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity']) > SESSION_IDLE_TIMEOUT) {
        destroySession();
        return false;
    }

    // Update last activity
    $_SESSION['last_activity'] = $now;
    return true;
}

function destroySession(): void {
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    session_destroy();
}

// ─── Role Guards ───────────────────────────────────────────────────────────────

function require_login() {
    if (!isSessionValid()) {
        // Check if this is an API request (expects JSON)
        if (isApiRequest()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Session expired. Please log in again.']);
            exit();
        }
        header("Location: /login.php");
        exit();
    }
}

function require_admin() {
    require_login();
    $role = $_SESSION['role'] ?? 'student';
    if ($role !== 'admin') {
        logSecurityEvent(
            "ACCESS_DENIED: Non-admin tried to access admin page: " . ($_SESSION['email'] ?? 'unknown'),
            $_SESSION['email'] ?? '',
            'Medium'
        );
        // Students & teachers are bounced to their own portal with a notice flag
        $home = ($role === 'teacher') ? '/teacher.php' : '/index.php';
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Admin access required.']);
            exit();
        }
        header("Location: $home?denied=admin");
        exit();
    }
}

function require_teacher() {
    require_login();
    $role = $_SESSION['role'] ?? 'student';
    if ($role !== 'teacher' && $role !== 'admin') {
        logSecurityEvent(
            "ACCESS_DENIED: Non-teacher tried to access faculty page: " . ($_SESSION['email'] ?? 'unknown'),
            $_SESSION['email'] ?? '',
            'Medium'
        );
        if (isApiRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Faculty access required.']);
            exit();
        }
        header("Location: /index.php?denied=faculty");
        exit();
    }
}

/**
 * Require an exact role set for student-only areas.
 * Admins and teachers may still view student portals (support scenarios),
 * so this simply enforces authentication.
 */
function require_student_area() {
    require_login();
}

// ─── Helpers ───────────────────────────────────────────────────────────────────

function isApiRequest(): bool {
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    $isXhr = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    return $isXhr 
        || strpos($accept, 'application/json') !== false 
        || strpos($contentType, 'application/json') !== false;
}

/**
 * Get current session user info safely.
 */
function getCurrentUser(): array {
    return [
        'id' => $_SESSION['user_id'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'name' => $_SESSION['name'] ?? 'Guest',
        'role' => $_SESSION['role'] ?? 'student',
        'student_number' => $_SESSION['student_number'] ?? 'N/A'
    ];
}
?>
