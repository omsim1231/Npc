<?php
/**
 * supabase_helper.php — Centralized Supabase Server-Side Helper
 * 
 * Provides:
 *  - .env loading (SUPABASE_URL, SUPABASE_KEY, SUPABASE_SERVICE_ROLE_KEY)
 *  - JWT token verification via Supabase Auth API
 *  - REST API queries using the service role key (bypasses RLS)
 *  - CSRF token generation and validation
 */

// ─── .env Loader ───────────────────────────────────────────────────────────────

function loadEnv(string $path = null): array {
    static $cache = null;
    if ($cache !== null) return $cache;

    $envFile = $path ?? __DIR__ . '/.env';
    $cache = [];

    if (!file_exists($envFile)) return $cache;

    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        if (strpos($line, '=') === false) continue;

        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        // Strip surrounding quotes
        if ((strlen($value) >= 2) && ($value[0] === '"' || $value[0] === "'")) {
            $value = substr($value, 1, -1);
        }
        $cache[$key] = $value;
        // Also set as environment variable for consistency
        putenv("$key=$value");
    }
    return $cache;
}

function getSupabaseUrl(): string {
    $env = loadEnv();
    return $env['SUPABASE_URL'] ?? '';
}

function getSupabaseAnonKey(): string {
    $env = loadEnv();
    return $env['SUPABASE_KEY'] ?? '';
}

function getSupabaseServiceKey(): string {
    $env = loadEnv();
    return $env['SUPABASE_SERVICE_ROLE_KEY'] ?? '';
}

// ─── JWT Token Verification ────────────────────────────────────────────────────

/**
 * Verify a Supabase access token server-side.
 * Calls GET /auth/v1/user with the token as Bearer.
 * Returns the user object on success, or null on failure.
 */
function verifySupabaseToken(string $accessToken): ?array {
    $url = getSupabaseUrl();
    $anonKey = getSupabaseAnonKey();

    if (empty($url) || empty($accessToken)) return null;

    $ch = curl_init("$url/auth/v1/user");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: $anonKey",
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) return null;

    $user = json_decode($response, true);
    if (!$user || !isset($user['id']) || !isset($user['email'])) return null;

    return $user;
}

// ─── Supabase REST API (Service Role) ──────────────────────────────────────────

/**
 * Query the Supabase REST API using the service role key (bypasses RLS).
 * 
 * @param string $endpoint  e.g. "/rest/v1/users?email=eq.test@example.com&select=role"
 * @param string $method    GET, POST, PATCH, DELETE
 * @param array|null $body  JSON body for POST/PATCH
 * @param array $extraHeaders Additional headers
 * @return array ['status' => int, 'data' => mixed]
 */
function supabaseServiceQuery(string $endpoint, string $method = 'GET', ?array $body = null, array $extraHeaders = []): array {
    $url = getSupabaseUrl();
    $serviceKey = getSupabaseServiceKey();

    if (empty($url) || empty($serviceKey)) {
        return ['status' => 0, 'data' => null, 'error' => 'Missing Supabase configuration'];
    }

    $fullUrl = $url . $endpoint;

    $headers = [
        "apikey: $serviceKey",
        "Authorization: Bearer $serviceKey",
        "Content-Type: application/json"
    ];
    $headers = array_merge($headers, $extraHeaders);

    $ch = curl_init($fullUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    if ($body !== null && in_array($method, ['POST', 'PATCH', 'PUT'])) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status' => $httpCode,
        'data' => json_decode($response, true),
        'raw' => $response
    ];
}

/**
 * Look up a user's role from the users table (using service key to bypass RLS).
 * Returns the role string or 'student' as default.
 */
function getUserRoleFromDB(string $email): string {
    $safeEmail = urlencode($email);
    $result = supabaseServiceQuery("/rest/v1/users?email=eq.$safeEmail&select=role&limit=1");

    if ($result['status'] === 200 && is_array($result['data']) && !empty($result['data'])) {
        $role = $result['data'][0]['role'] ?? 'student';
        // Validate role is one of the allowed values
        if (in_array($role, ['student', 'teacher', 'admin', 'faculty'])) {
            // Normalize 'faculty' to 'teacher'
            return $role === 'faculty' ? 'teacher' : $role;
        }
    }
    return 'student';
}

/**
 * Upsert a user record in the users table (using service key).
 */
function upsertUserRecord(array $userData): bool {
    $result = supabaseServiceQuery(
        "/rest/v1/users",
        'POST',
        $userData,
        ["Prefer: resolution=merge-duplicates"]
    );
    return $result['status'] >= 200 && $result['status'] < 300;
}

/**
 * Insert a security log entry.
 */
function logSecurityEvent(string $event, string $userEmail = '', string $severity = 'Low'): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

    // Only honor X-Forwarded-For when the direct peer is a trusted local proxy.
    // Otherwise clients could spoof their IP in the audit trail.
    $isLocalProxy = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false;
    if ($isLocalProxy && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $forwarded = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
        $forwarded = trim($forwarded);
        if (filter_var($forwarded, FILTER_VALIDATE_IP)) {
            $ip = $forwarded;
        }
    }

    supabaseServiceQuery("/rest/v1/security_logs", 'POST', [
        'event' => substr($event, 0, 500), // Limit length
        'user_email' => substr($userEmail, 0, 255),
        'ip_address' => $ip,
        'severity' => in_array($severity, ['Low', 'Medium', 'High']) ? $severity : 'Low'
    ], ["Prefer: return=minimal"]);
}

// ─── CSRF Protection ───────────────────────────────────────────────────────────

/**
 * Generate or retrieve the current CSRF token for this session.
 */
function getCsrfToken(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify a submitted CSRF token.
 * Returns true if valid, false otherwise.
 */
function verifyCsrfToken(?string $token): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($token) || empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Verify CSRF from the request (checks POST body and headers).
 * Sends 403 and exits on failure.
 */
function requireCsrf(): void {
    $token = $_POST['csrf_token']
        ?? $_SERVER['HTTP_X_CSRF_TOKEN']
        ?? null;

    // Also check JSON body
    if ($token === null) {
        $input = json_decode(file_get_contents('php://input'), true);
        $token = $input['csrf_token'] ?? null;
    }

    if (!verifyCsrfToken($token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid or missing CSRF token']);
        exit();
    }
}

// ─── Admin Email List ──────────────────────────────────────────────────────────

function getAdminEmails(): array {
    return [
        'admin@navotaspolytechniccollege.edu.ph',
        'jderramas251505@navotaspolytechniccollege.edu.ph'
    ];
}

function isAdminEmail(string $email): bool {
    return in_array(strtolower(trim($email)), getAdminEmails());
}

// ─── Config for JS injection ───────────────────────────────────────────────────

/**
 * Get config values safe for injection into JavaScript.
 * Only exposes the publishable (anon) key, NEVER the service key.
 */
function getJsConfig(): array {
    return [
        'url' => getSupabaseUrl(),
        'key' => getSupabaseAnonKey()
    ];
}
?>
