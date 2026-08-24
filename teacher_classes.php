<?php
require_once 'auth.php';
require_teacher();
$teacher_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Faculty Professor';
$teacher_initial = strtoupper(substr($teacher_name, 0, 1));
$teacher_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$jsConfig = getJsConfig();
$csrf_token = getCsrfToken();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>My Assigned Classes - NPC Faculty</title>
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
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'faculty'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 lg:ml-64 bg-surface min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Faculty</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">My Assigned Classes</h2>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="document.getElementById('add-class-modal').classList.remove('hidden')" class="bg-primary text-on-primary px-4 py-2 rounded-xl flex items-center gap-2 text-xs font-bold hover:opacity-90 transition-opacity shadow-sm npc-navy-card">
                    <span class="material-symbols-outlined text-[16px]">add</span>
                    Create Class
                </button>
            </div>
        </header>

        <!-- Canvas -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-6 flex-1">
            <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-primary tracking-tight">Classes Created by You</h1>
                    <p class="text-xs text-on-surface-variant mt-0.5">Logged in as: <strong class="font-mono text-primary"><?= htmlspecialchars($teacher_email) ?></strong> (<?= htmlspecialchars($teacher_name) ?>)</p>
                </div>
                <div>
                    <input type="text" id="class-search" onkeyup="filterClasses()" placeholder="Search subject code, section..." class="bg-surface border border-outline-variant/60 text-xs rounded-xl px-3 py-2 text-primary focus:outline-none focus:border-primary w-52">
                </div>
            </div>

            <div id="classes-list-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div class="col-span-full bg-surface-container-lowest border border-outline-variant rounded-2xl p-12 text-center text-on-surface-variant">
                    <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">hourglass_empty</span>
                    <p class="font-semibold text-lg">Loading your classes...</p>
                </div>
            </div>
        </div>

        <!-- CREATE / ADD CLASS MODAL -->
        <div id="add-class-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-lg mx-4 overflow-hidden border border-outline-variant/20">
                <div class="p-6 border-b border-outline-variant/30 flex items-center justify-between bg-surface-subtle">
                    <div>
                        <h3 class="text-xl font-bold text-primary">Create Subject Class</h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Class will be automatically assigned to <strong class="text-primary"><?= htmlspecialchars($teacher_name) ?></strong></p>
                    </div>
                    <button onclick="document.getElementById('add-class-modal').classList.add('hidden')" class="text-on-surface-variant hover:text-error transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-4 max-h-[70vh] overflow-y-auto">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Course / Program</label>
                            <select id="new-program" onchange="populateSubjectDropdown('new')" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold">
                                <option value="AIS" selected>AIS (Associate in Information Systems)</option>
                                <option value="BSIS">BSIS (Bachelor of Science in Information Systems)</option>
                                <option value="BSBA - HR">BSBA - HR (Human Resource Management)</option>
                                <option value="BSBA - FM">BSBA - FM (Financial Management)</option>
                                <option value="BSBA - MM">BSBA - MM (Marketing Management)</option>
                                <option value="BSEd">BSEd (Secondary Education)</option>
                                <option value="BEEd">BEEd (Elementary Education)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Section</label>
                            <select id="new-section" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold font-mono">
                                <option value="2A" selected>2A</option>
                                <option value="1A">1A</option>
                                <option value="1B">1B</option>
                                <option value="2B">2B</option>
                                <option value="3A">3A</option>
                                <option value="3B">3B</option>
                                <option value="4A">4A</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dependent Subject Dropdown based on Course -->
                    <div>
                        <label class="block text-xs font-bold text-primary mb-1">Select Subject (Auto-Filtered by Course)</label>
                        <select id="new-subject-select" onchange="onSubjectSelectChange('new')" class="w-full px-3.5 py-2.5 bg-surface border border-outline-variant/60 rounded-xl focus:outline-none focus:border-primary text-xs font-bold text-primary">
                            <!-- Populated dynamically based on chosen Course -->
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div class="col-span-1">
                            <label class="block text-xs font-semibold text-on-surface mb-1">Subject Code</label>
                            <input type="text" id="new-code" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/50 rounded-xl font-mono uppercase font-bold text-xs" readonly>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-semibold text-on-surface mb-1">Subject Description</label>
                            <input type="text" id="new-title" class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant/50 rounded-xl font-semibold text-xs text-on-surface" readonly>
                        <input type="hidden" id="new-units" value="3.0">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Schedule Day</label>
                            <select id="new-day" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-semibold">
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday" selected>Saturday</option>
                                <option value="Sunday">Sunday</option>
                                <option value="TBA">TBA / Arranged</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Time Slot (+3h Standard Period)</label>
                            <select id="new-time-range" class="w-full px-3 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-xs font-mono font-semibold">
                                <option value="06:00 AM - 09:00 AM">06:00 AM - 09:00 AM</option>
                                <option value="06:30 AM - 09:30 AM">06:30 AM - 09:30 AM</option>
                                <option value="07:00 AM - 10:00 AM" selected>07:00 AM - 10:00 AM</option>
                                <option value="07:30 AM - 10:30 AM">07:30 AM - 10:30 AM</option>
                                <option value="08:00 AM - 11:00 AM">08:00 AM - 11:00 AM</option>
                                <option value="08:30 AM - 11:30 AM">08:30 AM - 11:30 AM</option>
                                <option value="09:00 AM - 12:00 PM">09:00 AM - 12:00 PM</option>
                                <option value="09:30 AM - 12:30 PM">09:30 AM - 12:30 PM</option>
                                <option value="10:00 AM - 01:00 PM">10:00 AM - 01:00 PM</option>
                                <option value="10:30 AM - 01:30 PM">10:30 AM - 01:30 PM</option>
                                <option value="11:00 AM - 02:00 PM">11:00 AM - 02:00 PM</option>
                                <option value="01:00 PM - 04:00 PM">01:00 PM - 04:00 PM</option>
                                <option value="01:30 PM - 04:30 PM">01:30 PM - 04:30 PM</option>
                                <option value="02:00 PM - 05:00 PM">02:00 PM - 05:00 PM</option>
                                <option value="02:30 PM - 05:30 PM">02:30 PM - 05:30 PM</option>
                                <option value="03:00 PM - 06:00 PM">03:00 PM - 06:00 PM</option>
                                <option value="03:30 PM - 06:30 PM">03:30 PM - 06:30 PM</option>
                                <option value="04:00 PM - 07:00 PM">04:00 PM - 07:00 PM</option>
                                <option value="04:30 PM - 07:30 PM">04:30 PM - 07:30 PM</option>
                                <option value="05:00 PM - 08:00 PM">05:00 PM - 08:00 PM</option>
                                <option value="05:30 PM - 08:30 PM">05:30 PM - 08:30 PM</option>
                                <option value="06:00 PM - 09:00 PM">06:00 PM - 09:00 PM</option>
                                <option value="06:30 PM - 09:30 PM">06:30 PM - 09:30 PM</option>
                                <option value="TBA">TBA / Arranged</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Room Assignment</label>
                            <input type="text" id="new-room" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-sm" placeholder="e.g. Room 302 / Lab 2">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-on-surface mb-1">Units</label>
                            <input type="number" step="0.5" id="new-units" class="w-full px-4 py-2 bg-surface border border-outline-variant/50 rounded-xl focus:outline-none focus:border-primary text-sm font-mono font-bold" value="3.0">
                        </div>
                    </div>

                    <!-- Auto-Attached Instructor Banner -->
                    <div class="p-3 bg-surface-subtle border border-outline-variant/60 rounded-xl text-xs flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary text-[18px]">verified_user</span>
                        <div>
                            <span class="text-on-surface-variant">Instructor assigned:</span>
                            <strong class="text-primary font-bold block"><?= htmlspecialchars($teacher_name) ?> (<?= htmlspecialchars($teacher_email) ?>)</strong>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-outline-variant/30 bg-surface-subtle flex justify-end gap-3">
                    <button onclick="document.getElementById('add-class-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-on-surface-variant hover:bg-surface-variant transition-colors font-semibold text-sm">Cancel</button>
                    <button onclick="saveNewFacultyClass()" class="px-5 py-2 bg-primary text-on-primary rounded-xl hover:opacity-90 transition-opacity font-semibold text-sm npc-navy-card">Create Class</button>
                </div>
            </div>
        </div>
    </main>

    <script>
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        const currentTeacherName = <?= json_encode($teacher_name) ?>;
        const currentTeacherEmail = <?= json_encode($teacher_email) ?>;
        const isAdmin = <?= json_encode($is_admin) ?>;
        const csrfToken = <?= json_encode($csrf_token) ?>;

        let myClasses = [];

        
        const npcCurriculum = {
            'AIS': [
                { code: 'DM103', title: 'BUSINESS PROCESS MANAGEMENT', units: 3.0 },
                { code: 'CC107', title: 'COMPUTER PROGRAMMING 3', units: 3.0 },
                { code: 'PATHFIT 3', title: 'DANCE', units: 2.0 },
                { code: 'IS105', title: 'ENTERPRISE ARCHITECTURE', units: 3.0 },
                { code: 'DM102', title: 'FINANCIAL MANAGEMENT', units: 3.0 },
                { code: 'ADV02', title: 'HUMAN COMPUTER INTERACTION', units: 3.0 },
                { code: 'ADV04', title: 'IS INNOVATIONS AND NEW TECHNOLOGIES', units: 3.0 },
                { code: 'QUAMETH', title: 'QUANTITATIVE METHODS', units: 3.0 },
                { code: 'ADV03', title: 'TECHNOPRENUERSHIP', units: 3.0 },
                { code: 'CC101', title: 'INTRODUCTION TO COMPUTING', units: 3.0 },
                { code: 'CC102', title: 'COMPUTER PROGRAMMING 1', units: 3.0 },
                { code: 'CC103', title: 'COMPUTER PROGRAMMING 2', units: 3.0 },
                { code: 'IS101', title: 'FUNDAMENTALS OF INFORMATION SYSTEMS', units: 3.0 },
                { code: 'IS102', title: 'IT INFRASTRUCTURE AND NETWORK', units: 3.0 },
                { code: 'IS103', title: 'SYSTEMS ANALYSIS AND DESIGN', units: 3.0 },
                { code: 'IS104', title: 'DATABASE MANAGEMENT SYSTEMS', units: 3.0 }
            ],
            'BSIS': [
                { code: 'IS101', title: 'FUNDAMENTALS OF INFORMATION SYSTEMS', units: 3.0 },
                { code: 'IS102', title: 'IT INFRASTRUCTURE AND NETWORK', units: 3.0 },
                { code: 'IS103', title: 'SYSTEMS ANALYSIS AND DESIGN', units: 3.0 },
                { code: 'IS104', title: 'DATABASE MANAGEMENT SYSTEMS', units: 3.0 },
                { code: 'IS105', title: 'ENTERPRISE ARCHITECTURE', units: 3.0 },
                { code: 'IS106', title: 'INFORMATION ASSURANCE AND SECURITY', units: 3.0 },
                { code: 'IS107', title: 'IS STRATEGY, MANAGEMENT, AND ACQUISITION', units: 3.0 },
                { code: 'IS108', title: 'DATA MINING AND WAREHOUSING', units: 3.0 },
                { code: 'CC101', title: 'INTRODUCTION TO COMPUTING', units: 3.0 },
                { code: 'CC102', title: 'COMPUTER PROGRAMMING 1', units: 3.0 },
                { code: 'CC103', title: 'COMPUTER PROGRAMMING 2', units: 3.0 },
                { code: 'CC104', title: 'DATA STRUCTURES AND ALGORITHMS', units: 3.0 },
                { code: 'CC105', title: 'APPLICATION DEV AND EMERGING TECH', units: 3.0 },
                { code: 'CC106', title: 'OBJECT-ORIENTED PROGRAMMING', units: 3.0 },
                { code: 'CC107', title: 'COMPUTER PROGRAMMING 3', units: 3.0 },
                { code: 'QUAMETH', title: 'QUANTITATIVE METHODS', units: 3.0 },
                { code: 'TECHNO', title: 'TECHNOPRENEURSHIP 101', units: 3.0 },
                { code: 'CAPSTONE1', title: 'IS CAPSTONE PROJECT 1', units: 3.0 },
                { code: 'CAPSTONE2', title: 'IS CAPSTONE PROJECT 2', units: 3.0 },
                { code: 'OJT', title: 'INDUSTRY PRACTICUM', units: 6.0 }
            ],
            'BSBA - HR': [
                { code: 'HR101', title: 'HUMAN RESOURCE MANAGEMENT', units: 3.0 },
                { code: 'HR102', title: 'RECRUITMENT AND SELECTION', units: 3.0 },
                { code: 'HR103', title: 'TRAINING AND DEVELOPMENT', units: 3.0 },
                { code: 'HR104', title: 'COMPENSATION AND BENEFITS', units: 3.0 },
                { code: 'HR105', title: 'LABOR LAWS AND LEGISLATION', units: 3.0 },
                { code: 'HR106', title: 'ORGANIZATIONAL BEHAVIOR', units: 3.0 },
                { code: 'BA101', title: 'PRINCIPLES OF MANAGEMENT', units: 3.0 },
                { code: 'BA102', title: 'BUSINESS COMMUNICATIONS', units: 3.0 },
                { code: 'BA103', title: 'TOTAL QUALITY MANAGEMENT', units: 3.0 }
            ],
            'BSBA - FM': [
                { code: 'FM101', title: 'FINANCIAL MANAGEMENT 1', units: 3.0 },
                { code: 'FM102', title: 'FINANCIAL MANAGEMENT 2', units: 3.0 },
                { code: 'FM103', title: 'BANKING AND FINANCIAL INSTITUTIONS', units: 3.0 },
                { code: 'FM104', title: 'INVESTMENT AND PORTFOLIO MANAGEMENT', units: 3.0 },
                { code: 'FM105', title: 'PUBLIC FINANCE', units: 3.0 },
                { code: 'FM106', title: 'CREDIT AND COLLECTION', units: 3.0 },
                { code: 'BA101', title: 'PRINCIPLES OF MANAGEMENT', units: 3.0 },
                { code: 'BA104', title: 'BUSINESS TAXATION', units: 3.0 }
            ],
            'BSBA - MM': [
                { code: 'MM101', title: 'MARKETING MANAGEMENT', units: 3.0 },
                { code: 'MM102', title: 'CONSUMER BEHAVIOR', units: 3.0 },
                { code: 'MM103', title: 'PROFESSIONAL SALESMANSHIP', units: 3.0 },
                { code: 'MM104', title: 'ADVERTISING AND SALES PROMOTION', units: 3.0 },
                { code: 'MM105', title: 'STRATEGIC MARKETING', units: 3.0 },
                { code: 'MM106', title: 'E-COMMERCE AND DIGITAL MARKETING', units: 3.0 },
                { code: 'BA101', title: 'PRINCIPLES OF MANAGEMENT', units: 3.0 }
            ],
            'BSEd': [
                { code: 'ED101', title: 'CHILD AND ADOLESCENT LEARNERS', units: 3.0 },
                { code: 'ED102', title: 'THE TEACHING PROFESSION', units: 3.0 },
                { code: 'ED103', title: 'ASSESSMENT IN LEARNING 1', units: 3.0 },
                { code: 'ED104', title: 'ASSESSMENT IN LEARNING 2', units: 3.0 },
                { code: 'ED105', title: 'CURRICULUM DEVELOPMENT', units: 3.0 },
                { code: 'ED106', title: 'TEACHING STRATEGIES AND METHODOLOGIES', units: 3.0 },
                { code: 'ED107', title: 'EDUCATIONAL TECHNOLOGY', units: 3.0 },
                { code: 'ED108', title: 'FIELD STUDY AND PRACTICE TEACHING', units: 6.0 }
            ],
            'BEEd': [
                { code: 'BED101', title: 'TEACHING LITERACY IN ELEMENTARY GRADES', units: 3.0 },
                { code: 'BED102', title: 'TEACHING SCIENCE IN ELEMENTARY GRADES', units: 3.0 },
                { code: 'BED103', title: 'TEACHING MATHEMATICS IN PRIMARY GRADES', units: 3.0 },
                { code: 'BED104', title: 'TEACHING SOCIAL STUDIES IN ELEMENTARY', units: 3.0 },
                { code: 'ED101', title: 'CHILD AND ADOLESCENT DEVELOPMENT', units: 3.0 },
                { code: 'ED102', title: 'THE TEACHING PROFESSION', units: 3.0 },
                { code: 'ED105', title: 'CURRICULUM DEVELOPMENT', units: 3.0 },
                { code: 'BED108', title: 'ELEMENTARY FIELD STUDY & TEACHING', units: 6.0 }
            ]
        };

        function populateSubjectDropdown(prefix) {
            const prog = document.getElementById(`${prefix}-program`).value;
            const select = document.getElementById(`${prefix}-subject-select`);
            const list = npcCurriculum[prog] || npcCurriculum['AIS'];

            select.innerHTML = list.map((item, idx) => `
                <option value="${idx}">${item.code} - ${item.title} (${item.units} Units)</option>
            `).join('');

            onSubjectSelectChange(prefix);
        }

        function onSubjectSelectChange(prefix) {
            const progEl = document.getElementById(`${prefix}-program`);
            const select = document.getElementById(`${prefix}-subject-select`);
            if (!progEl || !select) return;
            const prog = progEl.value;
            const list = npcCurriculum[prog] || npcCurriculum['AIS'];
            const chosen = list[select.selectedIndex] || list[0];

            if (chosen) {
                const codeEl = document.getElementById(`${prefix}-code`);
                if (codeEl) codeEl.value = chosen.code;
                const titleEl = document.getElementById(`${prefix}-title`);
                if (titleEl) titleEl.value = chosen.title;
                const unitsEl = document.getElementById(`${prefix}-units`);
                if (unitsEl) unitsEl.value = chosen.units;
            }
        }

        async function loadClasses() {
            const grid = document.getElementById('classes-list-grid');
            try {
                const { data, error } = await supabaseClient.from('classes').select('*').order('code', { ascending: true });
                if (error) throw error;

                // STRICT FACULTY SCOPING: Faculty only sees classes they created or where their email/name matches
                const all = data || [];
                myClasses = all.filter(c => {
                    const cEmail = (c.created_by_email || '').toLowerCase().trim();
                    const myEmail = (currentTeacherEmail || '').toLowerCase().trim();
                    return cEmail === myEmail;
                });

                renderClasses(myClasses);
            } catch (err) {
                console.error(err);
            }
        }

        function renderClasses(list) {
            const grid = document.getElementById('classes-list-grid');
            if (!list || list.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full bg-surface-container-lowest border border-outline-variant rounded-2xl p-12 text-center text-on-surface-variant shadow-sm">
                        <span class="material-symbols-outlined text-[48px] text-outline-variant mb-2 block">school</span>
                        <p class="font-bold text-lg text-primary mb-1">No Classes Created Yet</p>
                        <p class="text-xs text-on-surface-variant mb-4">Click "Create Class" above to add your first subject schedule.</p>
                        <button onclick="document.getElementById('add-class-modal').classList.remove('hidden')" class="bg-primary text-on-primary px-4 py-2 rounded-xl text-xs font-semibold npc-navy-card">
                            + Create Class
                        </button>
                    </div>
                `;
                return;
            }

            grid.innerHTML = list.map(c => `
                <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="px-2.5 py-0.5 bg-primary/10 text-primary font-mono text-xs font-bold rounded-lg">${c.code}</span>
                            <span class="text-xs font-mono font-bold bg-surface-container-low text-on-surface px-2 py-0.5 rounded">${c.section || 'AIS 2A'}</span>
                        </div>
                        <h3 class="font-bold text-base text-primary mb-2">${c.title}</h3>
                        <div class="space-y-1.5 text-xs text-on-surface-variant font-medium">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-status-info">schedule</span>
                                <span>${c.schedule_day || 'TBA'} (${c.start_time || 'TBA'}${c.end_time && c.end_time !== 'TBA' ? ' - ' + c.end_time : ''})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-secondary">location_on</span>
                                <span>${c.room || 'Room TBA'}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-primary">person</span>
                                <span>${c.instructor || currentTeacherName}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-outline-variant/30 flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <a href="teacher_attendance.php?class_id=${c.id}" class="flex-1 bg-primary text-on-primary py-2.5 rounded-xl text-center text-xs font-bold hover:opacity-90 flex items-center justify-center gap-1 shadow-sm npc-navy-card">
                                <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span> Attendance
                            </a>
                            <a href="teacher_grades.php?class_id=${c.id}" class="flex-1 bg-secondary-container text-on-secondary-container py-2.5 rounded-xl text-center text-xs font-bold hover:brightness-95 flex items-center justify-center gap-1 shadow-sm">
                                <span class="material-symbols-outlined text-[16px]">table_chart</span> Encode Grades
                            </a>
                        </div>
                        <div class="flex justify-end">
                            <button onclick="deleteFacultyClass('${c.id}')" class="text-error hover:underline text-[11px] font-semibold flex items-center gap-0.5 mt-1">
                                <span class="material-symbols-outlined text-[13px]">delete</span> Delete Class
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        async function saveNewFacultyClass() {
            const code = document.getElementById('new-code').value.trim();
            const program = document.getElementById('new-program').value;
            const section = document.getElementById('new-section').value;
            const combinedSection = `${program} ${section}`;
            const title = document.getElementById('new-title').value.trim();
            const day = document.getElementById('new-day').value;
            const timeRange = document.getElementById('new-time-range').value;
            const room = document.getElementById('new-room').value.trim();
            const units = parseFloat(document.getElementById('new-units').value) || 3.0;

            if (!code || !title) return alert('Please provide at least Subject Code and Title.');

            let startTime = 'TBA';
            let endTime = 'TBA';
            if (timeRange.includes('-')) {
                const parts = timeRange.split('-');
                startTime = parts[0].trim();
                endTime = parts[1].trim();
            } else if (timeRange) {
                startTime = timeRange;
            }

            // Create class via secured server endpoint (ownership stamped server-side)
            let ok = false;
            try {
                const res = await fetch('api_faculty.php?action=create_class', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({
                        code: code,
                        title: title,
                        section: combinedSection,
                        schedule_day: day,
                        start_time: startTime,
                        end_time: endTime,
                        instructor: currentTeacherName,
                        room: room || 'Room TBA',
                        units: units,
                        csrf_token: csrfToken
                    })
                });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Server rejected the new class.');
                ok = true;
            } catch (err) {
                alert('Error creating class: ' + err.message);
            }

            if (ok) {
                document.getElementById('add-class-modal').classList.add('hidden');
                document.getElementById('new-code').value = '';
                document.getElementById('new-title').value = '';
                document.getElementById('new-room').value = '';
                populateSubjectDropdown('new');
        loadClasses();
            }
        }

        async function deleteFacultyClass(id) {
            if (!confirm('Are you sure you want to delete this class?')) return;
            let ok = false;
            let msg = '';
            try {
                const res = await fetch('api_faculty.php?action=delete_class', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ id: id, csrf_token: csrfToken })
                });
                const data = await res.json();
                ok = !!data.success;
                msg = data.message || '';
            } catch (err) {
                msg = err.message;
            }
            if (!ok) alert('Error: ' + (msg || 'Delete failed.'));
            populateSubjectDropdown('new');
        loadClasses();
        }

        function filterClasses() {
            const q = document.getElementById('class-search').value.toLowerCase();
            const filtered = myClasses.filter(c => 
                (c.code && c.code.toLowerCase().includes(q)) || 
                (c.title && c.title.toLowerCase().includes(q)) || 
                (c.section && c.section.toLowerCase().includes(q))
            );
            renderClasses(filtered);
        }

        populateSubjectDropdown('new');
        loadClasses();
    </script>
</body>
</html>
