<?php
require_once 'auth.php';
require_admin();
$admin_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Administrator';
$admin_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : 'admin@navotaspolytechniccollege.edu.ph';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$csrf_token = getCsrfToken();
$jsConfig = getJsConfig();
$selected_class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Grade Approvals & Change Requests - NPC Connect Admin</title>
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
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">
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
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-surface lg:pl-64" id="main-wrapper">
        <!-- TopNavBar Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Connect</span>
                <h2 class="text-lg font-bold text-primary hidden lg:block">Grade Review, Approvals & Changes</h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        <?= $admin_initial ?>
                    </div>
                    <span class="text-xs font-semibold text-primary hidden sm:inline"><?= htmlspecialchars($admin_name) ?> (Registrar)</span>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 p-6 md:p-8 space-y-6 max-w-7xl w-full mx-auto">
            
            <!-- Page Header -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-outline-variant/60 pb-5">
                <div>
                    <h1 class="text-2xl font-bold text-primary tracking-tight">Official Grade Review & Publishing</h1>
                    <p class="text-xs text-on-surface-variant mt-1">Review faculty-submitted class grade sheets, approve and lock official ratings, and review grade correction requests.</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="loadQueues()" class="px-3.5 py-2 bg-surface hover:bg-surface-container text-xs font-bold rounded-xl text-primary border border-outline-variant flex items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined text-[16px]">refresh</span> Refresh Queues
                    </button>
                </div>
            </div>

            <!-- Tabs: Grade Submissions vs Grade Change Requests -->
            <div class="flex items-center gap-2 border-b border-outline-variant/60">
                <button id="tab-btn-submissions" onclick="switchMainTab('submissions')" class="px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">verified</span>
                    <span>Pending Class Grade Sheets (<span id="count-pending-submissions">0</span>)</span>
                </button>
                <button id="tab-btn-requests" onclick="switchMainTab('requests')" class="px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">edit_document</span>
                    <span>Grade Change Requests (<span id="count-pending-requests">0</span>)</span>
                </button>
            </div>

            <!-- 1. PENDING GRADE SUBMISSIONS QUEUE -->
            <div id="section-submissions" class="space-y-4">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                    <div class="p-5 border-b border-outline-variant bg-surface-subtle flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-primary text-sm">Class Grade Submissions</h3>
                            <p class="text-xs text-on-surface-variant">Click 'Review & Approve' to examine student grades and officially publish them to student academic records.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low font-mono text-[11px] text-on-surface uppercase tracking-wider">
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Class Code & Title</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Section</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Instructor</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Submitted Date</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant text-center">Status</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="submissions-tbody" class="divide-y divide-outline-variant/30 text-xs">
                                <tr><td colspan="6" class="p-8 text-center text-gray-400">Loading submitted grade sheets...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- 2. GRADE CHANGE REQUESTS QUEUE -->
            <div id="section-requests" class="hidden space-y-4">
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                    <div class="p-5 border-b border-outline-variant bg-surface-subtle flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-primary text-sm">Faculty Grade Correction Requests</h3>
                            <p class="text-xs text-on-surface-variant">Review correction justification provided by faculty for already locked/published student grade records.</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-surface-container-low font-mono text-[11px] text-on-surface uppercase tracking-wider">
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Student</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Subject</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Faculty</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant text-center">Current -> Proposed</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant">Reason / Justification</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant text-center">Status</th>
                                    <th class="py-3 px-5 font-bold border-b border-outline-variant text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody id="requests-tbody" class="divide-y divide-outline-variant/30 text-xs">
                                <tr><td colspan="7" class="p-8 text-center text-gray-400">Loading grade change requests...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Review Class Grades Modal -->
    <div id="review-grades-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-4xl w-full p-6 shadow-2xl border border-gray-200 space-y-4 max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b pb-3">
                <div>
                    <h3 class="text-base font-bold text-primary" id="modal-review-title">Review Class Grades</h3>
                    <p class="text-xs text-gray-500" id="modal-review-subtitle">Examine all encoded student grades prior to approval and publishing.</p>
                </div>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="flex-1 overflow-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-100 font-mono font-bold text-gray-700">
                            <th class="py-2.5 px-4 border-b">Student #</th>
                            <th class="py-2.5 px-4 border-b">Name</th>
                            <th class="py-2.5 px-4 border-b text-center">Prelim (30%)</th>
                            <th class="py-2.5 px-4 border-b text-center">Midterm (30%)</th>
                            <th class="py-2.5 px-4 border-b text-center">Final (40%)</th>
                            <th class="py-2.5 px-4 border-b text-center">Final GPA</th>
                            <th class="py-2.5 px-4 border-b text-right">Remark</th>
                        </tr>
                    </thead>
                    <tbody id="modal-review-tbody" class="divide-y text-xs">
                        <tr><td colspan="7" class="p-6 text-center text-gray-400">Loading student grades...</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-between pt-3 border-t">
                <div class="text-xs text-gray-500">
                    Publishing will lock the grade sheet and instantly notify enrolled students.
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="closeReviewModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50">
                        Close
                    </button>
                    <button id="modal-btn-approve" onclick="approveAndPublishModalClass()" class="px-5 py-2 bg-[#107c41] hover:bg-[#0c5d31] text-white rounded-xl text-xs font-bold flex items-center gap-1.5 shadow-md">
                        <span class="material-symbols-outlined text-[16px]">verified</span> Approve, Lock & Publish to Students
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-2">
        <span class="material-symbols-outlined text-[18px] text-emerald-400" id="toast-icon">check_circle</span>
        <span id="toast-message">Notification</span>
    </div>

    <script>
        const csrfToken = <?= json_encode($csrf_token) ?>;
        let pendingSubmissions = [];
        let changeRequests = [];
        let currentReviewClassId = null;

        function switchMainTab(tab) {
            document.getElementById('tab-btn-submissions').className = tab === 'submissions' 
                ? 'px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2'
                : 'px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2';
            document.getElementById('tab-btn-requests').className = tab === 'requests' 
                ? 'px-5 py-3 text-xs font-bold text-primary border-b-2 border-primary flex items-center gap-2'
                : 'px-5 py-3 text-xs font-bold text-gray-500 hover:text-primary border-b-2 border-transparent flex items-center gap-2';

            document.getElementById('section-submissions').classList.toggle('hidden', tab !== 'submissions');
            document.getElementById('section-requests').classList.toggle('hidden', tab !== 'requests');
        }

        async function loadQueues() {
            try {
                const res = await fetch('api_admin.php?action=get_grade_approval_queue');
                const data = await res.json();

                if (data.success) {
                    pendingSubmissions = data.submissions || [];
                    changeRequests = data.change_requests || [];

                    renderSubmissionsTable();
                    renderChangeRequestsTable();
                }
            } catch (err) {
                console.error('Queue load error:', err);
            }
        }

        function renderSubmissionsTable() {
            const tbody = document.getElementById('submissions-tbody');
            const pendingCount = pendingSubmissions.filter(s => s.status === 'Submitted').length;
            document.getElementById('count-pending-submissions').innerText = pendingCount;

            if (pendingSubmissions.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-gray-400">No grade sheets submitted yet.</td></tr>';
                return;
            }

            tbody.innerHTML = pendingSubmissions.map(s => {
                const isPub = s.status === 'Published' || s.status === 'Approved';
                const statusBadge = isPub 
                    ? '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-status-success">Published</span>'
                    : (s.status === 'Submitted' 
                        ? '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-800 animate-pulse">Submitted</span>'
                        : '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">Draft</span>');

                return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-5 font-mono font-bold text-primary">${s.class_code}</td>
                        <td class="py-3 px-5 font-mono font-bold">${s.section}</td>
                        <td class="py-3 px-5">${s.faculty_name || s.faculty_email}</td>
                        <td class="py-3 px-5 font-mono text-gray-500">${s.submitted_at ? new Date(s.submitted_at).toLocaleString() : '—'}</td>
                        <td class="py-3 px-5 text-center">${statusBadge}</td>
                        <td class="py-3 px-5 text-right">
                            <button onclick="openReviewGradesModal('${s.class_id}', '${s.class_code}', '${s.section}')" class="px-3 py-1.5 bg-primary text-white hover:bg-primary-container rounded-lg font-bold text-xs shadow-xs">
                                Review & Approve
                            </button>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderChangeRequestsTable() {
            const tbody = document.getElementById('requests-tbody');
            const pendingCount = changeRequests.filter(r => r.status === 'Pending').length;
            document.getElementById('count-pending-requests').innerText = pendingCount;

            if (changeRequests.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-400">No grade change requests submitted.</td></tr>';
                return;
            }

            tbody.innerHTML = changeRequests.map(r => {
                const isPending = r.status === 'Pending';
                const statusBadge = r.status === 'Approved'
                    ? '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-status-success">Approved</span>'
                    : (r.status === 'Rejected'
                        ? '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-error">Rejected</span>'
                        : '<span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 animate-pulse">Pending Review</span>');

                return `
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-5">
                            <p class="font-bold text-primary">${r.student_name}</p>
                            <p class="font-mono text-gray-500">${r.student_number}</p>
                        </td>
                        <td class="py-3 px-5 font-mono font-bold">${r.class_code}</td>
                        <td class="py-3 px-5">${r.faculty_name}</td>
                        <td class="py-3 px-5 font-mono font-bold text-center">
                            <span class="text-gray-500">${r.original_grade}</span> &rarr; <span class="text-status-success">${r.proposed_grade}</span>
                        </td>
                        <td class="py-3 px-5 text-gray-700 max-w-xs truncate" title="${r.reason}">${r.reason}</td>
                        <td class="py-3 px-5 text-center">${statusBadge}</td>
                        <td class="py-3 px-5 text-right">
                            ${isPending ? `
                                <div class="flex items-center justify-end gap-1.5">
                                    <button onclick="reviewGradeChange('${r.id}', 'Approved')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] shadow-xs">
                                        Approve
                                    </button>
                                    <button onclick="reviewGradeChange('${r.id}', 'Rejected')" class="px-2.5 py-1 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-[11px] shadow-xs">
                                        Reject
                                    </button>
                                </div>
                            ` : `<span class="text-gray-400 font-mono text-[11px]">${r.status}</span>`}
                        </td>
                    </tr>
                `;
            }).join('');
        }

        async function openReviewGradesModal(classId, code, section) {
            currentReviewClassId = classId;
            document.getElementById('modal-review-title').innerText = `Review Grades: ${code} (${section})`;
            document.getElementById('review-grades-modal').classList.remove('hidden');

            try {
                const res = await fetch(`api_gradebook.php?action=get_class_grades&class_id=${classId}`);
                const data = await res.json();

                const tbody = document.getElementById('modal-review-tbody');
                if (data.grades && data.grades.length > 0) {
                    tbody.innerHTML = data.grades.map(g => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 px-4 font-mono font-bold">${g.student_number}</td>
                            <td class="py-2.5 px-4">${g.student_name || 'Student'}</td>
                            <td class="py-2.5 px-4 font-mono text-center">${g.prelim > 0 ? g.prelim : '—'}</td>
                            <td class="py-2.5 px-4 font-mono text-center">${g.midterm > 0 ? g.midterm : '—'}</td>
                            <td class="py-2.5 px-4 font-mono text-center">${g.final > 0 ? g.final : '—'}</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-primary text-center">${g.equivalent_grade > 0 ? g.equivalent_grade.toFixed(2) : '—'}</td>
                            <td class="py-2.5 px-4 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold ${g.remarks === 'Passed' || g.remarks === 'PASSED' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'}">${g.remarks || 'Ongoing'}</span>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="7" class="p-6 text-center text-gray-400">No grades saved for this class yet.</td></tr>';
                }
            } catch (err) {
                console.error(err);
            }
        }

        function closeReviewModal() {
            document.getElementById('review-grades-modal').classList.add('hidden');
        }

        async function approveAndPublishModalClass() {
            if (!currentReviewClassId) return;

            if (!confirm('Are you sure you want to approve, lock, and publish these grades?\n\nStudents will immediately see their official final grades on their portal.')) {
                return;
            }

            try {
                const res = await fetch('api_gradebook.php?action=approve_and_publish', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ class_id: currentReviewClassId, csrf_token: csrfToken })
                });

                const data = await res.json();
                if (data.success) {
                    alert('✅ Grades successfully approved and published!');
                    closeReviewModal();
                    await loadQueues();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Approval error: ' + err.message);
            }
        }

        async function reviewGradeChange(reqId, decision) {
            const reason = prompt(`Enter any admin remarks for ${decision.toUpperCase()}:`, '');
            if (reason === null) return;

            try {
                const res = await fetch('api_gradebook.php?action=review_grade_change', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ request_id: reqId, decision: decision, remarks: reason, csrf_token: csrfToken })
                });

                const data = await res.json();
                if (data.success) {
                    alert(`✅ Grade Change Request ${decision}!`);
                    await loadQueues();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Review error: ' + err.message);
            }
        }

        loadQueues();
    </script>
</body>
</html>
