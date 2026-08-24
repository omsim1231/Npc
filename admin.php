<?php
require_once 'auth.php';
require_admin();
$admin_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Administrator';
$admin_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : 'admin@navotaspolytechniccollege.edu.ph';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$jsConfig = getJsConfig();
$csrf_token = getCsrfToken();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>NPC Connect - Admin Dashboard</title>
    <script>
        /* Pre-paint theme: apply saved night-mode before first paint (no flash) */
        (function () {
            try {
                var t = localStorage.getItem('npc-theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "rgb(var(--primary-rgb) / <alpha-value>)",
                        "primary-container": "rgb(var(--primary-container-rgb) / <alpha-value>)",
                        "on-primary": "rgb(var(--on-primary-rgb) / <alpha-value>)",
                        "on-primary-container": "rgb(var(--on-primary-container-rgb) / <alpha-value>)",
                        "primary-fixed": "rgb(var(--primary-fixed-rgb) / <alpha-value>)",
                        "primary-fixed-dim": "rgb(var(--primary-fixed-dim-rgb) / <alpha-value>)",
                        "on-primary-fixed": "rgb(var(--on-primary-fixed-rgb) / <alpha-value>)",
                        "on-primary-fixed-variant": "rgb(var(--on-primary-fixed-variant-rgb) / <alpha-value>)",
                        "secondary": "rgb(var(--secondary-rgb) / <alpha-value>)",
                        "secondary-container": "rgb(var(--secondary-container-rgb) / <alpha-value>)",
                        "on-secondary": "rgb(var(--on-secondary-rgb) / <alpha-value>)",
                        "on-secondary-container": "rgb(var(--on-secondary-container-rgb) / <alpha-value>)",
                        "secondary-fixed": "rgb(var(--secondary-fixed-rgb) / <alpha-value>)",
                        "secondary-fixed-dim": "rgb(var(--secondary-fixed-dim-rgb) / <alpha-value>)",
                        "on-secondary-fixed": "rgb(var(--on-secondary-fixed-rgb) / <alpha-value>)",
                        "on-secondary-fixed-variant": "rgb(var(--on-secondary-fixed-variant-rgb) / <alpha-value>)",
                        "npc-gold-muted": "rgb(var(--npc-gold-muted-rgb) / <alpha-value>)",
                        "tertiary": "rgb(var(--tertiary-rgb) / <alpha-value>)",
                        "on-tertiary": "rgb(var(--on-tertiary-rgb) / <alpha-value>)",
                        "tertiary-container": "rgb(var(--tertiary-container-rgb) / <alpha-value>)",
                        "tertiary-fixed": "rgb(var(--tertiary-fixed-rgb) / <alpha-value>)",
                        "tertiary-fixed-dim": "rgb(var(--tertiary-fixed-dim-rgb) / <alpha-value>)",
                        "on-tertiary-fixed": "rgb(var(--on-tertiary-fixed-rgb) / <alpha-value>)",
                        "on-tertiary-fixed-variant": "rgb(var(--on-tertiary-fixed-variant-rgb) / <alpha-value>)",
                        "background": "rgb(var(--background-rgb) / <alpha-value>)",
                        "surface": "rgb(var(--surface-rgb) / <alpha-value>)",
                        "surface-subtle": "rgb(var(--surface-subtle-rgb) / <alpha-value>)",
                        "surface-bright": "rgb(var(--surface-bright-rgb) / <alpha-value>)",
                        "surface-dim": "rgb(var(--surface-dim-rgb) / <alpha-value>)",
                        "surface-tint": "rgb(var(--surface-tint-rgb) / <alpha-value>)",
                        "surface-variant": "rgb(var(--surface-variant-rgb) / <alpha-value>)",
                        "surface-container-lowest": "rgb(var(--surface-container-lowest-rgb) / <alpha-value>)",
                        "surface-container-low": "rgb(var(--surface-container-low-rgb) / <alpha-value>)",
                        "surface-container": "rgb(var(--surface-container-rgb) / <alpha-value>)",
                        "surface-container-high": "rgb(var(--surface-container-high-rgb) / <alpha-value>)",
                        "surface-container-highest": "rgb(var(--surface-container-highest-rgb) / <alpha-value>)",
                        "on-surface": "rgb(var(--on-surface-rgb) / <alpha-value>)",
                        "on-surface-variant": "rgb(var(--on-surface-variant-rgb) / <alpha-value>)",
                        "outline": "rgb(var(--outline-rgb) / <alpha-value>)",
                        "outline-variant": "rgb(var(--outline-variant-rgb) / <alpha-value>)",
                        "status-info": "rgb(var(--status-info-rgb) / <alpha-value>)",
                        "status-success": "rgb(var(--status-success-rgb) / <alpha-value>)",
                        "status-warning": "rgb(var(--status-warning-rgb) / <alpha-value>)",
                        "error": "rgb(var(--error-rgb) / <alpha-value>)",
                        "on-error": "rgb(var(--on-error-rgb) / <alpha-value>)",
                        "error-container": "rgb(var(--error-container-rgb) / <alpha-value>)",
                        "on-error-container": "rgb(var(--on-error-container-rgb) / <alpha-value>)",
                        "inverse-surface": "rgb(var(--inverse-surface-rgb) / <alpha-value>)",
                        "inverse-on-surface": "rgb(var(--inverse-on-surface-rgb) / <alpha-value>)",
                        "inverse-primary": "rgb(var(--inverse-primary-rgb) / <alpha-value>)",
                        "excel-green": "rgb(var(--excel-green-rgb) / <alpha-value>)",
                        "excel-dark": "rgb(var(--excel-dark-rgb) / <alpha-value>)",
                        "excel-light": "rgb(var(--excel-light-rgb) / <alpha-value>)",
                        "excel-border": "rgb(var(--excel-border-rgb) / <alpha-value>)"
                    },
                    fontFamily: {
                        "sans": ["Geist", "sans-serif"],
                        "mono": ["JetBrains Mono", "monospace"]
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/styles.css') ?>">
    <script src="npc.js?v=<?= filemtime(__DIR__ . '/npc.js') ?>"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-surface lg:pl-64" id="main-wrapper">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Admin</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">Administrative Master Panel</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                        <?= htmlspecialchars($admin_initial) ?>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-sm font-semibold text-primary leading-tight"><?= htmlspecialchars($admin_name) ?></p>
                        <p class="text-xs text-on-surface-variant font-mono">Administrator</p>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Content -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-8 flex-1">
            <div>
                <h1 class="text-2xl font-bold text-primary mb-1">Administrative Overview</h1>
                <p class="text-sm text-on-surface-variant">Live metrics, pending approvals, student services, and security status for Navotas Polytechnic College.</p>
            </div>

            <!-- Global Search + Quick Post -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 relative">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 px-5 py-4">
                        <span class="material-symbols-outlined text-on-surface-variant text-[22px]">search</span>
                        <input type="text" id="npc-global-search" autocomplete="off"
                               placeholder="Search students, faculty, classes… (press / to focus)"
                               class="flex-1 bg-transparent border-none outline-none text-sm text-on-surface placeholder:text-on-surface-variant/60 focus:ring-0" />
                        <span class="kbd">/</span>
                    </div>
                    <div id="npc-global-results" class="npc-popover hidden" style="left:0;right:0;min-width:0;">
                        <div id="npc-global-results-list" class="max-h-80 overflow-y-auto"></div>
                    </div>
                </div>
                <a href="admin_announcements.php" class="npc-card press bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex items-center gap-3 px-5 py-4 group">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">edit_note</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-on-surface">Quick Post</p>
                        <p class="text-[11px] font-mono text-on-surface-variant truncate">New campus announcement →</p>
                    </div>
                </a>
            </div>

            <!-- Metrics Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Metric 1: Total Students -->
                <div class="npc-card tilt bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-mono font-semibold text-on-surface-variant uppercase tracking-wider">Registered Students</span>
                        <div class="w-9 h-9 rounded-xl bg-primary-container text-on-primary flex items-center justify-center npc-navy-card">
                            <span class="material-symbols-outlined text-[20px]">group</span>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-primary" id="metric-total-students">0</div>
                    <div class="mt-3 flex items-center gap-1 text-xs text-on-surface-variant">
                        <a href="admin_students.php" class="text-primary font-semibold hover:underline flex items-center gap-1">
                            Manage directory <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Metric 2: Pending Grade Approvals -->
                <div class="npc-card tilt bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-mono font-semibold text-on-surface-variant uppercase tracking-wider">Pending Grade Approvals</span>
                        <div class="w-9 h-9 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">verified</span>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-primary" id="metric-pending-grades">0</div>
                    <div class="mt-3 flex items-center gap-1 text-xs text-on-surface-variant">
                        <a href="admin_grades.php" class="text-primary font-semibold hover:underline flex items-center gap-1">
                            Review grade sheets <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Metric 3: Active Classes -->
                <div class="npc-card tilt bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-mono font-semibold text-on-surface-variant uppercase tracking-wider">Active Classes</span>
                        <div class="w-9 h-9 rounded-xl bg-surface-container-high text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">school</span>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-primary" id="metric-total-classes">0</div>
                    <div class="mt-3 flex items-center gap-1 text-xs text-on-surface-variant">
                        <a href="admin_classes.php" class="text-primary font-semibold hover:underline flex items-center gap-1">
                            Manage schedules <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>

                <!-- Metric 4: Pending Document Requests -->
                <div class="npc-card tilt bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-mono font-semibold text-on-surface-variant uppercase tracking-wider">Pending Document Requests</span>
                        <div class="w-9 h-9 rounded-xl bg-primary-fixed text-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-[20px]">receipt_long</span>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-primary" id="metric-pending-docs">0</div>
                    <div class="mt-3 flex items-center gap-1 text-xs text-on-surface-variant">
                        <a href="admin_settings.php" class="text-primary font-semibold hover:underline flex items-center gap-1">
                            Process requests <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Action Banner -->
            <div class="bg-primary text-on-primary rounded-2xl p-6 shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4 relative overflow-hidden npc-navy-card">
                <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-1">Administrative Control Center</h3>
                    <p class="text-sm text-on-primary-container max-w-xl">Review and approve faculty grade sheets, assign course loads, verify document requests, and manage security audit logs.</p>
                </div>
                <div class="flex items-center gap-3 relative z-10 shrink-0">
                    <a href="admin_grades.php" class="bg-secondary-container text-on-secondary-container font-semibold px-4 py-2.5 rounded-xl hover:opacity-90 transition-opacity text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">verified</span>
                        Grade Approvals
                    </a>
                    <a href="admin_students.php" class="bg-white/10 text-white font-semibold px-4 py-2.5 rounded-xl hover:bg-white/20 transition-all text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">group_add</span>
                        Manage Users
                    </a>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <section class="grid grid-cols-2 lg:grid-cols-5 gap-4" aria-label="Quick actions">
                <a href="admin_grades.php" class="npc-card npc-tile ripple press bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">verified</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Approve Grades</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Review &amp; post</p>
                    </div>
                </a>
                <a href="admin_students.php" class="npc-card npc-tile ripple press bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center npc-navy-card">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">person_add</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Enroll Student</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Directory &amp; imports</p>
                    </div>
                </a>
                <a href="admin_schedules.php" class="npc-card npc-tile ripple press bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-high text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">event_upcoming</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Build Schedules</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Import &amp; assign</p>
                    </div>
                </a>
                <a href="admin_announcements.php" class="npc-card npc-tile ripple press bg-surface-container-lowest border border-outline-variant rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-status-info/15 text-status-info flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">campaign</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Broadcast News</p>
        <p class="text-[11px] font-mono text-on-surface-variant">Campus bulletins</p>
                    </div>
                </a>
                <a href="admin_docs.php" class="npc-card npc-tile ripple press bg-primary text-on-primary rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 group npc-navy-card">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px] text-secondary-container">smart_toy</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Train Campus AI</p>
                        <p class="text-[11px] font-mono opacity-70">Knowledge base</p>
                    </div>
                </a>
            </section>

            <!-- System Health Monitor -->
            <section class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 md:p-6 shadow-sm" aria-label="System health">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-status-success text-[20px] pulse-dot-host">monitor_heart</span>
                        System Health Monitor
                    </h3>
                    <div class="eq calm" aria-hidden="true"><span></span><span></span><span></span><span></span><span></span></div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="rounded-xl border border-outline-variant/60 p-3.5 bg-surface-subtle flex flex-col gap-2">
                        <div class="hm-pill ok"><span class="dot"></span> Operational</div>
                        <p class="text-xs text-on-surface-variant font-semibold">Portal Web Server</p>
                    </div>
                    <div class="rounded-xl border border-outline-variant/60 p-3.5 bg-surface-subtle flex flex-col gap-2">
                        <div class="hm-pill ok"><span class="dot"></span> Connected</div>
                        <p class="text-xs text-on-surface-variant font-semibold">Database Cluster</p>
                    </div>
                    <div class="rounded-xl border border-outline-variant/60 p-3.5 bg-surface-subtle flex flex-col gap-2">
                        <div id="npc-health-ai" class="hm-pill warn"><span class="dot"></span> Checking…</div>
                        <p class="text-xs text-on-surface-variant font-semibold">AI Knowledge Engine</p>
                    </div>
                    <div class="rounded-xl border border-outline-variant/60 p-3.5 bg-surface-subtle flex flex-col gap-2">
                        <div class="flex items-baseline gap-1">
                            <span id="npc-health-storage" class="text-lg font-extrabold text-primary tabular-nums">—</span>
                            <span class="text-[10px] font-mono text-on-surface-variant">% storage used</span>
                        </div>
                        <div class="h-1.5 rounded-full bg-surface-container overflow-hidden">
                            <div id="npc-health-bar" class="h-full rounded-full transition-all duration-1000 ease-out" style="width:0%;background:linear-gradient(90deg, rgb(var(--status-success-rgb)), rgb(var(--status-info-rgb)));"></div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Recent Security & System Audit Logs -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">security</span>
                        System Security & Activity Logs
                    </h3>
                    <span class="text-xs font-mono text-on-surface-variant bg-surface-container px-2.5 py-1 rounded-md border border-outline-variant">Live Audit Feed</span>
                </div>

                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-surface-container-low font-mono text-xs text-on-surface uppercase tracking-wider">
                                    <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Action / Event</th>
                                    <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">User / Account</th>
                                    <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Timestamp</th>
                                    <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Severity</th>
                                </tr>
                            </thead>
                            <tbody id="security-logs-tbody" class="divide-y divide-outline-variant/30">
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-on-surface-variant">Loading system audit records...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        async function loadAdminStats() {
            try {
                const res = await fetch('api_admin.php?action=get_dashboard_metrics');
                const data = await res.json();

                if (data.success) {
                    /* Count-up animation on the four metric tiles */
                    const setMetric = (id, val) => {
                        const el = document.getElementById(id);
                        if (!el) return;
                        el.setAttribute('data-countup', String(val ?? 0));
                        if (window.npcCountUp) window.npcCountUp(el); else el.innerText = val ?? 0;
                    };
                    setMetric('metric-total-students', data.metrics.total_students);
                    setMetric('metric-pending-grades', data.metrics.pending_grades);
                    setMetric('metric-total-classes', data.metrics.active_classes);
                    setMetric('metric-pending-docs', data.metrics.pending_docs);

                    const tbody = document.getElementById('security-logs-tbody');
                    const logs = data.recent_logs || [];

                    if (logs.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-gray-400">No security events logged yet.</td></tr>';
                    } else {
                    tbody.innerHTML = logs.map(l => `
                        <tr class="hover:bg-surface-subtle transition-colors">
                            <td class="py-3.5 px-6 font-medium text-primary flex items-center gap-2">
                                <span class="material-symbols-outlined text-status-info text-sm">shield</span>
                                ${l.event_type || 'SYSTEM_EVENT'}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs text-on-surface-variant">${l.user_email || 'system'}</td>
                            <td class="py-3.5 px-6 text-on-surface-variant text-xs">${new Date(l.created_at).toLocaleString()}</td>
                            <td class="py-3.5 px-6">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold ${l.severity === 'High' ? 'bg-red-100 text-error' : (l.severity === 'Medium' ? 'bg-amber-100 text-amber-800' : 'bg-surface-container text-primary')}">
                                    ${l.severity || 'Low'}
                                </span>
                            </td>
                        </tr>
                    `).join('');
                    }
                }
            } catch (err) {
                console.error('Error loading admin stats:', err);
            }
        }

        loadAdminStats();

        /* ═══ Global Search (/) — searches students & faculty via list_users ═══ */
        (function () {
            const input = document.getElementById('npc-global-search');
            const box = document.getElementById('npc-global-results');
            const list = document.getElementById('npc-global-results-list');
            if (!input || !box || !list) return;
            let USERS = [];
            let loaded = false;

            async function ensureUsers() {
                if (loaded) return;
                try {
                    const res = await fetch('api_admin.php?action=list_users', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const data = await res.json();
                    if (data.success) { USERS = data.users || []; loaded = true; }
                } catch (e) { /* offline */ }
            }

            function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

            function render(q) {
                const needle = q.toLowerCase().trim();
                if (!needle) { box.classList.add('hidden'); return; }
                const hits = USERS.filter(u =>
                    [u.full_name, u.email, u.student_number].join(' ').toLowerCase().includes(needle)
                ).slice(0, 8);
                if (!hits.length) {
                    list.innerHTML = '<div class="p-5 text-center text-xs text-on-surface-variant">No accounts matching "' + esc(q) + '"</div>';
                } else {
                    list.innerHTML = hits.map(u =>
                        '<a href="admin_students.php" class="flex items-center gap-3 px-4 py-2.5 hover:bg-surface-container-low transition-colors">' +
                        '<span class="material-symbols-outlined text-[18px] text-on-surface-variant">' + (u.role === 'student' ? 'school' : (u.role === 'teacher' ? 'cast_for_education' : 'shield_person')) + '</span>' +
                        '<div class="min-w-0"><p class="text-sm font-semibold text-on-surface truncate">' + esc(u.full_name) + '</p>' +
                        '<p class="text-[11px] font-mono text-on-surface-variant truncate">' + esc(u.email) + '</p></div>' +
                        '<span class="ml-auto text-[10px] font-mono uppercase font-bold text-outline">' + esc(u.role) + '</span></a>'
                    ).join('');
                }
                box.classList.remove('hidden');
            }

            input.addEventListener('focus', ensureUsers);
            input.addEventListener('input', e => render(e.target.value));
            input.addEventListener('keydown', e => { if (e.key === 'Escape') { box.classList.add('hidden'); input.blur(); } });
            document.addEventListener('click', e => { if (!box.contains(e.target) && e.target !== input) box.classList.add('hidden'); });
            document.addEventListener('keydown', e => {
                if (e.key === '/' && document.activeElement !== input &&
                    !/INPUT|TEXTAREA|SELECT/.test(document.activeElement.tagName)) {
                    e.preventDefault(); input.focus();
                }
            });
        })();

        /* ── System Health Monitor wiring ─────────────────────── */
        (function () {
            // AI knowledge engine reachability probe
            fetch('http://localhost:8000/documents', { method: 'GET' }).then(r => {
                const pill = document.getElementById('npc-health-ai');
                if (!pill) return;
                if (r.ok) { pill.className = 'hm-pill ok'; pill.innerHTML = '<span class="dot"></span> Online'; }
                else throw 0;
            }).catch(() => {
                const pill = document.getElementById('npc-health-ai');
                if (pill) { pill.className = 'hm-pill bad'; pill.innerHTML = '<span class="dot"></span> Offline'; }
            });

            // Simulated storage utilization gauge (replace with a real API when available)
            var pct = Math.round(38 + Math.random() * 22);
            var barEl = document.getElementById('npc-health-bar');
            var numEl = document.getElementById('npc-health-storage');
            setTimeout(function () {
                if (numEl) numEl.textContent = pct;
                if (barEl) barEl.style.width = pct + '%';
            }, 350);
        })();
    </script>
</body>
</html>
