<?php
require_once 'auth.php';
require_login();
$is_logged_in = isset($_SESSION['user_id']);
$raw_name = (isset($_SESSION['name']) && $_SESSION['name'] !== null) ? (string)$_SESSION['name'] : 'Guest User';
$user_name = $is_logged_in ? explode(' ', trim($raw_name))[0] : 'Guest';
$user_id_display = $is_logged_in && isset($_SESSION['student_number']) ? (string)$_SESSION['student_number'] : 'GUEST';
$is_admin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'registrar');
$jsConfig = getJsConfig();
$csrf_token = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php $PAGE_TITLE = 'NPC Connect - Academic Portal'; include __DIR__ . '/_head.php'; ?>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>

    <!-- App Container -->
    <div class="flex min-h-screen w-full" id="app-root">

        <!-- SideNavBar (Sticky Desktop navigation) -->
        <?php $NPC_PORTAL = 'student'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-surface" id="main-wrapper">

        <!-- TopNavBar Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Connect</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block" id="page-title">Dashboard</h2>
            </div>

            <div class="flex items-center gap-3">
                <span class="font-mono text-xs font-semibold bg-surface-container px-3 py-1.5 rounded-md border border-outline-variant text-primary" id="user-id-chip">ID: <?= htmlspecialchars($user_id_display) ?></span>
                <img alt="User profile avatar" class="w-9 h-9 rounded-full object-cover border border-outline-variant shadow-sm"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDY0YlFx4kB5x_Rj0yQKvW09upvbRIgsxiTOfv_YigLlswS0RXYGufPqUxoUuVd_bLZ_KE0GB0Ptj2-vztXffkP_cnsXegpkXH74h3zlnDr9hKjw2qrYP-VCz3m7k_WVJkoXu3TTogTQrvCuK0foEFWF6UW_ls96NG-zedSKfDJmwR-nGFSKnjpKJtj_siJzuRiXlEkZKKfUHgQqSYXq-qqp9U-UFk-qgYsAClMs8P3C9NGcDm1eQMFNg">
                <span class="text-sm font-semibold text-primary hidden sm:inline" id="user-name-display"><?= htmlspecialchars($raw_name) ?></span>
            </div>
        </header>

        <!-- Page Canvas -->
        <main class="flex-1 p-6 md:p-10 max-w-7xl w-full mx-auto lg:pl-64" id="canvas-container">

            <!-- 1. LOGIN VIEW REMOVED - NOW IN LOGIN.PHP -->

            <!-- 2. STUDENT DASHBOARD VIEW -->
            <div id="dashboard-view" class="view active">
                <!-- Dashboard Greeting Header -->
                <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-outline-variant/60 pb-6">
                    <div>
                        <p class="font-mono text-xs text-outline font-medium uppercase tracking-wider mb-1" id="current-date">Loading date...</p>
                        <?php
                            // ── Role-aware greeting ──────────────────────────────
                            $npcRole = $_SESSION['role'] ?? 'student';
                            if ($npcRole === 'admin') {
                                $npcHello  = 'Administrator view — seeing the portal exactly as students do.';
                                $npcBadge  = ['ADMIN VIEW', 'shield_person', 'bg-error/10 text-error border border-error/30'];
                                $npcWave   = 'shield_person';
                            } elseif ($npcRole === 'teacher') {
                                $npcHello  = 'Faculty view — checking what your students see.';
                                $npcBadge  = ['FACULTY VIEW', 'cast_for_education', 'bg-secondary-container text-on-secondary-container border border-secondary-container'];
                                $npcWave   = 'cast_for_education';
                            } else {
                                $npcHello  = 'Here is your real-time academic overview and schedule.';
                                $npcBadge  = ['STUDENT', 'school', 'bg-surface-container text-primary border border-outline-variant'];
                                $npcWave   = 'waving_hand';
                            }
                        ?>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight">
                            <span class="text-shimmer text-primary">Welcome, <?= htmlspecialchars($user_name) ?>.</span>
                            <span class="inline-block align-middle ml-2 animate-float material-symbols-outlined text-[28px] text-secondary" aria-hidden="true"><?= $npcWave ?></span>
                        </h2>
                        <p class="text-base text-on-surface-variant mt-1 flex flex-wrap items-center gap-x-3 gap-y-1">
                            <span><?= htmlspecialchars($npcHello) ?></span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-mono text-[10px] font-bold uppercase tracking-widest <?= $npcBadge[2] ?>">
                                <span class="material-symbols-outlined text-[13px]"><?= $npcBadge[1] ?></span>
                                <?= $npcBadge[0] ?>
                            </span>
                            <span class="inline-flex items-center gap-1.5 font-mono text-xs font-semibold px-2 py-0.5 rounded bg-surface-container border border-outline-variant text-primary">
                                <span class="material-symbols-outlined text-[13px] text-status-success">schedule</span>
                                <span id="npc-live-clock"><?= date('g:i:s A') ?></span>
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-surface-container border border-outline-variant text-primary font-mono text-xs font-semibold">
                                <span class="w-2 h-2 rounded-full <?= $is_logged_in ? 'bg-status-success' : 'bg-status-warning' ?>"></span>
                                <?= $is_logged_in ? 'Student ID: ' . htmlspecialchars($user_id_display) : 'Guest Mode' ?>
                            </span>
                    </div>
                </div>

                <!-- Quick Actions Bento Grid -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8" aria-label="Quick actions">
                    <a href="qrcode.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm flex items-center gap-4 group">
                        <div class="w-11 h-11 rounded-xl bg-primary-container text-on-primary flex items-center justify-center shrink-0 npc-navy-card">
                            <span class="material-symbols-outlined npc-tile-icon text-[22px]">qr_code_scanner</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">Scan Attendance</p>
                            <p class="text-[11px] text-on-surface-variant font-mono">QR check-in</p>
                        </div>
                    </a>
                    <a href="schedule.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm flex items-center gap-4 group">
                        <div class="w-11 h-11 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined npc-tile-icon text-[22px]">calendar_month</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">My Schedule</p>
                            <p class="text-[11px] text-on-surface-variant font-mono">Weekly view</p>
                        </div>
                    </a>
                    <a href="academic.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-5 shadow-sm flex items-center gap-4 group">
                        <div class="w-11 h-11 rounded-xl bg-surface-container-high text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined npc-tile-icon text-[22px]">insights</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-on-surface">My Grades</p>
                            <p class="text-[11px] text-on-surface-variant font-mono">Performance</p>
                        </div>
                    </a>
                    <a href="ai_assistant.php" class="npc-card npc-tile ripple press bg-primary text-on-primary rounded-2xl p-5 shadow-sm flex items-center gap-4 group npc-navy-card">
                        <div class="w-11 h-11 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined npc-tile-icon text-[22px] text-secondary-container">smart_toy</span>
                        </div>
                        <div>
                            <p class="text-sm font-bold">Ask Campus AI</p>
                            <p class="text-[11px] font-mono opacity-70">Instant answers</p>
                        </div>
                    </a>
                </section>

                <!-- Main Content 2-Column Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- Left 2-Column Area: Classes & Attendance -->
                    <div class="lg:col-span-2 flex flex-col gap-6">

                        <!-- Upcoming Classes Card -->
                        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant overflow-hidden shadow-sm">
                            <div class="p-5 border-b border-outline-variant/60 flex justify-between items-center bg-surface-subtle">
                                <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">school</span>
                                    <span>Upcoming Classes</span>
                                </h3>
                                <span class="font-mono text-xs text-on-surface-variant font-medium bg-surface-container px-2.5 py-1 rounded">Today</span>
                            </div>

                            <div class="divide-y divide-outline-variant/40">
                                <?php if (!$is_logged_in): ?>
                                    <div class="p-8 text-center flex flex-col items-center justify-center">
                                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-3">lock</span>
                                        <h4 class="text-lg font-semibold text-on-surface mb-1">Login Required</h4>
                                        <p class="text-sm text-on-surface-variant max-w-md mx-auto mb-4">You must be logged in as an official NPC student to view your upcoming classes and schedule.</p>
                                        <a href="/login.php" class="px-5 py-2 bg-primary text-white font-semibold rounded-lg hover:bg-primary-container transition-colors inline-flex items-center gap-2">
                                            <span class="material-symbols-outlined text-[18px]">login</span> Sign In
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div id="classes-container" class="divide-y divide-outline-variant/40">
                                        <div class="p-5 text-center text-on-surface-variant text-sm">
                                            <span class="animate-pulse">Loading classes...</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </section>

                        <!-- Attendance Summary Widget -->
                        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-6 shadow-sm">
                            <h3 class="text-lg font-bold text-primary mb-6 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary text-[20px]">fact_check</span>
                                <span>Attendance Overview</span>
                            </h3>

                            <?php if (!$is_logged_in): ?>
                                <div class="text-center py-6">
                                    <span class="material-symbols-outlined text-[36px] text-outline-variant mb-2">lock</span>
                                    <p class="text-sm text-on-surface-variant font-medium">Login to view attendance</p>
                                </div>
                            <?php else: ?>
                                <div class="flex flex-col sm:flex-row items-center justify-around gap-6 sm:gap-8">
                                    <!-- Donut Chart -->
                                    <div class="relative w-32 h-32 shrink-0">
                                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                            <!-- Background Circle -->
                                            <path class="text-surface-container-high" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-width="3.5"></path>
                                            <!-- Present (Green/Primary) -->
                                            <path id="donut-present-path" class="text-primary transition-all duration-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="100, 100" stroke-width="3.5"></path>
                                            <!-- Late (Yellow) -->
                                            <path id="donut-late-path" class="text-secondary-container transition-all duration-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-dashoffset="0" stroke-width="3.5"></path>
                                            <!-- Absent (Red) -->
                                            <path id="donut-absent-path" class="text-error transition-all duration-700" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="currentColor" stroke-dasharray="0, 100" stroke-dashoffset="0" stroke-width="3.5"></path>
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                            <span id="attendance-rate-display" class="text-2xl font-bold text-primary">100%</span>
                                            <span class="text-[10px] font-mono text-on-surface-variant uppercase">Rate</span>
                                        </div>
                                    </div>

                                    <!-- Legend Cards -->
                                    <div class="flex-1 w-full grid grid-cols-3 gap-2.5 sm:gap-3">
                                        <div class="p-3 bg-surface-container-low rounded-xl border border-outline-variant/60 flex flex-col items-center text-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-primary mb-1.5"></div>
                                            <span class="font-mono text-[10px] text-on-surface-variant uppercase mb-0.5">Present</span>
                                            <span id="attendance-present-stat" class="text-base sm:text-lg font-bold text-on-surface">100%</span>
                                            <span id="attendance-present-count" class="text-[10px] font-mono text-on-surface-variant/80">0 scans</span>
                                        </div>
                                        <div class="p-3 bg-surface-container-low rounded-xl border border-outline-variant/60 flex flex-col items-center text-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-secondary-container mb-1.5"></div>
                                            <span class="font-mono text-[10px] text-on-surface-variant uppercase mb-0.5">Late</span>
                                            <span id="attendance-late-stat" class="text-base sm:text-lg font-bold text-on-surface">0%</span>
                                            <span id="attendance-late-count" class="text-[10px] font-mono text-on-surface-variant/80">0 scans</span>
                                        </div>
                                        <div class="p-3 bg-surface-container-low rounded-xl border border-outline-variant/60 flex flex-col items-center text-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-error mb-1.5"></div>
                                            <span class="font-mono text-[10px] text-on-surface-variant uppercase mb-0.5">Absent</span>
                                            <span id="attendance-absent-stat" class="text-base sm:text-lg font-bold text-error">0%</span>
                                            <span id="attendance-absent-count" class="text-[10px] font-mono text-on-surface-variant/80">0 scans</span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </section>
                    </div>

                    <!-- Right Column: Announcements Feed -->
                    <div class="lg:col-span-1 flex flex-col gap-6">
                        <section class="bg-surface-container-lowest rounded-2xl border border-outline-variant p-6 shadow-sm flex flex-col">
                            <div class="pb-4 border-b border-outline-variant/60 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                                    <span class="material-symbols-outlined text-secondary text-[22px]" style="font-variation-settings: 'FILL' 1;">campaign</span>
                                    <span>Campus Bulletins</span>
                                </h3>
                                <span class="font-mono text-xs text-on-surface-variant font-semibold bg-surface-container px-2 py-0.5 rounded">Live</span>
                            </div>

                            <div class="py-2 flex flex-col gap-3.5" id="announcements-container">
                                <div class="text-center text-on-surface-variant text-sm py-4">
                                    <span class="animate-pulse">Loading announcements...</span>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>

            <script>
                const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
                const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
                let supabaseClient = null;
                try { supabaseClient = supabase.createClient(supabaseUrl, supabaseKey); }
                catch (e) { console.warn('Supabase init failed:', e); }

                function escapeHtml(text) {
                    if (!text) return '';
                    var d = document.createElement('div');
                    d.textContent = String(text);
                    return d.innerHTML;
                }

                // Fetch dynamic data for dashboard
                document.addEventListener('DOMContentLoaded', async () => {
                    loadClassesFeed();
                    try { loadAnnouncementsFeed(); } catch (e) { console.warn('announcements:', e); }
                    try { loadAttendanceOverview(); } catch (e) { console.warn('attendance:', e); }
                });

                /* Donut safety: recompute on resize/rotation so the chart
                   never renders broken on phones. */
                (function () {
                    var t = null;
                    window.addEventListener('resize', function () {
                        clearTimeout(t);
                        t = setTimeout(function () {
                            var p = document.getElementById('donut-present-path');
                            if (p && typeof loadAttendanceOverview === 'function') {
                                try { loadAttendanceOverview(); } catch (e) { /* noop */ }
                            }
                        }, 250);
                    });
                })();

                async function loadClassesFeed() {
                    const container = document.getElementById('classes-container');
                    if (!container || !supabaseClient) return;
                    try {
                        const { data, error } = await supabaseClient
                            .from('classes')
                            .select('*')
                            .order('created_at', { ascending: false })
                            .limit(3);

                        if (error) throw error;

                        if (data && data.length > 0) {
                            container.innerHTML = data.map(c => `
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-surface-container-low/50 transition-colors">
                                <div class="flex items-start gap-3.5 sm:gap-4">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-xl bg-surface-container flex flex-col items-center justify-center text-primary border border-outline-variant shrink-0 font-bold">
                                        <span class="font-mono text-xs leading-tight">${escapeHtml(c.code)}</span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 mb-1">
                                            <h4 class="text-sm sm:text-base font-semibold text-on-surface break-words">${escapeHtml(c.title)}</h4>
                                            <span class="px-2 py-0.5 rounded-full bg-status-info/10 text-status-info font-mono text-[10px] uppercase font-bold border border-status-info/20">${escapeHtml(c.section || '01')}</span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 font-mono text-xs text-on-surface-variant">
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">person</span> ${escapeHtml(c.instructor || 'Faculty')}</span>
                                            <span class="hidden sm:inline w-1 h-1 rounded-full bg-outline-variant"></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">meeting_room</span> ${escapeHtml(c.room || 'TBA')}</span>
                                            <span class="hidden sm:inline w-1 h-1 rounded-full bg-outline-variant"></span>
                                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">schedule</span> ${escapeHtml(c.schedule_day || 'TBA')}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            `).join('');
                        } else {
                            container.innerHTML = '<div class="p-5 text-center text-on-surface-variant text-sm">No scheduled classes found.</div>';
                        }
                    } catch (err) {
                        console.error('Failed to load classes', err);
                        container.innerHTML = '<div class="p-5 text-center text-on-surface-variant text-sm">No scheduled classes found.</div>';
                    }
                }

                async function loadAttendanceOverview() {
                    try {
                        const res = await fetch('api_student.php?action=get_attendance_history', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const d = await res.json();
                        
                        let present = 0, late = 0, absent = 0, total = 0, rate = 100;
                        if (d && d.success && d.stats) {
                            present = parseInt(d.stats.present || 0, 10);
                            late = parseInt(d.stats.late || 0, 10);
                            absent = parseInt(d.stats.absent || 0, 10);
                            total = parseInt(d.stats.total_checkins || 0, 10);
                            if (!total) total = present + late + absent;
                            rate = total > 0 ? parseFloat(d.stats.rate) || 0 : 100;
                        }

                        const presentPct = total > 0 ? Math.round((present / total) * 100) : 100;
                        const latePct = total > 0 ? Math.round((late / total) * 100) : 0;
                        const absentPct = total > 0 ? Math.round((absent / total) * 100) : 0;

                        const rateDisplay = document.getElementById('attendance-rate-display');
                        if (rateDisplay) rateDisplay.textContent = (total > 0 ? Math.round(rate) : 100) + '%';

                        const pStat = document.getElementById('attendance-present-stat');
                        if (pStat) pStat.textContent = presentPct + '%';
                        const pCount = document.getElementById('attendance-present-count');
                        if (pCount) pCount.textContent = present + ' scans';

                        const lStat = document.getElementById('attendance-late-stat');
                        if (lStat) lStat.textContent = latePct + '%';
                        const lCount = document.getElementById('attendance-late-count');
                        if (lCount) lCount.textContent = late + ' scans';

                        const aStat = document.getElementById('attendance-absent-stat');
                        if (aStat) aStat.textContent = absentPct + '%';
                        const aCount = document.getElementById('attendance-absent-count');
                        if (aCount) aCount.textContent = absent + ' scans';

                        // Animate SVG Donut Paths
                        const pPath = document.getElementById('donut-present-path');
                        if (pPath) pPath.setAttribute('stroke-dasharray', `${presentPct}, 100`);

                        const lPath = document.getElementById('donut-late-path');
                        if (lPath) {
                            lPath.setAttribute('stroke-dasharray', `${latePct}, 100`);
                            lPath.setAttribute('stroke-dashoffset', `-${presentPct}`);
                        }

                        const aPath = document.getElementById('donut-absent-path');
                        if (aPath) {
                            aPath.setAttribute('stroke-dasharray', `${absentPct}, 100`);
                            aPath.setAttribute('stroke-dashoffset', `-${presentPct + latePct}`);
                        }
                    } catch (e) {
                        console.warn('Attendance stats using default fallback:', e);
                    }
                }

                function renderAnnouncementBody(raw) {
                    if (!raw) return '<p class="text-on-surface-variant/70 italic text-xs">No announcement content.</p>';
                    let text = String(raw).trim();

                    // Strip browser extension artifacts
                    text = text.replace(/\s*bis_skin_checked="[^"]*"/gi, '');
                    text = text.replace(/\s*contenteditable="[^"]*"/gi, '');
                    text = text.replace(/\s*spellcheck="[^"]*"/gi, '');

                    // Check if content has HTML markup
                    if (/<(div|p|span|ul|ol|li|h[1-6]|strong|b|em|i|blockquote|table|br)/i.test(text)) {
                        // Strip risky tags
                        text = text.replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '');
                        text = text.replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '');
                        text = text.replace(/on\w+="[^"]*"/gi, '');

                        // Normalize alert boxes from editor to theme-aware classes
                        text = text.replace(/style="[^"]*background-color:\s*#fee2e2[^"]*"/gi, 'class="p-3 mb-2 rounded-xl bg-error/15 border-l-4 border-error text-on-surface text-xs"');
                        text = text.replace(/style="[^"]*background-color:\s*#eff4ff[^"]*"/gi, 'class="p-3 mb-2 rounded-xl bg-primary/15 border-l-4 border-primary text-on-surface text-xs"');
                        text = text.replace(/style="[^"]*background-color:\s*#fef3c7[^"]*"/gi, 'class="p-3 mb-2 rounded-xl bg-secondary-container/30 border-l-4 border-secondary text-on-surface text-xs"');

                        // Style lists cleanly
                        text = text.replace(/<ul\b([^>]*)>/gi, '<ul class="list-disc ml-5 my-1 space-y-1 text-xs" $1>');
                        text = text.replace(/<ol\b([^>]*)>/gi, '<ol class="list-decimal ml-5 my-1 space-y-1 text-xs" $1>');

                        return `<div class="announcement-content text-xs leading-relaxed break-words" style="overflow-wrap:anywhere; word-break:break-word;">${text}</div>`;
                    }

                    // Plain text or markdown formatting
                    const escaped = escapeHtml(text);
                    const formatted = escaped
                        .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-primary">$1</strong>')
                        .replace(/\*(.*?)\*/g, '<em class="italic">$1</em>')
                        .replace(/&lt;u&gt;(.*?)&lt;\/u&gt;/gi, '<u class="underline">$1</u>')
                        .replace(/• (.*?)(\n|$)/g, '<li class="ml-4 list-disc">$1</li>')
                        .replace(/\n/g, '<br>');

                    return `<div class="announcement-content text-xs leading-relaxed break-words" style="overflow-wrap:anywhere; word-break:break-word;">${formatted}</div>`;
                }

                /* Announcement cards grow with their text. Very long posts
                   start collapsed (~12 lines) with a smooth Read-more. */
                function buildAnnouncementCard(a) {
                    /* Box height always matches the text exactly — no clamping */
                    return `<div class="announcement-body text-xs leading-relaxed">${renderAnnouncementBody(a.body)}</div>`;
                }

                async function loadAnnouncementsFeed() {
                    const container = document.getElementById('announcements-container');
                    if (!container || !supabaseClient) return;
                    try {
                        const { data, error } = await supabaseClient
                            .from('announcements')
                            .select('*')
                            .eq('status', 'published')
                            .order('created_at', { ascending: false })
                            .limit(8);

                        if (error) throw error;

                        if (data && data.length > 0) {
                            container.innerHTML = data.map((a, idx) => {
                                const isEmergency = a.category === 'emergency';
                                const isAcademic = a.category === 'academic';
                                const badgeClass = isEmergency 
                                    ? 'bg-error-container text-error' 
                                    : (isAcademic ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container text-primary');
                                const icon = isEmergency ? 'warning' : (isAcademic ? 'school' : 'campaign');
                                const dateStr = new Date(a.created_at).toLocaleDateString();

                                return `
                                <div class="p-3.5 bg-surface-container-low/40 rounded-xl border border-outline-variant/40 hover:bg-surface-container-low transition-colors space-y-2 overflow-hidden">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full ${badgeClass} font-mono text-[10px] uppercase font-bold tracking-wide shrink-0">
                                            <span class="material-symbols-outlined text-[12px]">${icon}</span> ${escapeHtml(a.category)}
                                        </span>
                                        <span class="font-mono text-[10px] text-on-surface-variant shrink-0">${dateStr}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-primary break-words leading-snug" style="overflow-wrap:anywhere; word-break:break-word;">${escapeHtml(a.title)}</h4>
                                    ${buildAnnouncementCard(a)}
                                </div>
                                `;
                            }).join('');
                        } else {
                            container.innerHTML = '<div class="text-center text-on-surface-variant text-sm py-4">No recent announcements.</div>';
                        }
                    } catch (err) {
                        console.error('Failed to load announcements', err);
                        container.innerHTML = '<div class="text-center text-error text-sm py-4">Failed to load announcements.</div>';
                    }
                }
            </script>

            <!-- 3. NPC AI ASSISTANT CHAT VIEW -->
            <div id="chatbot-view" class="view">
                <div class="h-[calc(100vh-8rem)] flex overflow-hidden bg-surface border border-outline-variant rounded-2xl shadow-sm">
                    <!-- Conversation History Sidebar -->
                    <aside class="hidden md:flex flex-col w-72 bg-surface-subtle border-r border-outline-variant">
                        <div class="p-4 border-b border-outline-variant">
                            <button class="w-full flex items-center justify-center gap-2 bg-primary text-on-primary py-2.5 px-4 rounded-xl hover:bg-primary/90 transition-colors shadow-sm">
                                <span class="material-symbols-outlined text-sm">add</span>
                                <span class="font-mono text-sm font-semibold">New Conversation</span>
                            </button>
                        </div>
                        <div class="flex-1 overflow-y-auto p-2 space-y-1">
                            <div class="px-3 py-2 text-on-surface-variant font-mono text-xs font-semibold opacity-70 mt-2">Today</div>
                            <button class="w-full text-left px-3 py-2.5 rounded-lg bg-surface-container text-on-surface flex items-center gap-3 border border-outline-variant/50">
                                <span class="material-symbols-outlined text-outline">chat_bubble</span>
                                <span class="text-base truncate">Enrollment Deadlines</span>
                            </button>
                            <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                <span class="text-base truncate">Degree Audit Help</span>
                            </button>
                            <div class="px-3 py-2 text-on-surface-variant font-mono text-xs font-semibold opacity-70 mt-4">Previous 7 Days</div>
                            <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                <span class="text-base truncate">Library Access Hours</span>
                            </button>
                            <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                <span class="text-base truncate">Campus Wi-Fi Setup</span>
                            </button>
                        </div>
                    </aside>

                    <!-- Active Chat Canvas -->
                    <section class="flex-1 flex flex-col bg-surface relative">
                        <!-- Chat Header -->
                        <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-lowest flex justify-between items-center shadow-sm z-10">
                            <div>
                                <h2 class="text-2xl font-bold text-on-surface">NPC AI Assistant</h2>
                                <p class="font-mono text-sm font-semibold text-on-surface-variant">Your official student information assistant</p>
                            </div>
                            <!-- Safety UI Indicator -->
                            <div class="flex items-center gap-2 bg-surface-container-high px-3 py-1.5 rounded-full border border-outline-variant">
                                <span class="material-symbols-outlined text-status-info" style="font-size: 18px;">shield</span>
                                <span class="font-mono text-xs font-semibold text-on-surface-variant">Warning Count: <span class="font-bold">0/3</span></span>
                            </div>
                        </div>

                        <!-- Chat Messages Area -->
                        <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-background">
                            <!-- Intro Message -->
                            <div class="flex justify-center my-4">
                                <div class="bg-surface-container-low px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant font-mono text-xs font-semibold">
                                    Conversation started automatically
                                </div>
                            </div>

                            <!-- Initial AI Message -->
                            <div class="flex flex-col items-start w-full">
                                <div class="flex gap-3 max-w-[90%] md:max-w-[80%]">
                                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 mt-1 shadow-sm">
                                        <span class="material-symbols-outlined text-on-primary" style="font-size: 18px;">smart_toy</span>
                                    </div>
                                    <div class="bg-surface-container-lowest border border-outline-variant p-4 rounded-xl rounded-tl-none shadow-sm flex flex-col gap-3">
                                        <div class="text-base text-on-surface space-y-3">
                                            <p>Hello! I am your NPC academic assistant. You can ask me any question regarding campus policies, course requirements, or administrative documents in the knowledge base.</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="font-mono text-xs font-semibold text-on-surface-variant mt-1 ml-11">Now</span>
                            </div>
                        </div>

                        <!-- Input Area -->
                        <div class="p-6 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                            <div class="max-w-4xl mx-auto flex items-end gap-2 bg-surface-subtle border border-outline-variant rounded-2xl focus-within:border-primary focus-within:ring-1 focus-within:ring-primary overflow-hidden pr-2 pl-4 py-2 shadow-inner transition-shadow">
                                <textarea id="chat-input" class="flex-1 bg-transparent border-none focus:outline-none focus:ring-0 resize-none text-base text-on-surface py-2 max-h-32 overflow-y-auto" placeholder="Ask about academic policies, campus services, or your records..." rows="1" style="min-height: 40px;"></textarea>
                                <div class="flex items-center gap-1 pb-1">
                                    <button aria-label="Attach file" class="text-on-surface-variant p-2 hover:bg-surface-container-high hover:text-primary rounded-full transition-colors cursor-pointer">
                                        <span class="material-symbols-outlined">attach_file</span>
                                    </button>
                                    <button id="chat-send-btn" aria-label="Send message" onclick="sendMessage()" class="text-white bg-primary p-2 hover:bg-primary/90 rounded-full transition-colors flex items-center justify-center shadow-md cursor-pointer">
                                        <span class="material-symbols-outlined" style="font-size: 20px;">send</span>
                                    </button>
                                </div>
                            </div>
                            <div class="text-center mt-2">
                                <p class="font-mono text-xs font-semibold text-on-surface-variant/70">AI Assistant may produce inaccurate information. Always verify against official documents.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <!-- 5. ATTENDANCE KIOSK VIEW -->
            <div id="kiosk-view" class="view">
                <div class="flex flex-col items-center justify-center min-h-[70vh]">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-8 md:p-10 text-center max-w-lg w-full shadow-sm">
                        <div class="w-16 h-16 rounded-2xl bg-surface-container-low flex items-center justify-center mx-auto mb-6 text-primary border border-outline-variant">
                            <span class="material-symbols-outlined text-[36px]">fact_check</span>
                        </div>
                        <h1 class="text-2xl font-bold text-primary mb-2">Attendance Check-In</h1>
                        <p class="text-sm text-on-surface-variant mb-6">Enter your Student ID number or scan your badge below.</p>

                        <div class="mb-6">
                            <label class="block mb-2 font-mono text-xs text-on-surface uppercase font-semibold">Student ID Number</label>
                            <input type="text" id="kiosk-id" class="w-full text-center text-2xl font-mono font-bold p-4 bg-surface border border-outline-variant rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 tracking-wider"
                                placeholder="e.g. 251505">
                        </div>

                        <button class="w-full bg-primary hover:bg-primary-container text-white font-semibold py-4 px-6 rounded-xl transition-all shadow-md flex items-center justify-center gap-2 cursor-pointer active:scale-98" onclick="checkIn()">
                            <span class="material-symbols-outlined text-[20px]">check_circle</span>
                            <span>Confirm Attendance</span>
                        </button>

                        <div id="kiosk-status" class="mt-6 font-mono text-sm"></div>
                    </div>
                </div>
            </div>

            <!-- 6. ACADEMIC VIEW -->
            <div id="academic-view" class="view">
                <div class="flex flex-col gap-6 lg:gap-8">
                    <!-- Page Header & Student Summary -->
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-outline-variant pb-6">
                        <div>
                            <h1 class="font-headline-lg-mobile md:font-headline-lg text-headline-lg-mobile md:text-headline-lg text-primary mb-2">Academic Performance</h1>
                            <p class="font-body-md text-body-md text-on-surface-variant max-w-2xl">Track your grades, view academic standing, and monitor your progress towards graduation.</p>
                        </div>
                        <div class="flex items-center gap-4 bg-surface-container-lowest p-4 rounded-xl border border-outline-variant shrink-0">
                            <div class="w-12 h-12 rounded-full bg-primary-container overflow-hidden flex items-center justify-center">
                                <span class="text-on-primary text-lg font-bold"><?php echo strtoupper(substr($user_name, 0, 1)); ?></span>
                            </div>
                            <div>
                                <h2 class="font-headline-md text-headline-md text-on-surface"><?= htmlspecialchars($_SESSION['name']) ?></h2>
                                <div class="flex items-center gap-3 font-label-sm text-label-sm text-on-surface-variant mt-1">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">badge</span> <?= htmlspecialchars($user_id_display) ?></span>
                                    <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">computer</span> —</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance Overview (Bento Grid) -->
                    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Sem GPA -->
                        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant hover:shadow-md transition-shadow duration-300 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-[64px]">trending_up</span>
                            </div>
                            <div>
                                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest mb-1">Semester GPA</p>
                                <h3 class="font-display-lg text-display-lg text-primary">1.25</h3>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="inline-flex items-center px-2 py-1 rounded bg-surface-container text-on-primary-container font-label-sm text-label-sm gap-1">
                                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span> Excellent
                                </span>
                            </div>
                        </div>
                        <!-- Cumul GPA -->
                        <div class="bg-surface-container-lowest rounded-xl p-6 border border-outline-variant hover:shadow-md transition-shadow duration-300 flex flex-col justify-between relative overflow-hidden group">
                            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                                <span class="material-symbols-outlined text-[64px]">account_balance</span>
                            </div>
                            <div>
                                <p class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest mb-1">Cumulative GPA</p>
                                <h3 class="font-display-lg text-display-lg text-primary">1.38</h3>
                            </div>
                            <div class="mt-4 flex items-center gap-2">
                                <span class="font-label-sm text-label-sm text-on-surface-variant">Top 5% of Cohort</span>
                            </div>
                        </div>
                        <!-- Standing -->
                        <div class="bg-primary text-on-primary rounded-xl p-6 shadow-md flex flex-col justify-between relative overflow-hidden npc-navy-card">
                            <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-on-primary-fixed-variant/20 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <p class="font-label-sm text-label-sm text-inverse-primary uppercase tracking-widest mb-2">Academic Standing</p>
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="material-symbols-outlined text-[32px] text-secondary-container fill">workspace_premium</span>
                                    <h3 class="font-headline-lg text-headline-lg">Dean's Lister</h3>
                                </div>
                            </div>
                            <div class="relative z-10 mt-4">
                                <p class="font-label-sm text-label-sm text-primary-fixed-dim">Eligible for merit scholarship renewal next semester.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Main Content Area: Table & Trends Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
                        <!-- Grade Report Section (Span 2 cols on lg) -->
                        <div class="lg:col-span-2 flex flex-col gap-4">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <h3 class="font-headline-md text-headline-md text-primary">Grade Report</h3>
                                <!-- Semester Selector -->
                                <div class="relative min-w-[240px]">
                                    <select class="appearance-none w-full bg-surface-container-lowest border border-outline-variant text-on-surface font-label-md text-label-md py-2.5 pl-4 pr-10 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary cursor-pointer hover:bg-surface-container-low transition-colors shadow-sm">
                                        <option>1st Semester, 2026-2027</option>
                                        <option>2nd Semester, 2025-2026</option>
                                        <option>1st Semester, 2025-2026</option>
                                    </select>
                                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                                </div>
                            </div>
                            <!-- Detailed Table -->
                            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl overflow-hidden shadow-sm">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left border-collapse min-w-[600px]">
                                        <thead>
                                            <tr class="bg-primary text-on-primary font-label-sm text-label-sm uppercase tracking-wider npc-navy-card">
                                                <th class="p-4 py-3 font-medium border-b border-primary-container">Subject Code</th>
                                                <th class="p-4 py-3 font-medium border-b border-primary-container">Description</th>
                                                <th class="p-4 py-3 font-medium border-b border-primary-container text-center">Units</th>
                                                <th class="p-4 py-3 font-medium border-b border-primary-container text-center">Grade</th>
                                                <th class="p-4 py-3 font-medium border-b border-primary-container text-right">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="font-body-md text-body-md">
                                            <tr class="border-b border-surface-container-high hover:bg-surface-container-low/50 transition-colors">
                                                <td class="p-4 font-label-md text-label-md text-on-surface-variant">CS311</td>
                                                <td class="p-4 text-on-surface font-medium">Data Structures &amp; Algorithms</td>
                                                <td class="p-4 text-center text-on-surface-variant">3.0</td>
                                                <td class="p-4 text-center font-bold text-primary">1.25</td>
                                                <td class="p-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm">Passed</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-surface-container-high hover:bg-surface-container-low/50 transition-colors bg-surface-subtle">
                                                <td class="p-4 font-label-md text-label-md text-on-surface-variant">IT302</td>
                                                <td class="p-4 text-on-surface font-medium">Web Systems and Technologies</td>
                                                <td class="p-4 text-center text-on-surface-variant">3.0</td>
                                                <td class="p-4 text-center font-bold text-primary">1.50</td>
                                                <td class="p-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm">Passed</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-surface-container-high hover:bg-surface-container-low/50 transition-colors">
                                                <td class="p-4 font-label-md text-label-md text-on-surface-variant">MATH205</td>
                                                <td class="p-4 text-on-surface font-medium">Discrete Mathematics</td>
                                                <td class="p-4 text-center text-on-surface-variant">3.0</td>
                                                <td class="p-4 text-center font-bold text-primary">1.25</td>
                                                <td class="p-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-container text-primary font-label-sm text-label-sm">Passed</span>
                                                </td>
                                            </tr>
                                            <tr class="border-b border-surface-container-high hover:bg-surface-container-low/50 transition-colors bg-surface-subtle">
                                                <td class="p-4 font-label-md text-label-md text-on-surface-variant">HUM102</td>
                                                <td class="p-4 text-on-surface font-medium">Professional Ethics</td>
                                                <td class="p-4 text-center text-on-surface-variant">3.0</td>
                                                <td class="p-4 text-center font-bold text-secondary-container bg-secondary/10 rounded">1.00</td>
                                                <td class="p-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-secondary-container/20 text-on-secondary-container border border-secondary-container font-label-sm text-label-sm">Exceptional</span>
                                                </td>
                                            </tr>
                                            <tr class="hover:bg-surface-container-low/50 transition-colors">
                                                <td class="p-4 font-label-md text-label-md text-on-surface-variant">CS312</td>
                                                <td class="p-4 text-on-surface font-medium">Software Engineering I</td>
                                                <td class="p-4 text-center text-on-surface-variant">3.0</td>
                                                <td class="p-4 text-center font-bold text-outline-variant">-</td>
                                                <td class="p-4 text-right">
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-surface-variant text-on-surface-variant font-label-sm text-label-sm">Ongoing</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="bg-surface-container-low p-4 border-t border-outline-variant flex justify-between items-center">
                                    <span class="font-label-md text-label-md text-on-surface-variant">Total Units Enrolled: <strong class="text-on-surface">15.0</strong></span>
                                    <button class="text-primary font-label-md text-label-md hover:underline flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[18px]">download</span> Download PDF
                                    </button>
                                </div>

                            </div>
                        </div>
                        <!-- Performance Trend Sidebar -->
                        <div class="flex flex-col gap-4">
                            <h3 class="font-headline-md text-headline-md text-primary">Academic History</h3>
                            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-sm flex flex-col gap-6">
                                <!-- Simulated Chart/Trend -->
                                <div class="flex flex-col gap-1">
                                    <div class="flex justify-between items-end mb-2 border-b border-outline-variant pb-2">
                                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">Semester</span>
                                        <span class="font-label-sm text-label-sm text-on-surface-variant uppercase">GPA</span>
                                    </div>
                                    <!-- Trend Items -->
                                    <div class="flex items-center gap-4 group">
                                        <div class="w-16 font-label-md text-label-md text-on-surface-variant">Y3 S1</div>
                                        <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden flex items-center relative">
                                            <div class="absolute h-full bg-primary rounded-full" style="width: 85%;"></div>
                                        </div>
                                        <div class="w-10 text-right font-headline-md text-headline-md text-primary">1.25</div>
                                    </div>
                                    <div class="flex items-center gap-4 group mt-2">
                                        <div class="w-16 font-label-md text-label-md text-on-surface-variant">Y2 S2</div>
                                        <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden flex items-center relative">
                                            <div class="absolute h-full bg-primary-container rounded-full" style="width: 75%;"></div>
                                        </div>
                                        <div class="w-10 text-right font-headline-md text-headline-md text-on-surface">1.40</div>
                                    </div>
                                    <div class="flex items-center gap-4 group mt-2">
                                        <div class="w-16 font-label-md text-label-md text-on-surface-variant">Y2 S1</div>
                                        <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden flex items-center relative">
                                            <div class="absolute h-full bg-primary-container rounded-full" style="width: 70%;"></div>
                                        </div>
                                        <div class="w-10 text-right font-headline-md text-headline-md text-on-surface">1.55</div>
                                    </div>
                                    <div class="flex items-center gap-4 group mt-2">
                                        <div class="w-16 font-label-md text-label-md text-on-surface-variant">Y1 S2</div>
                                        <div class="flex-1 h-2 bg-surface-container rounded-full overflow-hidden flex items-center relative">
                                            <div class="absolute h-full bg-primary-container rounded-full" style="width: 80%;"></div>
                                        </div>
                                        <div class="w-10 text-right font-headline-md text-headline-md text-on-surface">1.35</div>
                                    </div>
                                </div>
                                <!-- Info Alert -->
                                <div class="bg-surface-container-low border border-surface-variant rounded-lg p-4 flex gap-3 mt-2">
                                    <span class="material-symbols-outlined text-status-info">info</span>
                                    <p class="font-label-sm text-label-sm text-on-surface-variant leading-relaxed">
                                        Your current trajectory indicates a high probability of graduating with <strong class="text-on-surface">Magna Cum Laude</strong> honors. Keep up the excellent work.
                                    </p>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Floating GWA Calculator -->
        <button id="npc-gwa-fab" data-tip="GWA Calculator"
                class="no-print fixed bottom-6 right-6 z-40 w-14 h-14 rounded-full bg-primary text-on-primary shadow-lg flex items-center justify-center ripple press hover:scale-105 transition-transform cursor-pointer npc-navy-card"
                aria-label="Open GWA calculator">
            <span class="material-symbols-outlined text-[24px]">calculate</span>
        </button>

    </main>
    </div>
    </div>

    <script>
        /* ── NPC dashboard enhancements v2 ───────────────────── */
        (function () {
            // Live clock
            var clockEl = document.getElementById('npc-live-clock');
            if (clockEl) {
                setInterval(function () {
                    clockEl.textContent = new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit' });
                }, 1000);
            }

            // Focus mode — dims sidebar/topbar chrome for distraction-free reading
            var focusBtn = document.getElementById('npc-focus-toggle');
            if (focusBtn) {
                var focused = false;
                focusBtn.addEventListener('click', function () {
                    focused = !focused;
                    document.documentElement.classList.toggle('npc-focus-mode', focused);
                    focusBtn.querySelector('.material-symbols-outlined').textContent = focused ? 'center_focus_weak' : 'center_focus_strong';
                    if (window.notify) window.notify(focused ? 'Focus mode ON — chrome dimmed.' : 'Focus mode OFF.', 'info', 2200);
                });
            }

            /* Notifications: handled by the universal npc.js center */
        })();
    </script>

    <script>
        /* ═══ GWA CALCULATOR — student feature ═══ */
        (function () {
            var fab = document.getElementById('npc-gwa-fab');
            if (!fab) return;

            var open = false;

            function escClose(e) { if (e.key === 'Escape') closeAll(); }
            function closeAll() {
                var m = document.getElementById('npc-gwa-modal');
                if (m) m.remove();
                document.removeEventListener('keydown', escClose);
                open = false;
            }

            fab.addEventListener('click', function () { if (!open) buildModal(); });

            function buildModal() {
                open = true;
                var m = document.createElement('div');
                m.id = 'npc-gwa-modal';
                m.className = 'npc-modal-backdrop';
                m.innerHTML =
                    '<div class="npc-modal-card max-w-lg" role="dialog" aria-label="GWA Calculator">' +
                    '  <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between bg-surface-subtle">' +
                    '    <h3 class="font-bold text-primary flex items-center gap-2"><span class="material-symbols-outlined">calculate</span> GWA Calculator</h3>' +
                    '    <button id="npc-gwa-close" class="p-1.5 rounded-full hover:bg-surface-container transition-colors cursor-pointer"><span class="material-symbols-outlined text-[20px]">close</span></button>' +
                    '  </div>' +
                    '  <div class="p-6 overflow-y-auto flex flex-col gap-4 text-sm">' +
                    '    <p class="text-xs text-on-surface-variant">Enter final grades per subject on the Philippine 5-point scale (lower is better). Untick a row to exclude it.</p>' +
                    '    <div class="overflow-x-auto -mx-1 px-1"><table class="w-full text-left"><thead><tr class="font-mono text-[10px] uppercase text-on-surface-variant tracking-wider">' +
                    '      <th class="pb-2">Subject</th><th class="pb-2 text-center">Final Grade</th><th class="pb-2 text-center">Units</th><th class="pb-2 text-center">Include</th><th></th>' +
                    '    </tr></thead><tbody id="npc-gwa-body"></tbody></table></div>' +
                    '    <button id="npc-gwa-addrow" class="self-start inline-flex items-center gap-1 text-xs font-bold text-primary hover:underline cursor-pointer"><span class="material-symbols-outlined text-[16px]">add_circle</span> Add subject</button>' +
                    '    <div class="rounded-2xl p-5 text-center" style="background:linear-gradient(135deg, rgb(var(--primary-rgb)/.08), rgb(var(--secondary-rgb)/.10));border:1px solid rgb(var(--outline-variant-rgb));">' +
                    '      <p class="font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Your General Weighted Average</p>' +
                    '      <p id="npc-gwa-value" class="text-5xl font-extrabold text-primary tabular-nums">—</p>' +
                    '      <p id="npc-gwa-honors" class="text-xs font-semibold mt-2 text-on-surface-variant">Enter grades to compute</p>' +
                    '    </div>' +
                    '  </div>' +
                    '  <div class="px-6 py-4 border-t border-outline-variant bg-surface-subtle flex justify-between items-center">' +
                    '    <button id="npc-gwa-reset" class="text-xs font-semibold text-error hover:underline cursor-pointer">Reset rows</button>' +
                    '    <button id="npc-gwa-done" class="bg-primary text-on-primary px-5 py-2.5 rounded-xl text-sm font-semibold hover:opacity-90 press cursor-pointer ripple npc-navy-card">Done</button>' +
                    '  </div>' +
                    '</div>';
                document.body.appendChild(m);

                var tbody = m.querySelector('#npc-gwa-body');
                var valEl = m.querySelector('#npc-gwa-value');
                var honorsEl = m.querySelector('#npc-gwa-honors');

                function addRow() {
                    var tr = document.createElement('tr');
                    tr.className = 'npc-gwa-row border-b border-outline-variant/40';
                    tr.innerHTML =
                        '<td class="px-2 py-1.5"><input class="npc-gwa-code w-full bg-surface-container-low border border-outline-variant rounded-lg px-2 py-1.5 text-xs font-mono" placeholder="e.g. CS311"></td>' +
                        '<td class="px-2 py-1.5"><input type="number" step="0.01" min="1" max="5" class="npc-gwa-grade w-full bg-surface-container-low border border-outline-variant rounded-lg px-2 py-1.5 text-xs font-mono text-center" placeholder="1.25"></td>' +
                        '<td class="px-2 py-1.5"><input type="number" step="0.5" min="0.5" max="12" class="npc-gwa-units w-full bg-surface-container-low border border-outline-variant rounded-lg px-2 py-1.5 text-xs font-mono text-center" placeholder="3.0"></td>' +
                        '<td class="px-2 py-1.5 text-center"><input type="checkbox" class="npc-gwa-inc w-4 h-4 accent-blue-600 cursor-pointer" checked></td>' +
                        '<td class="px-2 py-1.5 text-right"><button type="button" class="npc-gwa-del p-1 rounded hover:bg-error/10 text-error cursor-pointer opacity-60 hover:opacity-100 transition-opacity" title="Remove"><span class="material-symbols-outlined text-[16px]">delete</span></button></td>';
                    tbody.appendChild(tr);
                    tr.querySelector('.npc-gwa-del').addEventListener('click', function () { tr.remove(); compute(); });
                }

                function compute() {
                    var sum = 0, units = 0;
                    tbody.querySelectorAll('.npc-gwa-row').forEach(function (tr) {
                        if (!tr.querySelector('.npc-gwa-inc').checked) return;
                        var g = parseFloat(tr.querySelector('.npc-gwa-grade').value);
                        var u = parseFloat(tr.querySelector('.npc-gwa-units').value);
                        if (isNaN(g) || isNaN(u)) return;
                        sum += g * u; units += u;
                    });
                    if (units <= 0) {
                        valEl.textContent = '—';
                        honorsEl.textContent = 'Enter grades to compute';
                        honorsEl.className = 'text-xs font-semibold mt-2 text-on-surface-variant';
                        return;
                    }
                    var gwa = sum / units;
                    valEl.textContent = gwa.toFixed(2);
                    var msg, cls;
                    if (gwa <= 1.20) { msg = 'Summa cum laude trajectory! Outstanding.'; cls = 'text-status-success'; }
                    else if (gwa <= 1.45) { msg = 'Magna cum laude trajectory — excellent!'; cls = 'text-status-success'; }
                    else if (gwa <= 1.75) { msg = 'Cum laude territory — keep it up!'; cls = 'text-primary'; }
                    else if (gwa <= 3.00) { msg = 'Passing — steady progress.'; cls = 'text-on-surface-variant'; }
                    else { msg = 'Below passing line — consult your adviser.'; cls = 'text-error'; }
                    honorsEl.textContent = msg;
                    honorsEl.className = 'text-xs font-semibold mt-2 ' + cls;
                    if (gwa <= 1.45 && !compute.__celebrated) {
                        compute.__celebrated = true;
                        if (window.npcConfetti) window.npcConfetti.burst(90);
                    }
                }

                m.addEventListener('input', compute);
                m.addEventListener('change', compute);
                m.querySelector('#npc-gwa-addrow').addEventListener('click', addRow);
                m.querySelector('#npc-gwa-close').addEventListener('click', closeAll);
                m.querySelector('#npc-gwa-done').addEventListener('click', closeAll);
                m.querySelector('#npc-gwa-reset').addEventListener('click', function () {
                    tbody.innerHTML = '';
                    for (var i = 0; i < 5; i++) addRow();
                    compute();
                });
                m.addEventListener('click', function (e) { if (e.target === m) closeAll(); });
                document.addEventListener('keydown', escClose);

                for (var i = 0; i < 5; i++) addRow();
            }
        })();
    </script>

    <script src="app.js?v=<?= filemtime(__DIR__ . '/app.js') ?>"></script>
</body>

</html>
