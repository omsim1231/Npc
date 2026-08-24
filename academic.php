<?php
require_once 'auth.php';
require_login();
$is_logged_in = isset($_SESSION['user_id']);
$raw_name = (isset($_SESSION['name']) && $_SESSION['name'] !== null) ? (string)$_SESSION['name'] : 'Guest User';
$user_name = $is_logged_in ? explode(' ', trim($raw_name))[0] : 'Guest';
$full_name = $is_logged_in ? (string)$_SESSION['name'] : 'Guest User';
$user_id_display = $is_logged_in && isset($_SESSION['student_number']) ? (string)$_SESSION['student_number'] : 'GUEST';
$user_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$is_admin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'registrar');
$student_program = isset($_SESSION['program']) ? $_SESSION['program'] : 'AIS';
$student_section = isset($_SESSION['section']) ? $_SESSION['section'] : '2A';
$assigned_section = trim("$student_program $student_section");
$csrf_token = getCsrfToken();
$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Records & Student Services - NPC Connect</title>
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

    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/styles.css') ?>">
    <script src="npc.js?v=<?= filemtime(__DIR__ . '/npc.js') ?>"></script>
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
    <style>
        @media print {
            @page { size: portrait; margin: 12mm 15mm; }
            aside, #topbar, nav, button, .no-print { display: none !important; }
            #app-root, main, #canvas-container { display: block !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
            #print-report-header { display: block !important; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 15px; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #333 !important; padding: 6px 8px !important; color: #000 !important; }
            th { background: #eee !important; font-weight: bold !important; }
        }
        #print-report-header { display: none; }
    </style>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- App Container -->
    <div class="flex min-h-screen w-full" id="app-root">

        <!-- SideNavBar Desktop -->
        <?php $NPC_PORTAL = 'student'; include __DIR__ . '/_sidebar.php'; ?>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-surface" id="main-wrapper">
            <!-- TopNavBar Header -->
            <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-primary lg:hidden">NPC Connect</span>
                    <h2 class="text-xl font-bold text-primary hidden lg:block" id="page-title">Academic Records & Student Services</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-semibold bg-surface-container px-3 py-1.5 rounded-md border border-outline-variant text-primary" id="user-id-chip">ID: <?= htmlspecialchars($user_id_display) ?></span>
                        <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                            <?= strtoupper(substr($full_name, 0, 1)) ?>
                        </div>
                        <span class="text-sm font-semibold text-primary hidden sm:inline" id="user-name-display"><?= htmlspecialchars($full_name) ?></span>
                    </div>
                </div>
            </header>

            <!-- Page Canvas -->
            <main class="flex-1 p-6 md:p-10 max-w-7xl w-full mx-auto space-y-8 lg:pl-64" id="canvas-container">
                
                <!-- Print Header -->
                <div id="print-report-header">
                    <div class="flex items-center gap-4 mb-3">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg" class="w-12 h-12" alt="NPC Emblem">
                        <div>
                            <h2 class="text-base font-bold uppercase text-black">Navotas Polytechnic College</h2>
                            <p class="text-xs text-gray-700">Office of the Registrar — Student Academic & Grade Report</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 text-xs border-t border-b border-gray-400 py-1.5 mb-3">
                        <p><strong>Name:</strong> <?= htmlspecialchars($full_name) ?></p>
                        <p><strong>Student #:</strong> <?= htmlspecialchars($user_id_display) ?></p>
                        <p><strong>Program & Section:</strong> <?= htmlspecialchars($assigned_section) ?></p>
                        <p><strong>Term:</strong> 1st Semester, 2026-2027</p>
                    </div>
                </div>

                <!-- Page Header & Student Summary -->
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 border-b border-outline-variant/60 pb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h1 class="text-2xl md:text-3xl font-bold text-primary tracking-tight">Academic Performance</h1>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-status-success/15 text-status-success">
                                <span class="w-1.5 h-1.5 rounded-full bg-status-success"></span> Official Student Records
                            </span>
                        </div>
                        <p class="text-sm text-on-surface-variant">Confidential student grades, cumulative GPA evaluation, attendance metrics, and official registrar requests.</p>
                    </div>

                    <div class="flex items-center gap-2 no-print">
                        <button onclick="window.print()" class="px-4 py-2.5 bg-surface hover:bg-surface-container border border-outline-variant rounded-xl text-xs font-bold text-primary flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">print</span> Print Grade Slip
                        </button>
                        <button onclick="openDocumentRequestModal()" class="px-4 py-2.5 bg-primary hover:bg-primary-container text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[18px]">receipt_long</span> Request Documents
                        </button>
                    </div>
                </div>

                <!-- Bento Metrics -->
                <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant shadow-sm">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-on-surface-variant">Semester GPA</span>
                        <h3 class="text-3xl font-bold text-primary mt-2" id="semester-gpa-display">—</h3>
                        <p class="text-xs text-on-surface-variant font-medium mt-2 flex items-center gap-1" id="gpa-status-note">
                            <span class="material-symbols-outlined text-[16px] text-outline-variant">schedule</span> Evaluating Official Records
                        </p>
                    </div>

                    <div class="bg-surface-container-lowest rounded-2xl p-6 border border-outline-variant shadow-sm">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-on-surface-variant">Overall Attendance Rate</span>
                        <h3 class="text-3xl font-bold text-status-success mt-2" id="academic-attendance-rate">100%</h3>
                        <p class="text-xs text-on-surface-variant mt-2 font-medium" id="academic-checkins-count">Verified lecture check-ins</p>
                    </div>

                    <div class="bg-primary text-on-primary rounded-2xl p-6 shadow-md npc-navy-card">
                        <span class="text-xs font-mono font-semibold uppercase tracking-wider text-on-primary-container">Academic Standing</span>
                        <h3 class="text-2xl font-bold text-white mt-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary-container">school</span> Regular Student
                        </h3>
                        <p class="text-xs text-on-primary-container mt-2">Section <?= htmlspecialchars($assigned_section) ?> • AY 2026-2027</p>
                    </div>
                </section>

                <!-- Navigation Tabs: Grades vs Document Requests vs Attendance -->
                <div class="flex items-center gap-2 border-b border-outline-variant/60 no-print">
                    <button id="tab-btn-grades" onclick="switchStudentTab('grades')" class="px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">grade</span>
                        <span>My Official Grades</span>
                    </button>
                    <button id="tab-btn-docs" onclick="switchStudentTab('docs')" class="px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                        <span>Document Requests (<span id="doc-req-badge">0</span>)</span>
                    </button>
                    <button id="tab-btn-attendance" onclick="switchStudentTab('attendance')" class="px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">fact_check</span>
                        <span>Attendance History</span>
                    </button>
                </div>

                <!-- 1. OFFICIAL GRADES TAB -->
                <div id="section-student-grades" class="space-y-6">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-subtle">
                            <div>
                                <h3 class="font-bold text-primary text-base flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">military_tech</span>
                                    Official Published Grade Report
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">1st Semester, Academic Year 2026-2027 • Approved by Registrar</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-surface-container-low font-mono text-xs text-on-surface uppercase tracking-wider">
                                        <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Subject Code</th>
                                        <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Description</th>
                                        <th class="py-3.5 px-6 font-semibold border-b border-outline-variant text-center">Units</th>
                                        <th class="py-3.5 px-6 font-semibold border-b border-outline-variant text-center">Final Rating</th>
                                        <th class="py-3.5 px-6 font-semibold border-b border-outline-variant text-right">Remark</th>
                                    </tr>
                                </thead>
                                <tbody id="student-grades-tbody" class="divide-y divide-outline-variant/30 text-sm font-medium">
                                    <tr>
                                        <td colspan="5" class="p-12 text-center text-on-surface-variant">
                                            <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">pending_actions</span>
                                            <p class="font-semibold text-lg text-primary">Loading Approved Grades...</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. DOCUMENT REQUESTS TAB -->
                <div id="section-student-docs" class="hidden space-y-6">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-subtle">
                            <div>
                                <h3 class="font-bold text-primary text-base flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                                    Document Requests Status Tracker
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">Track Certificate of Registration (COR), Certificate of Enrollment (COE), Good Moral & Transcript requests.</p>
                            </div>
                            <button onclick="openDocumentRequestModal()" class="px-3.5 py-2 bg-primary text-white rounded-xl text-xs font-bold shadow-sm">
                                + New Request
                            </button>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-surface-container-low font-mono text-on-surface uppercase tracking-wider">
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Reference #</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Document Type</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Purpose</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Date Requested</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="document-requests-tbody" class="divide-y divide-outline-variant/30 text-xs">
                                    <tr><td colspan="5" class="p-8 text-center text-gray-400">Loading document requests...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. ATTENDANCE HISTORY TAB -->
                <div id="section-student-attendance" class="hidden space-y-6">
                    <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                        <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-subtle">
                            <div>
                                <h3 class="font-bold text-primary text-base flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-[20px]">fact_check</span>
                                    Verified Attendance Log
                                </h3>
                                <p class="text-xs text-on-surface-variant mt-0.5">Real-time attendance check-ins recorded via Faculty QR scanner.</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-surface-container-low font-mono text-on-surface uppercase tracking-wider">
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Session Code</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Date & Time</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant">Method</th>
                                        <th class="py-3 px-6 font-semibold border-b border-outline-variant text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody id="student-attendance-tbody" class="divide-y divide-outline-variant/30 text-xs">
                                    <tr><td colspan="4" class="p-8 text-center text-gray-400">Loading attendance history...</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>

    <!-- 📄 DOCUMENT REQUEST MODAL -->
    <div id="doc-request-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-[22px]">receipt_long</span>
                    <h3 class="text-base font-bold text-primary">Request Academic Document</h3>
                </div>
                <button onclick="closeDocumentRequestModal()" class="text-gray-400 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Document Type:</label>
                    <select id="req-doc-type" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-1 focus:ring-primary">
                        <option value="Certificate of Registration (COR)">Certificate of Registration (COR)</option>
                        <option value="Certificate of Enrollment (COE)">Certificate of Enrollment (COE)</option>
                        <option value="Certificate of Good Moral Character">Certificate of Good Moral Character</option>
                        <option value="Official Transcript of Records (OTR)">Official Transcript of Records (OTR)</option>
                        <option value="Certified True Copy of Grades / Grade Slip">Certified True Copy of Grades / Grade Slip</option>
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Purpose of Request:</label>
                    <textarea id="req-doc-purpose" rows="3" placeholder="e.g. Scholarship application, Employment requirement, Board Exam qualification..." class="w-full bg-white border border-gray-300 rounded-xl p-3 text-xs text-gray-800 focus:ring-1 focus:ring-primary"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t">
                <button onclick="closeDocumentRequestModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitDocumentRequest()" class="px-5 py-2 bg-primary text-white rounded-xl text-xs font-bold hover:bg-primary-container shadow-sm">
                    Submit Request
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-2">
        <span class="material-symbols-outlined text-[18px] text-emerald-400" id="toast-icon">check_circle</span>
        <span id="toast-message">Ready</span>
    </div>

    <script>
        const csrfToken = <?= json_encode($csrf_token) ?>;

        function switchStudentTab(tab) {
            document.getElementById('tab-btn-grades').className = tab === 'grades' 
                ? 'px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2'
                : 'px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2';
            document.getElementById('tab-btn-docs').className = tab === 'docs' 
                ? 'px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2'
                : 'px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2';
            document.getElementById('tab-btn-attendance').className = tab === 'attendance' 
                ? 'px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2'
                : 'px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2';

            document.getElementById('section-student-grades').classList.toggle('hidden', tab !== 'grades');
            document.getElementById('section-student-docs').classList.toggle('hidden', tab !== 'docs');
            document.getElementById('section-student-attendance').classList.toggle('hidden', tab !== 'attendance');
        }

        async function loadStudentData() {
            // 1. Fetch Grades
            try {
                const res = await fetch('api_student.php?action=get_my_grades');
                const data = await res.json();

                const tbody = document.getElementById('student-grades-tbody');
                const gpaDisplay = document.getElementById('semester-gpa-display');
                const gpaNote = document.getElementById('gpa-status-note');

                if (data.success && data.grades && data.grades.length > 0) {
                    tbody.innerHTML = data.grades.map(g => {
                        const units = parseFloat(g.units) || 3.0;
                        const grade = parseFloat(g.grade) || 0;
                        const isPass = grade <= 3.00 && grade >= 1.00;

                        return `
                            <tr class="hover:bg-surface-subtle transition-colors">
                                <td class="py-3.5 px-6 font-mono font-bold text-primary">${g.subject_code}</td>
                                <td class="py-3.5 px-6 text-on-surface">${g.description || 'Subject Course'}</td>
                                <td class="py-3.5 px-6 font-mono text-center">${units.toFixed(1)}</td>
                                <td class="py-3.5 px-6 font-mono font-bold text-primary text-center">${grade > 0 ? grade.toFixed(2) : '—'}</td>
                                <td class="py-3.5 px-6 text-right">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold ${isPass ? 'bg-status-success/15 text-status-success' : 'bg-red-100 text-error'}">
                                        <span class="w-1.5 h-1.5 rounded-full ${isPass ? 'bg-status-success' : 'bg-error'}"></span> ${g.status || (isPass ? 'Passed' : 'Failed')}
                                    </span>
                                </td>
                            </tr>
                        `;
                    }).join('');

                    gpaDisplay.innerText = data.summary.gpa;
                    gpaNote.innerHTML = '<span class="material-symbols-outlined text-[16px] text-status-success">verified</span> Evaluated & Officially Published';
                } else {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="p-12 text-center text-on-surface-variant">
                                <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">pending_actions</span>
                                <p class="font-semibold text-lg text-primary">No Published Grades Available Yet</p>
                                <p class="text-xs text-on-surface-variant mt-1 max-w-md mx-auto">Official grades will appear here once approved and published by the Registrar.</p>
                            </td>
                        </tr>
                    `;
                }
            } catch (err) {
                console.error(err);
            }

            // 2. Fetch Document Requests
            try {
                const res = await fetch('api_student.php?action=get_document_requests');
                const data = await res.json();
                const tbody = document.getElementById('document-requests-tbody');
                const badge = document.getElementById('doc-req-badge');

                if (data.success && data.requests && data.requests.length > 0) {
                    badge.innerText = data.requests.length;
                    tbody.innerHTML = data.requests.map(r => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono font-bold text-primary">${r.reference_no}</td>
                            <td class="py-3 px-6 font-bold">${r.document_type}</td>
                            <td class="py-3 px-6 text-gray-600">${r.purpose}</td>
                            <td class="py-3 px-6 font-mono text-gray-500">${new Date(r.requested_at).toLocaleDateString()}</td>
                            <td class="py-3 px-6 text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${r.status === 'Ready for Pickup' || r.status === 'Released' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">${r.status}</span>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    badge.innerText = '0';
                    tbody.innerHTML = '<tr><td colspan="5" class="p-8 text-center text-gray-400">No document requests submitted yet.</td></tr>';
                }
            } catch (err) {
                console.error(err);
            }

            // 3. Fetch Attendance History
            try {
                const res = await fetch('api_student.php?action=get_attendance_metrics');
                const data = await res.json();
                const tbody = document.getElementById('student-attendance-tbody');

                if (data.success && data.records && data.records.length > 0) {
                    document.getElementById('academic-attendance-rate').innerText = data.stats.rate;
                    document.getElementById('academic-checkins-count').innerText = `${data.stats.present} On-Time, ${data.stats.late} Late`;

                    tbody.innerHTML = data.records.map(r => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono font-bold text-primary">${r.session_code}</td>
                            <td class="py-3 px-6 font-mono text-gray-600">${new Date(r.check_in_at).toLocaleString()}</td>
                            <td class="py-3 px-6 uppercase font-mono text-[11px] text-gray-500">${r.method || 'QR Scanner'}</td>
                            <td class="py-3 px-6 text-right">
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold ${r.status === 'present' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">${r.status.toUpperCase()}</span>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="p-8 text-center text-gray-400">No attendance check-ins logged yet.</td></tr>';
                }
            } catch (err) {
                console.error(err);
            }
        }

        function openDocumentRequestModal() {
            document.getElementById('doc-request-modal').classList.remove('hidden');
        }
        function closeDocumentRequestModal() {
            document.getElementById('doc-request-modal').classList.add('hidden');
        }

        async function submitDocumentRequest() {
            const docType = document.getElementById('req-doc-type').value;
            const purpose = document.getElementById('req-doc-purpose').value.trim();

            if (!purpose) {
                alert('Please enter the purpose of your document request.');
                return;
            }

            try {
                const res = await fetch('api_student.php?action=request_document', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ document_type: docType, purpose: purpose, csrf_token: csrfToken })
                });

                const data = await res.json();
                if (data.success) {
                    alert(`✅ Document Request Submitted!\n\nReference Number: ${data.reference_no}\nPlease check back for status updates.`);
                    closeDocumentRequestModal();
                    await loadStudentData();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Request error: ' + err.message);
            }
        }

        loadStudentData();
    </script>
</body>
</html>
