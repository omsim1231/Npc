<?php
/**
 * _sidebar.php — ONE sidebar for every portal page (admin / faculty / student).
 *
 * Usage:
 *   $NPC_PORTAL = 'admin';    // or 'faculty' | 'student'
 *   include __DIR__ . '/_sidebar.php';
 *
 * Auto-detects the current page and highlights the right item.
 * Renders a fixed navy sidebar on desktop + slide-in drawer on mobile
 * with an overlay toggle button.
 */
if (session_status() === PHP_SESSION_NONE) session_start();
/* Block direct browser access — this file is an include-only component */
if (basename($_SERVER['PHP_SELF']) === '_sidebar.php') {
    if (empty($_SESSION['user_id'])) { http_response_code(403); exit('Forbidden'); }
}
$NPC_PORTAL = isset($NPC_PORTAL) ? $NPC_PORTAL : 'student';
$npcUserName  = $_SESSION['name'] ?? 'User';
$npcUserEmail = $_SESSION['email'] ?? '';
$npcInitial   = strtoupper(substr($npcUserName, 0, 1));
$cur = basename($_SERVER['PHP_SELF']);

/* ─── Portal definitions ───────────────────────────────────────────── */
$NPC_MENUS = [
    'admin' => [
        ['href' => 'admin.php',             'icon' => 'dashboard',        'label' => 'Dashboard'],
        ['href' => 'admin_grades.php',      'icon' => 'verified',         'label' => 'Grade Approvals'],
        ['href' => 'admin_classes.php',     'icon' => 'school',           'label' => 'Classes & Rosters'],
        ['href' => 'admin_schedules.php',   'icon' => 'calendar_month',   'label' => 'Student Schedules'],
        ['href' => 'admin_attendance.php',  'icon' => 'qr_code_scanner',  'label' => 'Live Attendance QR'],
        ['href' => 'admin_students.php',    'icon' => 'group',            'label' => 'Student Directory'],
        ['href' => 'admin_users.php',       'icon' => 'manage_accounts',  'label' => 'User Management'],
        ['href' => 'admin_announcements.php','icon' => 'campaign',        'label' => 'Announcements'],
        ['href' => 'admin_docs.php',        'icon' => 'description',      'label' => 'Documents & AI'],
        ['href' => 'admin_settings.php',    'icon' => 'settings',         'label' => 'System Settings'],
    ],
    'faculty' => [
        ['href' => 'teacher.php',              'icon' => 'dashboard',       'label' => 'Dashboard'],
        ['href' => 'teacher_classes.php',      'icon' => 'school',          'label' => 'My Assigned Classes'],
        ['href' => 'teacher_attendance.php',   'icon' => 'qr_code_scanner', 'label' => 'Live Attendance QR'],
        ['href' => 'teacher_grades.php',       'icon' => 'grade',           'label' => 'Grade Encoding'],
        ['href' => 'teacher_ai_assistant.php', 'icon' => 'smart_toy',       'label' => 'Teaching Assistant AI'],
    ],
    'student' => [
        ['href' => 'index.php',     'icon' => 'dashboard',       'label' => 'Dashboard'],
        ['href' => 'academic.php',  'icon' => 'school',          'label' => 'Academic'],
        ['href' => 'ai_assistant.php', 'icon' => 'smart_toy',    'label' => 'AI Assistant'],
        ['href' => 'schedule.php',  'icon' => 'calendar_month',  'label' => 'Schedule'],
        ['href' => 'qrcode.php',    'icon' => 'qr_code_scanner', 'label' => 'Scan QR Code'],
        ['href' => 'settings.php',  'icon' => 'settings',        'label' => 'Settings'],
    ],
];
$items = $NPC_MENUS[$NPC_PORTAL] ?? $NPC_MENUS['student'];

$PORTAL_META = [
    'admin'   => ['Admin Portal',     'admin_panel_settings'],
    'faculty' => ['Faculty Portal',   'cast_for_education'],
    'student' => ['Student Portal',   'school'],
];
$pm = $PORTAL_META[$NPC_PORTAL] ?? $PORTAL_META['student'];

