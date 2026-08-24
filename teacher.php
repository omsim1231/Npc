<?php
require_once 'auth.php';
require_teacher();
$teacher_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Faculty Professor';
$teacher_initial = strtoupper(substr($teacher_name, 0, 1));
$teacher_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$is_admin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'registrar');
$csrf_token = getCsrfToken();
$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Faculty Portal - NPC Connect</title>
    <!-- Tailwind CSS CDN -->
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
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/styles.css') ?>">

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
    <script src="npc.js?v=<?= filemtime(__DIR__ . '/npc.js') ?>"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'faculty'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 lg:pl-64 bg-surface min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Faculty</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">Faculty Dashboard & Consultation Hub</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                    <?= htmlspecialchars($teacher_initial) ?>
                </div>
                <span class="text-sm font-semibold text-primary hidden sm:inline"><?= htmlspecialchars($teacher_name) ?></span>
            </div>
        </header>

        <!-- Canvas -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-8 flex-1">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-outline-variant/60 pb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold tracking-tight">
                        <span class="text-shimmer text-primary">Welcome, Prof. <?= htmlspecialchars(explode(' ', trim($teacher_name))[0]) ?></span>
                        <span class="inline-block align-middle ml-1.5 animate-float material-symbols-outlined text-[24px] text-secondary" aria-hidden="true">waving_hand</span>
                    </h1>
                    <p class="text-sm text-on-surface-variant mt-1">Manage class attendance, encode semester grades, publish class materials, and review student appointments.</p>
                    <p class="font-mono text-[11px] text-outline uppercase tracking-wider mt-2 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[14px] text-status-success">schedule</span>
                        <span id="npc-live-clock"><?= date('l, M j · g:i:s A') ?></span>
                    </p>
                </div>
                <div class="flex items-center gap-3 shrink-0">
                    <a href="teacher_attendance.php" class="ripple press bg-primary text-on-primary px-4 py-2.5 rounded-xl text-xs font-semibold hover:opacity-90 flex items-center gap-2 shadow-sm transition-all hover:-translate-y-0.5 npc-navy-card">
                        <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                        Start QR Attendance
                    </a>
                    <a href="teacher_grades.php" class="bg-[#107c41] text-white px-4 py-2.5 rounded-xl text-xs font-semibold hover:bg-[#0c5d31] flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">grade</span>
                        Open Gradebook
                    </a>
                </div>
            </div>

            <!-- Bento Stats -->
            <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="npc-card tilt bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm">
                    <span class="text-xs font-mono font-semibold uppercase text-on-surface-variant">Active Class Load</span>
                    <h3 class="text-3xl font-bold text-primary mt-2 tabular-nums" id="teacher-class-count" data-countup="0">0</h3>
                    <p class="text-xs text-on-surface-variant mt-1">Assigned lecture &amp; lab sections</p>
                </div>

                <div class="npc-card tilt bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm">
                    <span class="text-xs font-mono font-semibold uppercase text-on-surface-variant">Pending Consultations</span>
                    <h3 class="text-3xl font-bold text-amber-600 mt-2 tabular-nums" id="teacher-consult-count" data-countup="0">0</h3>
                    <p class="text-xs text-on-surface-variant mt-1">Student appointment requests</p>
                </div>

                <div class="bg-primary text-on-primary p-6 rounded-2xl shadow-md npc-navy-card">
                    <span class="text-xs font-mono font-semibold uppercase text-on-primary-container">Academic Term</span>
                    <h3 class="text-2xl font-bold text-white mt-2 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary-container">school</span> AY 2026-2027
                    </h3>
                    <p class="text-xs text-on-primary-container mt-1">1st Semester • In Progress</p>
                </div>
            </section>

            <!-- Quick Actions -->
            <section class="grid grid-cols-2 lg:grid-cols-5 gap-4" aria-label="Quick actions">
                <a href="teacher_attendance.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex flex-col items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center npc-navy-card">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">qr_code_scanner</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Take Attendance</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Live QR session</p>
                    </div>
                </a>
                <a href="teacher_grades.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex flex-col items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#107c41]/10 text-[#107c41] flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">grade</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Encode Grades</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Gradebook</p>
                    </div>
                </a>
                <a href="teacher_classes.php" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex flex-col items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">group</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Class Rosters</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Student lists</p>
                    </div>
                </a>
                <a href="#material-upload-form" class="npc-card npc-tile ripple press bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex flex-col items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-status-info/15 text-status-info flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px]">upload_file</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-on-surface">Share Materials</p>
                        <p class="text-[11px] font-mono text-on-surface-variant">Upload handouts</p>
                    </div>
                </a>
                <a href="teacher_ai_assistant.php" class="npc-card npc-tile ripple press bg-primary text-on-primary rounded-2xl p-4 shadow-sm flex flex-col items-start gap-3 npc-navy-card">
                    <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                        <span class="material-symbols-outlined npc-tile-icon text-[20px] text-secondary-container">auto_awesome</span>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Teaching AI</p>
                        <p class="text-[11px] font-mono opacity-70">Lesson prep help</p>
                    </div>
                </a>
            </section>

            <!-- Today's Classes (live widget) -->
            <section class="bg-primary text-on-primary rounded-2xl p-6 shadow-md relative overflow-hidden npc-navy-card" aria-label="Today's classes">
                <div class="absolute -right-12 -top-12 w-48 h-48 rounded-full blur-3xl opacity-25 pointer-events-none" style="background:radial-gradient(circle, rgba(254,212,136,.8), transparent 70%);"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-base flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-container text-[20px]">today</span>
                            Today's Classes
                        </h3>
                        <span id="npc-today-date" class="font-mono text-[11px] uppercase tracking-wider text-on-primary-container"></span>
                    </div>
                    <div id="npc-today-classes" class="flex flex-col gap-2.5">
                        <p class="text-xs text-on-primary-container animate-pulse">Checking today's schedule…</p>
                    </div>
                </div>
            </section>

            <!-- Assigned Classes Grid -->
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[20px]">menu_book</span>
                        Your Assigned Classes & Sections
                    </h2>
                    <a href="teacher_classes.php" class="text-xs text-primary font-bold hover:underline">View Student Rosters →</a>
                </div>

                <div id="teacher-classes-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div class="col-span-full bg-surface-container-lowest border border-outline-variant rounded-2xl p-12 text-center text-on-surface-variant">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">hourglass_empty</span>
                        <p class="font-semibold text-lg">Loading your assigned classes...</p>
                    </div>
                </div>
            </div>

            <!-- Campus Bulletins (announcements feed) -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm">
                <div class="pb-4 border-b border-outline-variant/60 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[22px]" style="font-variation-settings: 'FILL' 1;">campaign</span>
                        <span>Campus Bulletins</span>
                    </h3>
                    <span class="font-mono text-xs text-on-surface-variant font-semibold bg-surface-container px-2 py-0.5 rounded">Live</span>
                </div>
                <div class="pt-4 flex flex-col gap-4" id="faculty-announcements-container">
                    <div class="text-center text-on-surface-variant text-sm py-4 animate-pulse">Loading announcements...</div>
                </div>
            </div>

            <!-- Student Consultation Appointments & Class Materials Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left: Consultation Appointments (7 cols) -->
                <div class="lg:col-span-7 bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-outline-variant/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">calendar_clock</span>
                            <h3 class="font-bold text-primary text-base">Student Consultation Schedule</h3>
                        </div>
                        <span class="text-xs font-mono text-gray-500">Live Requests</span>
                    </div>

                    <div id="consultations-list" class="space-y-3">
                        <p class="text-xs text-gray-400 p-4 text-center">Loading consultation appointments...</p>
                    </div>
                </div>

                <!-- Right: Secure Class Materials Upload (5 cols) -->
                <div class="lg:col-span-5 bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-outline-variant/60 pb-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">upload_file</span>
                            <h3 class="font-bold text-primary text-base">Class Materials & Syllabi</h3>
                        </div>
                    </div>

                    <form id="material-upload-form" onsubmit="uploadClassMaterial(event)" class="space-y-3 text-xs">
                        <div>
                            <label class="block font-bold text-gray-700 mb-1">Document Title:</label>
                            <input type="text" id="mat-title" required placeholder="e.g. DM103 Course Syllabus & Guidelines" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs">
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">Category:</label>
                                <select id="mat-category" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs">
                                    <option value="Syllabus">Syllabus</option>
                                    <option value="Lecture Notes">Lecture Notes</option>
                                    <option value="Assignment">Assignment</option>
                                    <option value="Exam Guide">Exam Guide</option>
                                </select>
                            </div>
                            <div>
                                <label class="block font-bold text-gray-700 mb-1">File (PDF, DOC, PPT, XLS):</label>
                                <input type="file" id="mat-file" required class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-container">
                            </div>
                        </div>

                        <button type="submit" id="btn-upload-mat" class="w-full py-2.5 bg-primary text-white rounded-xl font-bold text-xs hover:bg-primary-container shadow-sm flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">cloud_upload</span> Upload to Portal
                        </button>
                    </form>

                    <div class="border-t border-outline-variant/40 pt-3">
                        <h4 class="font-bold text-xs text-gray-700 mb-2">Uploaded Materials:</h4>
                        <div id="materials-list" class="space-y-2 max-h-48 overflow-y-auto text-xs">
                            <p class="text-gray-400">Loading files...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-2">
        <span class="material-symbols-outlined text-[18px] text-emerald-400" id="toast-icon">check_circle</span>
        <span id="toast-message">Notification</span>
    </div>

    <script>
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        const currentTeacherName = <?= json_encode($teacher_name) ?>;
        const currentTeacherEmail = <?= json_encode($teacher_email) ?>;
        const isAdmin = <?= json_encode($is_admin) ?>;
        const csrfToken = <?= json_encode($csrf_token) ?>;

        async function loadTeacherDashboard() {
            const grid = document.getElementById('teacher-classes-grid');
            try {
                const { data: classes, error } = await supabaseClient.from('classes').select('*').order('code', { ascending: true });
                if (error) throw error;

                const myEmail = (currentTeacherEmail || '').toLowerCase().trim();
                const myName = (currentTeacherName || '').toLowerCase().trim();

                let myClasses = [];
                if (isAdmin) {
                    myClasses = classes || [];
                } else {
                    myClasses = (classes || []).filter(c => {
                        const cEmail = (c.instructor_email || c.created_by_email || '').toLowerCase().trim();
                        const cInst = (c.instructor || '').toLowerCase().trim();
                        return (cEmail && cEmail === myEmail) || (cInst && myName && cInst.includes(myName));
                    });
                    if (myClasses.length === 0 && classes) myClasses = classes;
                }

                const classCountEl = document.getElementById('teacher-class-count');
                const total = myClasses.length;
                if (classCountEl) {
                    classCountEl.setAttribute('data-countup', String(total));
                    if (window.npcCountUp) window.npcCountUp(classCountEl); else classCountEl.innerText = total;
                }
                renderTodayClasses(myClasses);

                if (myClasses.length === 0) {
                    grid.innerHTML = `
                        <div class="col-span-full bg-surface-container-lowest border border-outline-variant rounded-2xl p-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">school</span>
                            <p class="font-semibold text-lg">No assigned classes found</p>
                        </div>
                    `;
                } else {
                    grid.innerHTML = myClasses.map(c => `
                        <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 hover:shadow-md transition-shadow flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span class="px-2.5 py-0.5 bg-primary/10 text-primary font-mono text-xs font-bold rounded-lg">${c.code}</span>
                                    <span class="text-xs font-mono font-bold bg-surface-container-low text-on-surface px-2 py-0.5 rounded">${c.section || 'AIS 2A'}</span>
                                </div>
                                <h3 class="font-bold text-sm text-primary mb-2">${c.title}</h3>
                                <div class="space-y-1 text-xs text-on-surface-variant font-medium">
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-status-info">schedule</span>
                                        <span>${c.schedule_day || 'TBA'} (${c.start_time || 'TBA'}${c.end_time ? ' - ' + c.end_time : ''})</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="material-symbols-outlined text-[15px] text-secondary">location_on</span>
                                        <span>${c.room || 'Room TBA'}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-5 pt-3 border-t border-outline-variant/30 flex items-center justify-between gap-2">
                                <a href="teacher_attendance.php?class_id=${c.id}" class="flex-1 bg-primary text-on-primary py-2 rounded-xl text-center text-xs font-bold hover:opacity-90 flex items-center justify-center gap-1 shadow-xs npc-navy-card">
                                    <span class="material-symbols-outlined text-[14px]">qr_code_scanner</span> Live QR
                                </a>
                                <a href="teacher_grades.php?class_id=${c.id}" class="flex-1 bg-[#107c41] text-white py-2 rounded-xl text-center text-xs font-bold hover:bg-[#0c5d31] flex items-center justify-center gap-1 shadow-xs">
                                    <span class="material-symbols-outlined text-[14px]">grade</span> Grades
                                </a>
                            </div>
                        </div>
                    `).join('');
                }

                await loadConsultations();
                await loadMaterials();

            } catch (err) {
                console.error(err);
            }
        }

        async function loadConsultations() {
            try {
                const res = await fetch('api_faculty.php?action=get_consultations');
                const data = await res.json();
                const list = document.getElementById('consultations-list');

                if (data.success && data.appointments && data.appointments.length > 0) {
                    const pendingCount = data.appointments.filter(a => a.status === 'Pending').length;
                    document.getElementById('teacher-consult-count').innerText = pendingCount;

                    list.innerHTML = data.appointments.map(a => `
                        <div class="p-3 bg-surface rounded-xl border border-outline-variant/60 flex items-center justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-primary">${a.student_name}</span>
                                    <span class="font-mono text-[11px] text-gray-500">(${a.student_number})</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold ${a.status === 'Confirmed' ? 'bg-emerald-100 text-emerald-800' : (a.status === 'Declined' ? 'bg-red-100 text-error' : 'bg-amber-100 text-amber-800')}">${a.status}</span>
                                </div>
                                <p class="text-xs text-gray-600 mt-0.5"><strong>Topic:</strong> ${a.topic} • <strong>Date:</strong> ${a.requested_date} @ ${a.requested_time}</p>
                            </div>
                            ${a.status === 'Pending' ? `
                                <div class="flex items-center gap-1">
                                    <button onclick="updateConsultationStatus('${a.id}', 'Confirmed')" class="px-2.5 py-1 bg-emerald-600 text-white rounded-lg font-bold text-[11px]">Confirm</button>
                                    <button onclick="updateConsultationStatus('${a.id}', 'Declined')" class="px-2.5 py-1 bg-red-600 text-white rounded-lg font-bold text-[11px]">Decline</button>
                                </div>
                            ` : ''}
                        </div>
                    `).join('');
                } else {
                    document.getElementById('teacher-consult-count').innerText = '0';
                    list.innerHTML = '<p class="text-xs text-gray-400 p-4 text-center">No student consultation requests pending.</p>';
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function updateConsultationStatus(id, status) {
            const note = prompt(`Enter notes for ${status}:`, '');
            if (note === null) return;

            try {
                const res = await fetch('api_faculty.php?action=update_consultation_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ appointment_id: id, status: status, notes: note, csrf_token: csrfToken })
                });
                const data = await res.json();
                if (data.success) {
                    await loadConsultations();
                }
            } catch (err) {
                alert(err.message);
            }
        }

        async function uploadClassMaterial(e) {
            e.preventDefault();
            const title = document.getElementById('mat-title').value.trim();
            const category = document.getElementById('mat-category').value;
            const fileInput = document.getElementById('mat-file');

            if (!fileInput.files[0]) return alert('Please choose a file.');

            const formData = new FormData();
            formData.append('title', title);
            formData.append('category', category);
            formData.append('file', fileInput.files[0]);
            formData.append('csrf_token', csrfToken);

            document.getElementById('btn-upload-mat').disabled = true;
            document.getElementById('btn-upload-mat').innerText = 'Uploading...';

            try {
                const res = await fetch('api_faculty.php?action=upload_material', {
                    method: 'POST',
                    headers: { 'X-CSRF-Token': csrfToken },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    alert('✅ Class material uploaded successfully!');
                    document.getElementById('material-upload-form').reset();
                    await loadMaterials();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Upload failed: ' + err.message);
            } finally {
                document.getElementById('btn-upload-mat').disabled = false;
                document.getElementById('btn-upload-mat').innerHTML = '<span class="material-symbols-outlined text-[16px]">cloud_upload</span> Upload to Portal';
            }
        }

        async function loadMaterials() {
            try {
                const res = await fetch('api_faculty.php?action=get_materials');
                const data = await res.json();
                const list = document.getElementById('materials-list');

                if (data.success && data.materials && data.materials.length > 0) {
                    list.innerHTML = data.materials.map(m => `
                        <div class="p-2.5 bg-surface rounded-xl border flex items-center justify-between">
                            <div>
                                <p class="font-bold text-primary">${m.title}</p>
                                <span class="text-[10px] text-gray-500">${m.category} • ${m.file_size || ''}</span>
                            </div>
                            <span class="text-status-success material-symbols-outlined text-[18px]">verified</span>
                        </div>
                    `).join('');
                } else {
                    list.innerHTML = '<p class="text-xs text-gray-400">No materials uploaded yet.</p>';
                }
            } catch (err) {
                console.error(err);
            }
        }


        /* ── Faculty announcements feed (full-text cards) ── */
        function facultyEscapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text == null ? '' : String(text);
            return div.innerHTML;
        }
        function facultyRenderBody(raw) {
            if (!raw) return '<p class="text-on-surface-variant/70 italic text-xs">No announcement content.</p>';
            let text = String(raw).trim()
                .replace(/\s*bis_skin_checked="[^"]*"/gi, '')
                .replace(/\s*contenteditable="[^"]*"/gi, '')
                .replace(/<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi, '')
                .replace(/<iframe\b[^<]*(?:(?!<\/iframe>)<[^<]*)*<\/iframe>/gi, '')
                .replace(/on\w+="[^"]*"/gi, '');
            if (/<(div|p|span|ul|ol|li|h[1-6]|strong|b|em|i|blockquote|table|br)/i.test(text)) {
                return `<div class="announcement-content text-xs leading-relaxed break-words" style="overflow-wrap:anywhere;word-break:break-word;">${text}</div>`;
            }
            const formatted = facultyEscapeHtml(text)
                .replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-primary">$1</strong>')
                .replace(/\*(.*?)\*/g, '<em class="italic">$1</em>')
                .replace(/• (.*?)(\n|$)/g, '<li class="ml-4 list-disc">$1</li>')
                .replace(/\n/g, '<br>');
            return `<div class="announcement-content text-xs leading-relaxed break-words" style="overflow-wrap:anywhere;word-break:break-word;">${formatted}</div>`;
        }
        async function loadFacultyAnnouncements() {
            const container = document.getElementById('faculty-announcements-container');
            if (!container || typeof supabase === 'undefined') return;
            try {
                const { data, error } = await supabaseClient
                    .from('announcements')
                    .select('*')
                    .eq('status', 'published')
                    .order('created_at', { ascending: false })
                    .limit(6);
                if (error) throw error;
                if (!data || !data.length) {
                    container.innerHTML = '<div class="text-center text-on-surface-variant text-sm py-4">No recent announcements.</div>';
                    return;
                }
                container.innerHTML = data.map(a => {
                    const isEmergency = a.category === 'emergency';
                    const isAcademic = a.category === 'academic';
                    const badgeClass = isEmergency ? 'bg-error-container text-error'
                        : (isAcademic ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container text-primary');
                    const icon = isEmergency ? 'warning' : (isAcademic ? 'school' : 'campaign');
                    const dateStr = new Date(a.created_at).toLocaleDateString();
                    return `
                    <div class="p-3.5 bg-surface-container-low/40 rounded-xl border border-outline-variant/40 hover:bg-surface-container-low transition-colors space-y-2 overflow-hidden">
                        <div class="flex items-center justify-between gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full ${badgeClass} font-mono text-[10px] uppercase font-bold tracking-wide shrink-0">
                                <span class="material-symbols-outlined text-[12px]">${icon}</span> ${facultyEscapeHtml(a.category)}
                            </span>
                            <span class="font-mono text-[10px] text-on-surface-variant shrink-0">${dateStr}</span>
                        </div>
                        <h4 class="text-sm font-bold text-primary break-words leading-snug" style="overflow-wrap:anywhere;word-break:break-word;">${facultyEscapeHtml(a.title)}</h4>
                        <div class="announcement-body text-xs leading-relaxed">${facultyRenderBody(a.body)}</div>
                    </div>`;
                }).join('');
            } catch (err) {
                console.error('Faculty announcements failed:', err);
                container.innerHTML = '<div class="text-center text-on-surface-variant text-sm py-4">Announcements unavailable right now.</div>';
            }
        }
        if (typeof supabaseClient !== 'undefined') loadFacultyAnnouncements();

        loadTeacherDashboard();

        /* ── Live clock + Today's Classes widget ─────────────── */
        (function () {
            var clockEl = document.getElementById('npc-live-clock');
            if (clockEl) {
                function tick() {
                    clockEl.textContent = new Date().toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' })
                        + ' · ' + new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit' });
                }
                tick();
                setInterval(tick, 1000);
            }

            var dateEl = document.getElementById('npc-today-date');
            if (dateEl) {
                dateEl.textContent = new Date().toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric' }).toUpperCase();
            }
        })();

        function renderTodayClasses(classes) {
            var wrap = document.getElementById('npc-today-classes');
            if (!wrap) return;
            var dayNames = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            var today = dayNames[new Date().getDay()];

            var todays = (classes || []).filter(function (c) {
                var d = String(c.schedule_day || '').toLowerCase();
                return d.indexOf(today.toLowerCase()) !== -1;
            });

            if (!todays.length) {
                wrap.innerHTML = '<div class="flex items-center gap-2.5 text-sm text-on-primary-container py-1">' +
                    '<span class="material-symbols-outlined text-[18px] text-secondary-container">event_available</span>' +
                    '<span>No classes scheduled for today — enjoy the break, Prof.!</span>' +
                    '</div>';
                return;
            }

            todays.sort(function (a, b) { return String(a.start_time || '').localeCompare(String(b.start_time || '')); });
            wrap.innerHTML = todays.map(function (c) {
                return '<div class="flex items-center justify-between gap-3 rounded-xl px-4 py-2.5 bg-white/10 border border-white/10 hover:bg-white/15 transition-colors">' +
                    '<div class="flex items-center gap-3 min-w-0">' +
                    '<span class="font-mono text-xs font-bold text-secondary-container shrink-0">' + (c.start_time || '') + '</span>' +
                    '<div class="min-w-0"><p class="text-sm font-semibold truncate">' + (c.code || '') + ' — ' + (c.title || '') + '</p>' +
                    '<p class="text-[11px] text-on-primary-container truncate">' + (c.room || 'Room TBA') + ' • Section ' + (c.section || '—') + '</p></div>' +
                    '</div>' +
                    '<a href="teacher_attendance.php?class_id=' + c.id + '" class="shrink-0 inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-secondary-container text-on-secondary-container text-[11px] font-bold hover:opacity-90 press">' +
                    '<span class="material-symbols-outlined text-[14px]">qr_code_scanner</span> QR</a>' +
                    '</div>';
            }).join('');
        }
    </script>
</body>
</html>
