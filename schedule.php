<?php
require_once 'auth.php';
require_login();
$is_logged_in = isset($_SESSION['user_id']);
$raw_name = (isset($_SESSION['name']) && $_SESSION['name'] !== null) ? (string)$_SESSION['name'] : 'Guest User';
$user_name = $is_logged_in ? explode(' ', trim($raw_name))[0] : 'Guest';
$full_name = $is_logged_in ? (string)$_SESSION['name'] : 'Guest User';
$user_id_display = $is_logged_in && isset($_SESSION['student_number']) ? (string)$_SESSION['student_number'] : 'GUEST';
$user_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$student_program = isset($_SESSION['program']) ? $_SESSION['program'] : 'AIS';
$student_section = isset($_SESSION['section']) ? $_SESSION['section'] : '2A';
$assigned_section = trim("$student_program $student_section");
$jsConfig = getJsConfig();
$csrf_token = getCsrfToken();
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NPC Connect - My Class Schedule</title>
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
    <style>
        /* On-screen 7-day Grid */
        .timetable-7day-grid {
            display: grid;
            grid-template-columns: 75px repeat(7, minmax(130px, 1fr));
            gap: 1px;
            background-color: #cbd5e1;
        }
        .timetable-cell {
            background-color: #ffffff;
            padding: 4px;
            min-height: 80px;
            position: relative;
        }
        .timetable-header {
            background-color: #F8FAFC;
            padding: 8px 4px;
            font-weight: 700;
            text-align: center;
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            color: #0b1c30;
        }
        .event-lecture {
            background-color: #eff4ff;
            border-left: 3.5px solid #002b5c;
        }
        .event-lab {
            background-color: #fef3c7;
            border-left: 3.5px solid #b45309;
        }
        .event-evening {
            background-color: #f3e8ff;
            border-left: 3.5px solid #6b21a8;
        }

        /* 🖨️ PERFECT PRINT STYLES */
        @media print {
            @page {
                size: letter portrait;
                margin: 12mm 15mm;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 10pt !important;
            }
            /* Hide UI chrome */
            aside, #topbar, #student-nav, .no-print, button, #view-toggle-bar, #tba-subjects-container, #grid-view-container {
                display: none !important;
            }
            #app-root, #main-wrapper, main, #canvas-container {
                display: block !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }
            /* Print Header */
            #print-header {
                display: block !important;
                border-bottom: 2px solid #000000;
                padding-bottom: 8px;
                margin-bottom: 15px;
            }
            /* Print Table */
            #table-view-container {
                display: block !important;
                border: 1px solid #000000 !important;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }
            th, td {
                border: 1px solid #333333 !important;
                padding: 6px 8px !important;
                font-size: 9pt !important;
                color: #000000 !important;
            }
            th {
                background-color: #f0f0f0 !important;
                font-weight: bold !important;
                text-transform: uppercase !important;
            }
            tr {
                page-break-inside: avoid !important;
            }
            .time-cell {
                white-space: nowrap !important;
                font-weight: 600 !important;
            }
            #print-footer {
                display: block !important;
                margin-top: 20px;
                font-size: 8pt;
                color: #555555;
            }
        }
        #print-header, #print-footer {
            display: none;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
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
                    <h2 class="text-xl font-bold text-primary hidden lg:block" id="page-title">My Class Schedule</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-semibold bg-surface-container px-3 py-1.5 rounded-md border border-outline-variant text-primary" id="user-id-chip">ID: <?php echo htmlspecialchars($user_id_display); ?></span>
                        <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                            <?php echo strtoupper(substr($full_name, 0, 1)); ?>
                        </div>
                        <span class="text-sm font-semibold text-primary hidden sm:inline" id="user-name-display"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                </div>
            </header>

            <!-- Page Canvas -->
            <main class="flex-1 p-6 md:p-10 max-w-7xl w-full mx-auto space-y-6 flex flex-col lg:pl-64" id="canvas-container">
                
                <!-- 🖨️ PRINT-ONLY OFFICIAL HEADER -->
                <div id="print-header">
                    <div class="flex items-center gap-4 mb-3">
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg" class="w-14 h-14 object-cover" alt="NPC Logo">
                        <div>
                            <h2 class="text-lg font-bold uppercase tracking-wide text-black">Navotas Polytechnic College</h2>
                            <p class="text-xs text-gray-700">Office of the College Registrar • Academic Year 2026-2027</p>
                            <h3 class="text-sm font-bold uppercase text-black mt-0.5">Official Student Class Schedule</h3>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs border-t border-b border-gray-400 py-2">
                        <div>
                            <p><strong>Student Name:</strong> <?php echo htmlspecialchars($full_name); ?></p>
                            <p><strong>Student Number:</strong> <?php echo htmlspecialchars($user_id_display); ?></p>
                        </div>
                        <div class="text-right">
                            <p><strong>Course & Section:</strong> <span id="print-section-display"><?php echo htmlspecialchars($assigned_section); ?></span></p>
                            <p><strong>Date Printed:</strong> <?php echo date('F d, Y'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- On-Screen Student Section Banner (Locked to their section, NO selection choices) -->
                <div class="bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl font-bold text-primary tracking-tight">Enrolled Class Schedule</h1>
                            <span class="px-3 py-1 rounded-full text-xs font-mono font-bold bg-primary text-white shadow-sm" id="student-section-pill"><?php echo htmlspecialchars($assigned_section); ?></span>
                        </div>
                        <p class="text-xs text-on-surface-variant mt-1">Official lecture and laboratory timetable for your enrolled section.</p>
                    </div>

                    <!-- Action Controls -->
                    <div class="flex items-center gap-3" id="view-toggle-bar">
                        <!-- View Toggle -->
                        <div class="flex items-center bg-surface border border-outline-variant/60 rounded-xl p-1">
                            <button id="view-table-btn" onclick="setViewMode('table')" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white flex items-center gap-1 shadow-sm transition-all">
                                <span class="material-symbols-outlined text-[14px]">table_rows</span> List Table
                            </button>
                            <button id="view-grid-btn" onclick="setViewMode('grid')" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary flex items-center gap-1 transition-all">
                                <span class="material-symbols-outlined text-[14px]">grid_view</span> 7-Day Grid
                            </button>
                        </div>

                        <!-- Print Button -->
                        <button onclick="window.print()" class="px-4 py-2 bg-primary text-on-primary hover:opacity-90 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition-all shadow-sm npc-navy-card">
                            <span class="material-symbols-outlined text-[16px]">print</span> Print Schedule
                        </button>
                    </div>
                </div>

                <!-- 1. TABLE LIST VIEW (DEFAULT & PERFECT FOR PRINTING) -->
                <div id="table-view-container" class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-4 px-6 border-b border-outline-variant bg-surface-subtle flex justify-between items-center no-print">
                        <h3 class="font-bold text-primary text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">list_alt</span>
                            Enrolled Subject Offerings
                        </h3>
                        <span class="text-xs font-mono text-on-surface-variant" id="total-units-badge">Loading units...</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-surface-container-low font-mono uppercase text-on-surface border-b border-outline-variant">
                                    <th class="py-3 px-4 font-bold">Course & Section</th>
                                    <th class="py-3 px-4 font-bold">Subject Code</th>
                                    <th class="py-3 px-4 font-bold">Subject Description</th>
                                    <th class="py-3 px-4 font-bold">Day</th>
                                    <th class="py-3 px-4 font-bold time-cell">Time</th>
                                    <th class="py-3 px-4 font-bold">Professor</th>
                                    <th class="py-3 px-4 font-bold text-right">Units</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-table-tbody" class="divide-y divide-outline-variant/30 font-medium">
                                <tr><td colspan="7" class="p-8 text-center text-on-surface-variant">Loading your section schedule...</td></tr>
                            </tbody>
                            <tfoot class="border-t-2 border-outline-variant bg-surface-subtle font-bold">
                                <tr>
                                    <td colspan="6" class="py-3 px-4 text-right uppercase font-mono">Total Academic Units:</td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-primary" id="table-total-units">—</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- 🖨️ PRINT-ONLY OFFICIAL FOOTER -->
                <div id="print-footer">
                    <div class="flex justify-between items-center text-xs mt-6 pt-4 border-t border-gray-400">
                        <div>
                            <p>Certified by: <strong>Office of Academic Affairs</strong></p>
                            <p class="text-[9px] text-gray-500 mt-1">This document serves as the official class schedule for the stated academic term.</p>
                        </div>
                        <div class="text-right">
                            <p>Registrar Signature: _______________________</p>
                        </div>
                    </div>
                </div>

                <!-- 2. TIMETABLE GRID VIEW (7 DAYS: SUNDAY TO SATURDAY) -->
                <div id="grid-view-container" class="hidden bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm overflow-hidden flex flex-col flex-1">
                    <div class="overflow-x-auto">
                        <div class="min-w-[1080px]">
                            <!-- Grid Headers -->
                            <div class="timetable-7day-grid border-b border-outline-variant">
                                <div class="timetable-header border-r border-outline-variant/50">TIME</div>
                                <div class="timetable-header">SUNDAY</div>
                                <div class="timetable-header">MONDAY</div>
                                <div class="timetable-header">TUESDAY</div>
                                <div class="timetable-header">WEDNESDAY</div>
                                <div class="timetable-header">THURSDAY</div>
                                <div class="timetable-header">FRIDAY</div>
                                <div class="timetable-header bg-primary/5 text-primary">SATURDAY</div>
                            </div>

                            <!-- Grid Time Slots -->
                            <div id="timetable-body">
                                <!-- 07:00 AM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">07:00 AM</div>
                                    <div class="timetable-cell" id="slot-Sunday-0700"></div>
                                    <div class="timetable-cell" id="slot-Monday-0700"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-0700"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-0700"></div>
                                    <div class="timetable-cell" id="slot-Thursday-0700"></div>
                                    <div class="timetable-cell" id="slot-Friday-0700"></div>
                                    <div class="timetable-cell" id="slot-Saturday-0700"></div>
                                </div>

                                <!-- 08:00 AM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">08:00 AM</div>
                                    <div class="timetable-cell" id="slot-Sunday-0800"></div>
                                    <div class="timetable-cell" id="slot-Monday-0800"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-0800"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-0800"></div>
                                    <div class="timetable-cell" id="slot-Thursday-0800"></div>
                                    <div class="timetable-cell" id="slot-Friday-0800"></div>
                                    <div class="timetable-cell" id="slot-Saturday-0800"></div>
                                </div>

                                <!-- 09:00 AM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">09:00 AM</div>
                                    <div class="timetable-cell" id="slot-Sunday-0900"></div>
                                    <div class="timetable-cell" id="slot-Monday-0900"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-0900"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-0900"></div>
                                    <div class="timetable-cell" id="slot-Thursday-0900"></div>
                                    <div class="timetable-cell" id="slot-Friday-0900"></div>
                                    <div class="timetable-cell" id="slot-Saturday-0900"></div>
                                </div>

                                <!-- 10:00 AM (10:30 AM starts here) -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">10:00 AM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1000"></div>
                                    <div class="timetable-cell" id="slot-Monday-1000"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1000"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1000"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1000"></div>
                                    <div class="timetable-cell" id="slot-Friday-1000"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1000"></div>
                                </div>

                                <!-- 11:00 AM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">11:00 AM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1100"></div>
                                    <div class="timetable-cell" id="slot-Monday-1100"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1100"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1100"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1100"></div>
                                    <div class="timetable-cell" id="slot-Friday-1100"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1100"></div>
                                </div>

                                <!-- 12:00 PM (Midday Break) -->
                                <div class="timetable-7day-grid bg-surface-subtle/80">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">12:00 PM</div>
                                    <div class="col-span-7 flex items-center justify-center border-t border-b border-outline-variant/30 text-xs font-mono text-on-surface-variant uppercase tracking-widest py-2 bg-surface-container-low/40">
                                        ☕ Midday Break
                                    </div>
                                </div>

                                <!-- 01:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">01:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1300"></div>
                                    <div class="timetable-cell" id="slot-Monday-1300"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1300"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1300"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1300"></div>
                                    <div class="timetable-cell" id="slot-Friday-1300"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1300"></div>
                                </div>

                                <!-- 02:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">02:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1400"></div>
                                    <div class="timetable-cell" id="slot-Monday-1400"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1400"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1400"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1400"></div>
                                    <div class="timetable-cell" id="slot-Friday-1400"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1400"></div>
                                </div>

                                <!-- 03:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">03:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1500"></div>
                                    <div class="timetable-cell" id="slot-Monday-1500"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1500"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1500"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1500"></div>
                                    <div class="timetable-cell" id="slot-Friday-1500"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1500"></div>
                                </div>

                                <!-- 04:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">04:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1600"></div>
                                    <div class="timetable-cell" id="slot-Monday-1600"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1600"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1600"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1600"></div>
                                    <div class="timetable-cell" id="slot-Friday-1600"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1600"></div>
                                </div>

                                <!-- 05:00 PM (05:30 PM evening starts here) -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">05:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1700"></div>
                                    <div class="timetable-cell" id="slot-Monday-1700"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1700"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1700"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1700"></div>
                                    <div class="timetable-cell" id="slot-Friday-1700"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1700"></div>
                                </div>

                                <!-- 06:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">06:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1800"></div>
                                    <div class="timetable-cell" id="slot-Monday-1800"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1800"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1800"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1800"></div>
                                    <div class="timetable-cell" id="slot-Friday-1800"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1800"></div>
                                </div>

                                <!-- 07:00 PM -->
                                <div class="timetable-7day-grid">
                                    <div class="timetable-cell bg-surface-subtle text-right text-[11px] font-mono font-bold text-on-surface-variant border-r border-outline-variant/50 flex flex-col justify-start pt-2 pr-1.5">07:00 PM</div>
                                    <div class="timetable-cell" id="slot-Sunday-1900"></div>
                                    <div class="timetable-cell" id="slot-Monday-1900"></div>
                                    <div class="timetable-cell" id="slot-Tuesday-1900"></div>
                                    <div class="timetable-cell" id="slot-Wednesday-1900"></div>
                                    <div class="timetable-cell" id="slot-Thursday-1900"></div>
                                    <div class="timetable-cell" id="slot-Friday-1900"></div>
                                    <div class="timetable-cell" id="slot-Saturday-1900"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TBA / Arranged Subjects Card Section -->
                <div id="tba-subjects-container" class="hidden bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm no-print">
                    <h3 class="font-bold text-primary text-sm mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[18px]">schedule</span>
                        TBA / Arranged Schedule Subjects
                    </h3>
                    <div id="tba-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"></div>
                </div>

            </main>
        </div>
    </div>

    <script>
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        const currentStudentSection = <?= json_encode($assigned_section) ?>;
        const currentStudentEmail = <?= json_encode($user_email) ?>;

        let myClasses = [];

        async function initStudentSchedule() {
            try {
                // 1. Get student assigned section from user profile in Supabase if exists
                if (currentStudentEmail) {
                    const { data: userProfile } = await supabaseClient.from('users').select('program, section').eq('email', currentStudentEmail).single();
                    if (userProfile && userProfile.program && userProfile.section) {
                        const sec = `${userProfile.program} ${userProfile.section}`.trim();
                        document.getElementById('student-section-pill').innerText = sec;
                        document.getElementById('print-section-display').innerText = sec;
                    }
                }

                // 2. Fetch all classes for this student's section
                const { data, error } = await supabaseClient.from('classes').select('*').order('code', { ascending: true });
                if (error) throw error;

                const activeSection = document.getElementById('student-section-pill').innerText.toUpperCase();

                // Filter strictly for this student's section
                myClasses = (data || []).filter(c => {
                    const sec = c.section ? c.section.toUpperCase() : '';
                    return sec.includes(activeSection) || activeSection.includes(sec);
                });

                // Calculate total units
                let totalUnits = 0;
                myClasses.forEach(c => {
                    totalUnits += parseFloat(c.units) || 3.0;
                });
                document.getElementById('table-total-units').innerText = totalUnits.toFixed(1);
                document.getElementById('total-units-badge').innerText = `Total: ${totalUnits.toFixed(1)} Units (${myClasses.length} Subjects)`;

                renderTable(myClasses);
                renderGrid(myClasses);
                renderTba(myClasses);

            } catch (err) {
                console.error('Error loading student schedule:', err);
            }
        }

        function setViewMode(mode) {
            const gridContainer = document.getElementById('grid-view-container');
            const tableContainer = document.getElementById('table-view-container');
            const gridBtn = document.getElementById('view-grid-btn');
            const tableBtn = document.getElementById('view-table-btn');

            if (mode === 'grid') {
                gridContainer.classList.remove('hidden');
                tableContainer.classList.add('hidden');
                gridBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white flex items-center gap-1 shadow-sm transition-all';
                tableBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary flex items-center gap-1 transition-all';
            } else {
                gridContainer.classList.add('hidden');
                tableContainer.classList.remove('hidden');
                tableBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold bg-primary text-white flex items-center gap-1 shadow-sm transition-all';
                gridBtn.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-on-surface-variant hover:text-primary flex items-center gap-1 transition-all';
            }
        }

        function renderTable(classesToRender) {
            const tbody = document.getElementById('schedule-table-tbody');
            if (!classesToRender || classesToRender.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-on-surface-variant">No subjects assigned for your section yet.</td></tr>';
                return;
            }

            tbody.innerHTML = classesToRender.map(c => `
                <tr class="hover:bg-surface-subtle transition-colors">
                    <td class="py-3 px-4 font-mono font-bold text-primary">${c.section || 'AIS 2A'}</td>
                    <td class="py-3 px-4 font-mono font-bold text-primary">${c.code}</td>
                    <td class="py-3 px-4 font-semibold text-on-surface">${c.title}</td>
                    <td class="py-3 px-4 font-mono">${c.schedule_day || 'TBA'}</td>
                    <td class="py-3 px-4 font-mono text-primary font-bold time-cell">${c.start_time || 'TBA'}${c.end_time && c.end_time !== 'TBA' ? ' - ' + c.end_time : ''}</td>
                    <td class="py-3 px-4 text-on-surface-variant">${c.instructor || 'TBA'}</td>
                    <td class="py-3 px-4 text-right font-mono font-bold">${c.units || '3.0'}</td>
                </tr>
            `).join('');
        }

        function renderGrid(classesToRender) {
            const slots = document.querySelectorAll('.timetable-cell[id^="slot-"]');
            slots.forEach(slot => slot.innerHTML = '');

            classesToRender.forEach(c => {
                const day = c.schedule_day || '';
                if (day.toUpperCase() === 'TBA') return;

                let targetDay = '';
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                for (const d of days) {
                    if (day.toLowerCase().includes(d.toLowerCase())) {
                        targetDay = d;
                        break;
                    }
                }
                if (!targetDay) targetDay = 'Monday';

                let timeSlot = '0800';
                const timeStr = `${c.start_time || ''} ${c.schedule_day || ''}`;

                if (timeStr.includes('07:00') || timeStr.includes('7:00')) timeSlot = '0700';
                else if (timeStr.includes('08:00') || timeStr.includes('8:00')) timeSlot = '0800';
                else if (timeStr.includes('09:00') || timeStr.includes('9:00')) timeSlot = '0900';
                else if (timeStr.includes('10:00') || timeStr.includes('10:30') || timeStr.includes('10:')) timeSlot = '1000';
                else if (timeStr.includes('11:00') || timeStr.includes('11:')) timeSlot = '1100';
                else if (timeStr.includes('01:00') || timeStr.includes('1:00') || timeStr.includes('13:00')) timeSlot = '1300';
                else if (timeStr.includes('02:00') || timeStr.includes('2:00') || timeStr.includes('14:00')) timeSlot = '1400';
                else if (timeStr.includes('03:00') || timeStr.includes('3:00') || timeStr.includes('15:00')) timeSlot = '1500';
                else if (timeStr.includes('04:00') || timeStr.includes('4:00') || timeStr.includes('16:00')) timeSlot = '1600';
                else if (timeStr.includes('05:00') || timeStr.includes('05:30') || timeStr.includes('5:30') || timeStr.includes('17:00')) timeSlot = '1700';
                else if (timeStr.includes('06:00') || timeStr.includes('6:00') || timeStr.includes('18:00')) timeSlot = '1800';
                else if (timeStr.includes('07:00 PM') || timeStr.includes('19:00')) timeSlot = '1900';

                const targetSlot = document.getElementById(`slot-${targetDay}-${timeSlot}`);

                let blockClass = 'event-lecture';
                if ((c.title && c.title.toLowerCase().includes('lab')) || (c.room && c.room.toLowerCase().includes('lab'))) {
                    blockClass = 'event-lab';
                } else if (timeSlot >= '1700') {
                    blockClass = 'event-evening';
                }

                if (targetSlot) {
                    const card = document.createElement('div');
                    card.className = `${blockClass} rounded-lg p-2 shadow-sm hover:shadow-md transition-shadow mb-1 flex flex-col justify-between cursor-pointer`;
                    card.innerHTML = `
                        <div>
                            <span class="font-mono text-xs font-bold text-primary">${c.code}</span>
                            <p class="text-[11px] font-bold text-primary line-clamp-1 leading-tight mb-1" title="${c.title}">${c.title}</p>
                        </div>
                        <div class="pt-1 border-t border-black/5 text-[9px] text-on-surface-variant font-mono space-y-0.5">
                            <div class="flex items-center gap-1 font-semibold text-primary">
                                <span class="material-symbols-outlined text-[10px]">schedule</span>
                                <span>${c.start_time || ''}${c.end_time ? ' - ' + c.end_time : ''}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>${c.instructor || 'Faculty'}</span>
                                <span>${c.room || 'TBA'}</span>
                            </div>
                        </div>
                    `;
                    targetSlot.appendChild(card);
                }
            });
        }

        function renderTba(classesToRender) {
            const tbaContainer = document.getElementById('tba-subjects-container');
            const tbaGrid = document.getElementById('tba-grid');

            const tbaList = classesToRender.filter(c => (c.schedule_day && c.schedule_day.toUpperCase() === 'TBA') || (c.start_time && c.start_time.toUpperCase() === 'TBA'));

            if (tbaList.length === 0) {
                tbaContainer.classList.add('hidden');
                return;
            }

            tbaContainer.classList.remove('hidden');
            tbaGrid.innerHTML = tbaList.map(c => `
                <div class="bg-surface-container-low border border-outline-variant/60 rounded-xl p-4 flex flex-col justify-between">
                    <div class="flex justify-between items-start mb-2">
                        <span class="px-2 py-0.5 bg-primary/10 text-primary font-mono text-xs font-bold rounded">${c.code}</span>
                        <span class="text-[10px] font-mono font-bold bg-surface px-2 py-0.5 rounded border">${c.section || 'AIS 2A'}</span>
                    </div>
                    <h4 class="font-bold text-sm text-primary mb-2">${c.title}</h4>
                    <div class="text-xs text-on-surface-variant space-y-1">
                        <div class="flex items-center gap-1 font-semibold text-secondary"><span class="material-symbols-outlined text-[14px]">info</span> Schedule to be arranged</div>
                        <div class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">person</span> ${c.instructor || 'Faculty Staff'}</div>
                    </div>
                </div>
            `).join('');
        }

        initStudentSchedule();
    </script>
</body>
</html>
