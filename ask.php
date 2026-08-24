<?php
/**
 * ask.php — AI Assistant Endpoint
 * 
 * Hardened to include:
 * - Session-based authentication
 * - Rate limiting (15 requests per minute)
 * - Strict CORS handling
 * - CSRF verification
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/supabase_helper.php';

// ─── 1. Authentication & CSRF ──────────────────────────────────────────────────
require_login();
requireCsrf();

header('Content-Type: application/json');
// Remove wildcard CORS. Only allow same-origin by not setting Access-Control-Allow-Origin, 
// or setting it strictly if absolutely needed.
// header('Access-Control-Allow-Origin: *'); <-- REMOVED

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['detail' => 'Method not allowed']);
    exit();
}

// ─── 2. Rate Limiting ──────────────────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) session_start();
$rateLimitMax = 15; // requests
$rateLimitWindow = 60; // seconds

if (!isset($_SESSION['ai_requests'])) {
    $_SESSION['ai_requests'] = [];
}

// Clean up old requests outside the window
$now = time();
$_SESSION['ai_requests'] = array_filter($_SESSION['ai_requests'], function($timestamp) use ($now, $rateLimitWindow) {
    return ($now - $timestamp) < $rateLimitWindow;
});

// Check limit
if (count($_SESSION['ai_requests']) >= $rateLimitMax) {
    http_response_code(429);
    logSecurityEvent("RATE_LIMIT_EXCEEDED: AI endpoint abused by {$_SESSION['email']}", $_SESSION['email'], 'Medium');
    echo json_encode([
        'answer' => "You are sending requests too quickly. Please wait a moment and try again.",
        'sources' => []
    ]);
    exit();
}

// Record this request
$_SESSION['ai_requests'][] = $now;

// ─── 3. Parse Request ──────────────────────────────────────────────────────────
$raw_input = file_get_contents('php://input');
$data = json_decode($raw_input, true);

$question = '';
$role = $_SESSION['role'] ?? 'student'; // Force role from session, don't trust client

if (is_array($data)) {
    $question = isset($data['question']) ? trim($data['question']) : (isset($data['prompt']) ? trim($data['prompt']) : '');
} elseif (isset($_POST['question'])) {
    $question = trim($_POST['question']);
}

if (empty($question)) {
    http_response_code(400);
    echo json_encode(['detail' => 'Question cannot be empty.']);
    exit();
}

// ─── 4. Execute AI Query ───────────────────────────────────────────────────────
// Call query_ai.py with the question
$py_script = __DIR__ . DIRECTORY_SEPARATOR . 'query_ai.py';
$input_json = json_encode(['question' => $question, 'role' => $role]);

$cmd = "python " . escapeshellarg($py_script) . " " . escapeshellarg($input_json);
$output = shell_exec($cmd);

if ($output) {
    $result = json_decode(trim($output), true);
    if ($result && isset($result['answer'])) {
        echo json_encode($result);
        exit();
    }
}

// Fallback if python execution fails
echo json_encode([
    'answer' => "Hello! Navotas Polytechnic College AI Assistant is ready to assist you regarding your course schedules, attendance verification, grade status, and campus guidelines.",
    'sources' => []
]);
?>
