<?php
/**
 * api_notifications.php — Lightweight announcements feed for the
 * universal notification bell (npc.js). Returns the latest published
 * announcements as JSON. Login required; no service key exposure.
 */
require_once __DIR__ . '/auth.php';
require_login();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$res = supabaseServiceQuery(
    "/rest/v1/announcements?status=eq.published&select=id,title,body,category,created_at&order=created_at.desc&limit=15"
);

$items = [];
if ($res['status'] === 200 && is_array($res['data'])) {
    foreach ($res['data'] as $a) {
        $body = trim(strip_tags((string)($a['body'] ?? '')));
        $body = preg_replace('/\*\*/', '', $body);
        $items[] = [
            'id'         => (string)($a['id'] ?? ''),
            'title'      => (string)($a['title'] ?? 'Announcement'),
            'excerpt'    => mb_substr($body, 0, 120),
            'category'   => (string)($a['category'] ?? 'news'),
            'created_at' => (string)($a['created_at'] ?? ''),
        ];
    }
}

echo json_encode(['success' => true, 'notifications' => $items]);
