<?php
require_once 'auth.php';
require_admin();
$admin_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Administrator';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$jsConfig = getJsConfig();
$csrf_token = getCsrfToken();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Student Directory - NPC Connect Admin</title>
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
    <!-- Google Fonts & Material Symbols -->
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
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>

    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 lg:ml-64 bg-surface min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Admin</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">Student Directory & Enrollment</h2>
            </div>
            <div class="flex items-center gap-3">
                <button id="import-btn" class="bg-secondary-container text-on-secondary-container px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-bold hover:brightness-95 transition-all shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">upload_file</span>
                    Import Student File
                </button>
                <button id="add-student-btn" class="bg-primary text-on-primary px-4 py-2 rounded-xl flex items-center gap-2 text-sm font-semibold hover:opacity-90 transition-opacity npc-navy-card">
                    <span class="material-symbols-outlined text-[18px]">person_add</span>
                    Add Student
                </button>
            </div>
        </header>

        <!-- Canvas Container -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-6 flex-1">
            
            <!-- Header & Filter Bar -->
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-primary tracking-tight">Enrolled Students Directory</h1>
                    <p class="text-xs text-on-surface-variant mt-0.5">Filter by academic program, section, or search student numbers and emails.</p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Search Input -->
                    <div class="relative">
                        <input type="text" id="search-input" onkeyup="filterStudents()" placeholder="Search name, ID, email..." class="bg-surface border border-outline-variant/60 text-xs rounded-xl pl-8 pr-3 py-2 text-primary focus:outline-none focus:border-primary w-48 sm:w-56">
                        <span class="material-symbols-outlined text-[16px] text-on-surface-variant absolute left-2.5 top-2.5">search</span>
                    </div>

                    <!-- Program Filter -->
                    <div>
                        <select id="program-filter" onchange="filterStudents()" class="bg-surface border border-outline-variant/60 text-primary text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:border-primary">
                            <option value="all">All Programs</option>
                            <option value="BSIS">BSIS (Info Systems)</option>
                            <option value="BSBA - HR">BSBA - HR</option>
                            <option value="BSBA - FM">BSBA - FM</option>
                            <option value="BSBA - MM">BSBA - MM</option>
                            <option value="BSEd">BSEd (Secondary Ed)</option>
                            <option value="BEEd">BEEd (Elem Ed)</option>
                            <option value="AIS">AIS (Applied Info Systems)</option>
                        </select>
                    </div>

                    <!-- Section Filter -->
                    <div>
                        <select id="section-filter" onchange="filterStudents()" class="bg-surface border border-outline-variant/60 text-primary text-xs font-semibold rounded-xl px-3 py-2 focus:outline-none focus:border-primary">
                            <option value="all">All Sections</option>
                            <option value="1A">Section 1A</option>
                            <option value="1B">Section 1B</option>
                            <option value="2A">Section 2A</option>
                            <option value="2B">Section 2B</option>
                            <option value="3A">Section 3A</option>
                            <option value="3B">Section 3B</option>
                            <option value="4A">Section 4A</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Students Table Container -->
            <div class="bg-surface-container-lowest rounded-2xl shadow-sm border border-outline-variant overflow-hidden">
                <div class="p-4 px-6 border-b border-outline-variant bg-surface-subtle flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-primary text-sm">Student Records</span>
                        <span id="student-count-badge" class="px-2.5 py-0.5 rounded-full text-xs font-mono font-bold bg-primary-container text-white">0</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low text-xs font-mono text-on-surface uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Student ID</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Full Name</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">NPC Email</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Program</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Section</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody id="students-list" class="divide-y divide-outline-variant/30 text-sm">
                            <tr><td colspan="6" class="p-8 text-center text-on-surface-variant">Loading student directory...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 1. ADD SINGLE STUDENT MODAL -->
        <div id="add-student-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden border border-outline-variant/20">
                <div class="p-6 border-b border-outline-variant/30 flex items-center justify-between bg-surface-subtle">
                    <h3 class="text-xl font-bold text-primary">Add Single Student</h3>
                    <button onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1">Full Name</label>
                        <input type="text" id="student-name" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-sm" placeholder="e.g. Juan Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1">Institutional Email</label>
                        <input type="email" id="student-email" oninput="autoFillStudentNumber()" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-sm font-mono" placeholder="e.g. jderramas251505@navotaspolytechniccollege.edu.ph">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1">Student Number (Auto-detected)</label>
                        <input type="text" id="student-number" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-sm font-mono" placeholder="e.g. 251505">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Program</label>
                            <select id="student-program" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold">
                                <option value="BSIS">BSIS</option>
                                <option value="BSBA - HR">BSBA - HR</option>
                                <option value="BSBA - FM">BSBA - FM</option>
                                <option value="BSBA - MM">BSBA - MM</option>
                                <option value="BSEd">BSEd</option>
                                <option value="BEEd">BEEd</option>
                                <option value="AIS">AIS</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Section</label>
                            <select id="student-section" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold font-mono">
                                <option value="1A">Section 1A</option>
                                <option value="1B">Section 1B</option>
                                <option value="2A">Section 2A</option>
                                <option value="2B">Section 2B</option>
                                <option value="3A">Section 3A</option>
                                <option value="3B">Section 3B</option>
                                <option value="4A">Section 4A</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-outline-variant/30 bg-surface-subtle flex justify-end gap-3">
                    <button onclick="document.getElementById('add-student-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-on-surface-variant hover:bg-surface-variant transition-colors font-semibold text-sm">Cancel</button>
                    <button id="save-btn" class="px-4 py-2 bg-primary text-on-primary rounded-xl hover:opacity-90 transition-opacity font-semibold text-sm npc-navy-card">Save Student</button>
                </div>
            </div>
        </div>

        <!-- 2. BULK FILE IMPORT MODAL -->
        <div id="import-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-xl mx-4 overflow-hidden border border-outline-variant/20">
                <div class="p-6 border-b border-outline-variant/30 flex items-center justify-between bg-surface-subtle">
                    <div>
                        <h3 class="text-xl font-bold text-primary">Import Students in Bulk</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Upload an Excel (.xlsx, .csv), PDF, Word (.docx), or Text file with student emails</p>
                    </div>
                    <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    <!-- Target Program & Section -->
                    <div class="grid grid-cols-2 gap-4 bg-surface-subtle p-4 rounded-xl border border-outline-variant/40">
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Target Program</label>
                            <select id="bulk-program" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold">
                                <option value="BSIS">BSIS (Information Systems)</option>
                                <option value="BSBA - HR">BSBA - HR</option>
                                <option value="BSBA - FM">BSBA - FM</option>
                                <option value="BSBA - MM">BSBA - MM</option>
                                <option value="BSEd">BSEd (Secondary Education)</option>
                                <option value="BEEd">BEEd (Elementary Education)</option>
                                <option value="AIS">AIS (Applied Info Systems)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-primary mb-1">Target Section</label>
                            <select id="bulk-section" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold font-mono">
                                <option value="1A">Section 1A</option>
                                <option value="1B">Section 1B</option>
                                <option value="2A">Section 2A</option>
                                <option value="2B">Section 2B</option>
                                <option value="3A">Section 3A</option>
                                <option value="3B">Section 3B</option>
                                <option value="4A">Section 4A</option>
                            </select>
                        </div>
                    </div>

                    <!-- File Drag and Drop Box -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1">Option A: Upload File (.xlsx, .csv, .pdf, .docx, .txt)</label>
                        <div id="dropzone" class="border-2 border-dashed border-outline-variant rounded-2xl p-6 text-center hover:border-primary hover:bg-surface-subtle transition-all cursor-pointer">
                            <input type="file" id="bulk-file-input" accept=".xlsx,.xls,.csv,.pdf,.docx,.txt" class="hidden">
                            <span class="material-symbols-outlined text-[36px] text-primary mb-2 block">cloud_upload</span>
                            <p class="text-xs font-semibold text-primary" id="file-label">Click or drag & drop student file here</p>
                            <p class="text-[10px] text-on-surface-variant mt-1">Accepts Excel, Word, PDF, CSV, and Text documents</p>
                        </div>
                    </div>

                    <!-- Option B: Direct Paste Textarea -->
                    <div>
                        <label class="block text-xs font-semibold text-on-surface mb-1">Option B: Or Paste Emails Directly</label>
                        <textarea id="bulk-text-input" rows="4" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-mono" placeholder="jqerramas251555@navotaspolytechniccollege.edu.ph&#10;jderramas251505@navotaspolytechniccollege.edu.ph"></textarea>
                    </div>

                    <!-- Scan Results Preview Area -->
                    <div id="import-status-box" class="hidden p-4 rounded-xl border text-xs"></div>
                </div>

                <div class="p-4 border-t border-outline-variant/30 bg-surface-subtle flex justify-between items-center">
                    <span class="text-[11px] text-on-surface-variant">Auto-detects student IDs from email prefix.</span>
                    <div class="flex gap-2">
                        <button onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-on-surface-variant hover:bg-surface-variant transition-colors font-semibold text-sm">Cancel</button>
                        <button id="process-import-btn" class="px-5 py-2 bg-primary text-on-primary rounded-xl hover:opacity-90 transition-opacity font-semibold text-sm flex items-center gap-2 npc-navy-card">
                            <span class="material-symbols-outlined text-[16px]">sync</span>
                            Import & Save
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);
        const csrfToken = <?= json_encode($csrf_token) ?>;

        let allStudents = [];

        async function loadStudents() {
            const tbody = document.getElementById('students-list');
            try {
                const { data, error } = await supabaseClient.from('users').select('*').eq('role', 'student').order('created_at', { ascending: false });
                if (error) throw error;
                allStudents = data || [];
                document.getElementById('student-count-badge').innerText = allStudents.length;
                renderStudents(allStudents);
            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="6" class="p-8 text-center text-error">Failed to load student records.</td></tr>';
            }
        }

        function renderStudents(list) {
            const tbody = document.getElementById('students-list');
            if (!list || list.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="p-12 text-center text-on-surface-variant">
                            <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">group</span>
                            <p class="font-semibold text-lg">No students found</p>
                            <p class="text-xs text-on-surface-variant mt-1">Import a student list or add a student to enroll them.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = list.map(s => `
                <tr class="hover:bg-surface-subtle transition-colors">
                    <td class="py-3.5 px-6 font-mono text-xs font-bold text-primary">${s.student_number || 'N/A'}</td>
                    <td class="py-3.5 px-6 font-bold text-on-surface">${s.full_name || 'Unnamed Student'}</td>
                    <td class="py-3.5 px-6 text-xs text-on-surface-variant font-mono">${s.email}</td>
                    <td class="py-3.5 px-6">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-surface-container text-primary font-mono">${s.program || 'BSIS'}</span>
                    </td>
                    <td class="py-3.5 px-6">
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-surface-container-low text-on-surface-variant">${s.section || '1A'}</span>
                    </td>
                    <td class="py-3.5 px-6 text-right">
                        <button onclick="deleteStudent('${s.id}')" class="text-error hover:underline text-xs font-semibold inline-flex items-center gap-0.5">
                            <span class="material-symbols-outlined text-[14px]">delete</span> Delete
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function filterStudents() {
            const query = document.getElementById('search-input').value.toLowerCase();
            const programVal = document.getElementById('program-filter').value;
            const sectionVal = document.getElementById('section-filter').value;

            const filtered = allStudents.filter(s => {
                const matchesQuery = !query || 
                    (s.full_name && s.full_name.toLowerCase().includes(query)) ||
                    (s.student_number && s.student_number.includes(query)) ||
                    (s.email && s.email.toLowerCase().includes(query));

                const matchesProgram = programVal === 'all' || (s.program && s.program === programVal);
                const matchesSection = sectionVal === 'all' || (s.section && s.section === sectionVal);

                return matchesQuery && matchesProgram && matchesSection;
            });

            renderStudents(filtered);
        }

        async function deleteStudent(id) {
            if (!confirm('Are you sure you want to remove this student?')) return;
            try {
                const res = await fetch('api_admin.php?action=delete_student', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ id: id, csrf_token: csrfToken })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Delete failed');
            } catch (err) {
                alert('Error: ' + err.message);
            }
            loadStudents();
        }

        function autoFillStudentNumber() {
            const email = document.getElementById('student-email').value;
            const prefix = email.split('@')[0];
            const digits = prefix.match(/\d+/);
            if (digits) {
                document.getElementById('student-number').value = digits[0];
            }
        }

        // Add Single Student Handlers
        document.getElementById('add-student-btn').onclick = () => document.getElementById('add-student-modal').classList.remove('hidden');
        document.getElementById('save-btn').onclick = async () => {
            const name = document.getElementById('student-name').value.trim();
            const email = document.getElementById('student-email').value.trim();
            const number = document.getElementById('student-number').value.trim();
            const program = document.getElementById('student-program').value;
            const section = document.getElementById('student-section').value;

            if (!name || !email) return alert('Please enter at least the student name and email.');

            let ok = false;
            try {
                const res = await fetch('api_admin.php?action=create_student', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({
                        full_name: name,
                        email: email,
                        student_number: number || 'N/A',
                        program: program,
                        section: section,
                        csrf_token: csrfToken
                    })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Save failed');
                ok = true;
            } catch (err) {
                alert('Error saving student: ' + err.message);
            }

            if (ok) {
                document.getElementById('add-student-modal').classList.add('hidden');
                document.getElementById('student-name').value = '';
                document.getElementById('student-email').value = '';
                document.getElementById('student-number').value = '';
                loadStudents();
            }
        };

        // Bulk Import Handlers
        document.getElementById('import-btn').onclick = () => document.getElementById('import-modal').classList.remove('hidden');

        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('bulk-file-input');
        const fileLabel = document.getElementById('file-label');

        dropzone.onclick = () => fileInput.click();
        fileInput.onchange = () => {
            if (fileInput.files.length > 0) {
                fileLabel.innerText = 'Selected: ' + fileInput.files[0].name;
            }
        };

        document.getElementById('process-import-btn').onclick = async () => {
            const file = fileInput.files[0];
            const text = document.getElementById('bulk-text-input').value.trim();
            const program = document.getElementById('bulk-program').value;
            const section = document.getElementById('bulk-section').value;
            const statusBox = document.getElementById('import-status-box');

            if (!file && !text) {
                alert('Please upload a file or paste email addresses.');
                return;
            }

            const formData = new FormData();
            if (file) formData.append('file', file);
            if (text) formData.append('emails_text', text);
            formData.append('program', program);
            formData.append('section', section);

            statusBox.className = 'p-4 rounded-xl border border-outline-variant bg-surface-subtle text-primary block';
            statusBox.innerHTML = '<span class="flex items-center gap-2"><span class="material-symbols-outlined animate-spin text-[16px]">progress_activity</span> Scanning emails and parsing student numbers...</span>';

            try {
                const res = await fetch('import_students.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await res.json();

                if (result.success) {
                    statusBox.className = 'p-4 rounded-xl border border-status-success/40 bg-status-success/10 text-status-success block font-semibold';
                    statusBox.innerHTML = `✅ ${result.message}`;
                    setTimeout(() => {
                        document.getElementById('import-modal').classList.add('hidden');
                        statusBox.classList.add('hidden');
                        fileInput.value = '';
                        fileLabel.innerText = 'Click or drag & drop student file here';
                        document.getElementById('bulk-text-input').value = '';
                        loadStudents();
                    }, 1500);
                } else {
                    statusBox.className = 'p-4 rounded-xl border border-error/40 bg-error/10 text-error block';
                    statusBox.innerHTML = `❌ ${result.message}`;
                }
            } catch (err) {
                console.error(err);
                statusBox.className = 'p-4 rounded-xl border border-error/40 bg-error/10 text-error block';
                statusBox.innerHTML = '❌ Server communication error during file parsing.';
            }
        };

        loadStudents();
    </script>
</body>
</html>