/* Pages that map to a menu parent for highlighting purposes */
$ALIASES = ['admin_class_view.php' => 'admin_classes.php'];

$activeHref = isset($ALIASES[$cur]) ? $ALIASES[$cur] : $cur;
?>
<!-- ══════════ NPC Shared Sidebar ══════════ -->
<!-- Self-contained critical styles: sidebar renders correctly even if
     the main stylesheet is cached or fails to load. -->
<style>
    #npc-sidebar.npc-side-nav{background:radial-gradient(600px 300px at -20% 0%,var(--nav-glow-rgba,rgba(254,212,136,.14)),transparent 60%),rgb(var(--nav-bg-rgb,0 19 42));border-right:1px solid rgba(255,255,255,.06)}
    #npc-sidebar .npc-brand-title{color:#fff}
    #npc-sidebar .npc-brand-sub{color:rgba(170,199,255,.85)}
    #npc-sidebar .npc-side-link{display:flex;align-items:center;gap:.75rem;padding:.7rem 1rem;border-radius:.75rem;font-size:.875rem;font-weight:500;color:rgba(170,199,255,.95);transition:background-color .18s ease,color .18s ease,transform .18s ease}
    #npc-sidebar .npc-side-link:hover{background:rgba(255,255,255,.10);color:#fff;transform:translateX(2px)}
    #npc-sidebar .npc-side-link.active{background:rgb(var(--secondary-container-rgb,254 212 136));color:rgb(var(--on-secondary-container-rgb,78 58 12));font-weight:600;box-shadow:0 4px 14px -4px rgba(254,212,136,.45)}
</style>
<nav id="npc-sidebar" class="npc-side-nav hidden lg:flex flex-col w-64 h-screen fixed left-0 top-0 z-40 py-6 shadow-md">
    <div class="px-6 mb-8 flex flex-col items-center">
        <img class="w-16 h-16 rounded-full mb-3 object-cover bg-white p-1 shadow-sm" alt="NPC Emblem"
             src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg">
        <h1 class="text-xl font-bold tracking-tight text-center npc-brand-title">NPC Connect</h1>
        <p class="text-xs font-mono uppercase tracking-wider mt-1 npc-brand-sub"><?= $pm[0] ?></p>
    </div>

    <div class="flex-1 px-4 space-y-1.5 font-medium text-sm overflow-y-auto" id="npc-side-links">
        <?php foreach ($items as $it):
            $isActive = ($it['href'] === $activeHref); ?>
        <a href="<?= $it['href'] ?>"
           class="npc-side-link<?= $isActive ? ' active' : '' ?>">
            <span class="material-symbols-outlined text-[20px]"><?= $it['icon'] ?></span>
            <span><?= $it['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="px-4 mt-auto pt-4 border-t border-white/10 space-y-1">
        <?php if ($NPC_PORTAL !== 'admin'): ?>
        <!-- Admins can enter; others are bounced by the guards -->
        <a href="admin.php" class="npc-side-link" title="Administrators only">
            <span class="material-symbols-outlined text-[20px]">shield_person</span>
            <span>Admin Portal</span>
        </a>
        <?php endif; ?>
        <?php if ($NPC_PORTAL === 'faculty'): ?>
        <a href="index.php" class="npc-side-link">
            <span class="material-symbols-outlined text-[20px]">person</span>
            <span>Student View</span>
        </a>
        <?php endif; ?>
        <?php if ($NPC_PORTAL === 'student'): ?>
        <a href="teacher.php" class="npc-side-link" title="Faculty only">
            <span class="material-symbols-outlined text-[20px]">cast_for_education</span>
            <span>Faculty Portal</span>
        </a>
        <?php endif; ?>
        <a href="/logout.php" class="npc-side-link" style="color:#ff8a80;">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span>Sign Out</span>
        </a>
    </div>
</nav>

<!-- Mobile drawer + topbar hamburger handled by npc.js (#npc-mobile-menu-btn) -->
<div id="npc-drawer-overlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[45] hidden lg:hidden"></div>
