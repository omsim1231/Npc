<?php
require_once 'auth.php';
require_teacher();
$teacher_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Faculty Professor';
$teacher_initial = strtoupper(substr($teacher_name, 0, 1));
$teacher_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$is_admin = isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'registrar');
$selected_class_id = isset($_GET['class_id']) ? $_GET['class_id'] : '';
$csrf_token = getCsrfToken();
$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Faculty Grade Encoding Portal - NPC Connect</title>
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
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <!-- SheetJS for authentic Excel Export/Import -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <style>
        /* Professional Excel Spreadsheet Grid Styles */
        .excel-container { user-select: none; position: relative; }
        .excel-table { border-collapse: separate; border-spacing: 0; font-family: 'Geist', -apple-system, sans-serif; font-size: 12px; table-layout: fixed; background-color: #ffffff; }
        .excel-corner { background: #f3f4f6; border-right: 1px solid #d1d5db; border-bottom: 1px solid #d1d5db; position: sticky; top: 0; left: 0; z-index: 25; width: 44px; min-width: 44px; height: 28px; text-align: center; }
        .excel-col-header { background-color: #f3f4f6; color: #4b5563; font-weight: 700; text-align: center; border-right: 1px solid #d1d5db; border-bottom: 1px solid #d1d5db; padding: 4px 6px; font-size: 11px; font-family: 'JetBrains Mono', monospace; position: sticky; top: 0; z-index: 20; height: 28px; }
        .excel-col-header.col-selected { background-color: #d1fae5 !important; color: #065f46 !important; font-weight: 800; }
        .excel-row-header { background-color: #f3f4f6; color: #4b5563; font-weight: 700; text-align: center; border-right: 1px solid #d1d5db; border-bottom: 1px solid #d1d5db; width: 44px; min-width: 44px; font-size: 11px; font-family: 'JetBrains Mono', monospace; position: sticky; left: 0; z-index: 15; height: 28px; }
        .excel-row-header.row-selected { background-color: #d1fae5 !important; color: #065f46 !important; font-weight: 800; }
        .excel-cell { border-right: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; padding: 0 6px; height: 28px; background-color: #ffffff; font-size: 12px; position: relative; outline: none; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
        .excel-cell.cell-active { outline: 2px solid #107c41 !important; outline-offset: -2px; background-color: #ffffff !important; z-index: 12; }
        .excel-cell.cell-selected { background-color: #ecfdf5 !important; }
        .excel-cell.cell-readonly-bg { background-color: #f9fafb; color: #4b5563; }
        .excel-cell-input { width: 100%; height: 100%; border: none; outline: none; background: transparent; font-family: inherit; font-size: inherit; padding: 0; margin: 0; color: inherit; text-align: inherit; }
        .excel-ribbon-tab { border-bottom: 2px solid transparent; font-size: 12px; font-weight: 600; padding: 6px 14px; cursor: pointer; transition: all 0.15s ease; color: #4b5563; }
        .excel-ribbon-tab:hover { color: #107c41; }
        .excel-ribbon-tab.active { border-bottom-color: #107c41; color: #107c41; font-weight: 700; }
        .sheet-tab { padding: 6px 16px; border: 1px solid #d1d5db; border-bottom: none; background: #f3f4f6; color: #4b5563; font-size: 11px; font-weight: 700; border-top-left-radius: 6px; border-top-right-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; transition: all 0.15s ease; }
        .sheet-tab.active { background: #ffffff; color: #107c41; border-top: 2px solid #107c41; }
        .cell-autofill-handle { position: absolute; right: 0; bottom: 0; width: 6px; height: 6px; background-color: #107c41; cursor: crosshair; display: none; }
        .excel-cell.cell-active .cell-autofill-handle { display: block; }
        @media print {
            aside, #topbar, #excel-ribbon-container, #excel-action-bar, #sheet-tabs-wrapper, .no-print { display: none !important; }
            #app-root, main, #excel-main-wrapper { margin: 0 !important; padding: 0 !important; width: 100% !important; }
            .excel-container { overflow: visible !important; }
            table { width: 100% !important; border-collapse: collapse !important; }
            th, td { border: 1px solid #000 !important; color: #000 !important; }
            #print-official-header { display: block !important; }
        }
        #print-official-header { display: none; }
    </style>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'faculty'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Workspace Area -->
    <div class="flex-1 flex flex-col min-w-0 bg-surface lg:pl-64" id="main-wrapper">
        <!-- TopNavBar Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-8 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
            <div class="flex items-center gap-3">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Connect</span>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-[#107c41] text-[24px]">table_view</span>
                    <h2 class="text-lg font-bold text-primary hidden sm:block">Faculty Grade Encoding Portal</h2>
                </div>
            </div>

            <!-- Subject / Class Switcher Dropdown -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="active-class-select" class="text-xs font-bold text-on-surface-variant font-mono hidden md:block">SELECT CLASS:</label>
                    <select id="active-class-select" onchange="onClassDropdownChange()" class="bg-surface-container-low border border-outline-variant text-xs font-bold rounded-xl px-3 py-2 text-primary focus:ring-2 focus:ring-primary focus:outline-none min-w-[220px]">
                        <option value="">Loading assigned classes...</option>
                    </select>
                </div>
                <div class="flex items-center gap-2 pl-2 border-l border-outline-variant/60">
                    <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        <?= $teacher_initial ?>
                    </div>
                    <span class="text-xs font-semibold text-primary hidden md:inline"><?= htmlspecialchars($teacher_name) ?></span>
                </div>
            </div>
        </header>

        <!-- Main Grade Sheet Canvas -->
        <main class="flex-1 p-4 md:p-6 space-y-4 max-w-[1600px] w-full mx-auto" id="excel-main-wrapper">
            
            <!-- Print Header Banner -->
            <div id="print-official-header" class="mb-4">
                <div class="flex items-center gap-3 border-b-2 border-black pb-2 mb-2">
                    <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg" class="w-10 h-10">
                    <div>
                        <h2 class="font-bold text-sm uppercase">Navotas Polytechnic College</h2>
                        <p class="text-xs">Office of the Registrar — Official Class Grade Sheet</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 text-xs mb-3">
                    <p><strong>Subject:</strong> <span id="print-course-code">DM103</span> - <span id="print-course-title">Business Process Management</span></p>
                    <p><strong>Section:</strong> <span id="print-course-section">AIS 2A</span></p>
                    <p><strong>Instructor:</strong> <span id="print-course-prof"><?= htmlspecialchars($teacher_name) ?></span></p>
                    <p><strong>Term:</strong> 1st Semester, AY 2026-2027</p>
                </div>
            </div>

            <!-- Class Information Banner & Statistics Bento -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-5 shadow-sm space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="px-2.5 py-0.5 bg-primary text-white font-mono text-xs font-bold rounded-lg" id="banner-code">DM103</span>
                            <span class="px-2.5 py-0.5 bg-surface-container text-primary font-mono text-xs font-bold rounded-lg border border-outline-variant" id="banner-section">AIS 2A</span>
                            <span class="px-2.5 py-0.5 bg-emerald-50 text-status-success font-mono text-xs font-bold rounded-lg border border-emerald-200" id="banner-enrolled">0 Enrolled</span>
                            
                            <!-- Lock / Submission Status Badge -->
                            <span id="grade-sheet-status-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Draft Mode (Editable)
                            </span>
                        </div>
                        <h1 class="text-xl font-bold text-primary" id="banner-title">Business Process Management</h1>
                        <p class="text-xs text-on-surface-variant mt-0.5">
                            AY 2026-2027 • 1st Semester • Instructor: <span class="font-semibold text-primary" id="banner-prof"><?= htmlspecialchars($teacher_name) ?></span>
                        </p>
                    </div>

                    <!-- Actions Buttons -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button id="btn-save-draft" onclick="saveDraftGradesToServer()" class="px-3.5 py-2 bg-surface hover:bg-surface-container border border-outline-variant/80 text-xs font-bold rounded-xl text-primary flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px] text-primary">save</span> Save Draft
                        </button>
                        <button onclick="exportGradeSheet('xls')" data-tip="Export Excel (Ctrl+E)" class="px-3.5 py-2 bg-excel-light hover:bg-surface-container border border-excel-border text-xs font-bold rounded-xl text-excel-dark flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">grid_on</span> Excel
                        </button>
                        <button onclick="exportGradeSheet('csv')" data-tip="Export CSV" class="px-3.5 py-2 bg-surface hover:bg-surface-container border border-outline-variant/80 text-xs font-bold rounded-xl text-primary flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">description</span> CSV
                        </button>

                        <button id="btn-submit-grades" onclick="submitGradeSheetForReview()" class="px-4 py-2 bg-[#107c41] hover:bg-[#0c5d31] text-xs font-bold rounded-xl text-white flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">send</span> Submit for Approval
                        </button>

                        <button id="btn-request-change" onclick="openGradeChangeModal()" class="hidden px-4 py-2 bg-secondary text-white hover:bg-secondary/90 text-xs font-bold rounded-xl flex items-center gap-1.5 shadow-sm transition-all cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">edit_note</span> Request Grade Change
                        </button>
                    </div>
                </div>

                <!-- Class Statistical Insights Bar -->
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-3 border-t border-outline-variant/40">
                    <div class="bg-surface p-3 rounded-xl border border-outline-variant/60">
                        <span class="text-[10px] font-mono font-bold text-on-surface-variant uppercase">Class Average</span>
                        <div class="flex items-baseline gap-1.5 mt-0.5">
                            <span class="text-xl font-bold text-primary font-mono" id="stat-class-avg">0.00</span>
                            <span class="text-[10px] text-gray-500 font-mono" id="stat-class-gpa-eq">(1.00 - 5.00)</span>
                        </div>
                    </div>
                    <div class="bg-surface p-3 rounded-xl border border-outline-variant/60">
                        <span class="text-[10px] font-mono font-bold text-status-success uppercase">Passing Rate</span>
                        <div class="text-xl font-bold text-status-success font-mono mt-0.5" id="stat-passing-rate">0%</div>
                    </div>
                    <div class="bg-surface p-3 rounded-xl border border-outline-variant/60">
                        <span class="text-[10px] font-mono font-bold text-status-info uppercase">Highest Grade</span>
                        <div class="text-xl font-bold text-status-info font-mono mt-0.5" id="stat-highest-grade">—</div>
                    </div>
                    <div class="bg-surface p-3 rounded-xl border border-outline-variant/60">
                        <span class="text-[10px] font-mono font-bold text-error uppercase">Lowest Grade</span>
                        <div class="text-xl font-bold text-error font-mono mt-0.5" id="stat-lowest-grade">—</div>
                    </div>
                    <div class="bg-surface p-3 rounded-xl border border-outline-variant/60">
                        <span class="text-[10px] font-mono font-bold text-amber-600 uppercase">Incomplete (INC)</span>
                        <div class="text-xl font-bold text-amber-600 font-mono mt-0.5" id="stat-inc-count">0</div>
                    </div>
                </div>
            </div>

            <!-- Professional Excel Ribbon Toolbar -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm no-print" id="excel-ribbon-container">
                <!-- Ribbon Tabs Navigation -->
                <div class="flex items-center gap-1 px-4 pt-2 bg-surface border-b border-outline-variant/60 overflow-x-auto">
                    <button class="excel-ribbon-tab active" onclick="switchRibbonTab('home', this)">Home</button>
                    <button class="excel-ribbon-tab" onclick="switchRibbonTab('formulas', this)">Formulas & Math</button>
                    <button class="excel-ribbon-tab" onclick="switchRibbonTab('data', this)">Import / Export</button>
                    <button class="excel-ribbon-tab" onclick="switchRibbonTab('weights', this)">Weights & Components</button>
                </div>

                <!-- Ribbon Tab Content Panes -->
                <div class="p-3 bg-surface-container-lowest border-b border-outline-variant flex flex-wrap items-center justify-between gap-3 text-xs">
                    <!-- Home Tab Content -->
                    <div id="ribbon-tab-home" class="flex flex-wrap items-center gap-2">
                        <div class="flex items-center gap-1 pr-2 border-r border-gray-300">
                            <button onclick="undoAction()" title="Undo (Ctrl+Z)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">undo</span>
                            </button>
                            <button onclick="redoAction()" title="Redo (Ctrl+Y)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">redo</span>
                            </button>
                            <button onclick="copyActiveCell()" title="Copy (Ctrl+C)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">content_copy</span>
                            </button>
                            <button onclick="pasteToActiveCell()" title="Paste (Ctrl+V)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700 font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">content_paste</span>
                            </button>
                        </div>

                        <!-- Text Formatting -->
                        <div class="flex items-center gap-1 pr-2 border-r border-gray-300">
                            <button onclick="toggleBold()" title="Bold (Ctrl+B)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-800 font-bold">
                                <span class="material-symbols-outlined text-[18px]">format_bold</span>
                            </button>
                            <button onclick="toggleItalic()" title="Italic (Ctrl+I)" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-800">
                                <span class="material-symbols-outlined text-[18px]">format_italic</span>
                            </button>
                            <button onclick="setCellAlignment('left')" title="Align Left" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700">
                                <span class="material-symbols-outlined text-[18px]">format_align_left</span>
                            </button>
                            <button onclick="setCellAlignment('center')" title="Align Center" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700">
                                <span class="material-symbols-outlined text-[18px]">format_align_center</span>
                            </button>
                            <button onclick="setCellAlignment('right')" title="Align Right" class="p-1.5 hover:bg-gray-100 rounded-lg text-gray-700">
                                <span class="material-symbols-outlined text-[18px]">format_align_right</span>
                            </button>
                        </div>

                        <!-- Grid Structure -->
                        <div class="flex items-center gap-1">
                            <button onclick="promptAddCustomColumn()" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">view_column</span> + Add Column
                            </button>
                            <button onclick="deleteActiveColumn()" class="px-2.5 py-1.5 hover:bg-red-50 text-error rounded-lg font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">delete</span> Delete Column
                            </button>
                            <button onclick="clearActiveCell()" class="px-2.5 py-1.5 hover:bg-gray-100 text-gray-600 rounded-lg font-semibold flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">backspace</span> Clear Cell
                            </button>
                        </div>
                    </div>

                    <!-- Formulas Tab Content -->
                    <div id="ribbon-tab-formulas" class="hidden flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-500 mr-1">NPC GRADING FORMULAS:</span>
                        <button onclick="applyOfficialNPCGradeFormula()" class="px-3 py-1.5 bg-emerald-100 text-emerald-900 hover:bg-emerald-200 rounded-lg font-bold flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px]">calculate</span> Recalculate Final GPAs
                        </button>
                        <button onclick="insertFormula('SUM')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold">AutoSum (Σ)</button>
                        <button onclick="insertFormula('AVERAGE')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold">Average</button>
                        <button onclick="insertFormula('MIN')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold">Min</button>
                        <button onclick="insertFormula('MAX')" class="px-2.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg font-semibold">Max</button>
                    </div>

                    <!-- Data / Import / Export Tab Content -->
                    <div id="ribbon-tab-data" class="hidden flex-wrap items-center gap-2">
                        <label class="px-3 py-1.5 bg-[#107c41] text-white hover:bg-[#0c5d31] rounded-lg font-bold flex items-center gap-1.5 shadow-sm cursor-pointer">
                            <span class="material-symbols-outlined text-[16px]">upload_file</span> Import Excel (.xlsx / .csv)
                            <input type="file" id="excel-file-input" accept=".xlsx, .xls, .csv" onchange="importFromExcelFile(event)" class="hidden">
                        </label>
                        <button onclick="exportToExcelFile()" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg font-bold flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[16px] text-emerald-600">download</span> Export Workbook (.xlsx)
                        </button>
                        <button onclick="exportToCSVFile()" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg font-semibold flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[16px] text-blue-600">csv</span> Export CSV
                        </button>
                        <button onclick="printGradeSheet()" class="px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 rounded-lg font-semibold flex items-center gap-1.5 shadow-sm">
                            <span class="material-symbols-outlined text-[16px] text-gray-600">print</span> Print Official Sheet
                        </button>
                    </div>

                    <!-- Weights & Components Tab Content -->
                    <div id="ribbon-tab-weights" class="hidden flex-wrap items-center gap-3">
                        <span class="text-xs font-bold text-gray-700">Period Weights:</span>
                        <div class="flex items-center gap-1">
                            <span class="font-mono text-gray-500">Prelim:</span>
                            <input type="number" id="weight-prelim" value="30" min="0" max="100" class="w-14 px-2 py-1 bg-white border rounded text-center font-bold text-xs">
                            <span class="text-gray-500">%</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-mono text-gray-500">Midterm:</span>
                            <input type="number" id="weight-midterm" value="30" min="0" max="100" class="w-14 px-2 py-1 bg-white border rounded text-center font-bold text-xs">
                            <span class="text-gray-500">%</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="font-mono text-gray-500">Final:</span>
                            <input type="number" id="weight-final" value="40" min="0" max="100" class="w-14 px-2 py-1 bg-white border rounded text-center font-bold text-xs">
                            <span class="text-gray-500">%</span>
                        </div>
                        <button onclick="applyCustomWeights()" class="px-3 py-1 bg-primary text-white rounded-lg font-bold text-xs hover:bg-primary-container">
                            Apply & Validate 100%
                        </button>
                    </div>
                </div>

                <!-- Formula Bar (Address box & fx input) -->
                <div class="px-4 py-2 bg-surface-subtle border-b border-outline-variant/60 flex items-center gap-2 font-mono text-xs">
                    <div class="flex items-center gap-1 bg-white border border-gray-300 rounded px-2 py-1 font-bold text-primary min-w-[60px] text-center shadow-xs">
                        <input id="active-cell-address" readonly value="A1" class="w-12 bg-transparent text-center outline-none font-bold text-xs">
                    </div>
                    <span class="font-bold text-gray-400 select-none text-sm italic">fx</span>
                    <input id="formula-bar-input" oninput="onFormulaBarInput(this.value)" onkeydown="onFormulaBarKeyDown(event)" placeholder="Enter grade value, formula or text..." class="flex-1 bg-white border border-gray-300 rounded px-3 py-1 text-xs text-gray-900 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs font-mono">
                </div>
            </div>

            <!-- Spreadsheet Grid Container -->
            <div class="bg-white border border-outline-variant rounded-2xl overflow-hidden shadow-sm excel-container">
                <div id="grid-scroll-viewport" class="overflow-auto max-h-[600px] outline-none" tabindex="0" onkeydown="onGridKeyDown(event)">
                    <div class="overflow-x-auto -mx-1 px-1"><table class="excel-table" id="excel-main-grid">
                        <thead id="excel-grid-thead">
                            <!-- Headers dynamically injected -->
                        </thead>
                        <tbody id="excel-grid-tbody">
                            <!-- Cell rows dynamically injected -->
                        </tbody>
                    </table></div>
                </div>

                <!-- Multi-Sheet Tabs Bar & Live Stats Footer -->
                <div id="sheet-tabs-wrapper" class="bg-surface border-t border-outline-variant px-4 py-2 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    <!-- Sheet Tabs -->
                    <div class="flex items-center gap-1 overflow-x-auto w-full sm:w-auto" id="sheet-tabs-container">
                        <div class="sheet-tab active" data-sheet="master" onclick="switchSheetTab('master', this)">
                            <span class="material-symbols-outlined text-[14px]">table_chart</span> Master Summary
                        </div>
                        <div class="sheet-tab" data-sheet="prelim" onclick="switchSheetTab('prelim', this)">
                            Prelim Sheet (30%)
                        </div>
                        <div class="sheet-tab" data-sheet="midterm" onclick="switchSheetTab('midterm', this)">
                            Midterm Sheet (30%)
                        </div>
                        <div class="sheet-tab" data-sheet="final" onclick="switchSheetTab('final', this)">
                            Final Sheet (40%)
                        </div>
                        <button onclick="promptAddNewSheet()" title="Add Custom Sheet" class="px-2 py-1 text-gray-500 hover:text-primary font-bold text-sm rounded hover:bg-gray-200">
                            +
                        </button>
                    </div>

                    <!-- Live Grid Stats -->
                    <div class="flex items-center gap-4 text-gray-500 font-mono text-[11px]">
                        <span id="excel-mode-indicator" class="flex items-center gap-1.5 font-bold text-status-success">
                            <span class="w-2 h-2 rounded-full bg-status-success animate-pulse"></span> READY
                        </span>
                        <span id="stat-count">Count: 0</span>
                        <span id="stat-sum">Sum: 0.00</span>
                        <span id="stat-avg">Average: 0.00</span>
                        <span id="stat-min">Min: 0.00</span>
                        <span id="stat-max">Max: 0.00</span>
                        <span id="stat-pass" class="font-bold text-status-success">Passing: —</span>
                        <span id="stat-fail" class="font-bold text-error">Failing: 0</span>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- 📝 GRADE CHANGE REQUEST MODAL -->
    <div id="grade-change-modal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-200 space-y-4">
            <div class="flex items-center justify-between border-b pb-3">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-secondary text-[22px]">edit_document</span>
                    <h3 class="text-base font-bold text-primary">Official Grade Change Request</h3>
                </div>
                <button onclick="closeGradeChangeModal()" class="text-gray-400 hover:text-gray-700">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <p class="text-xs text-on-surface-variant">
                This grade sheet is officially locked. Any score modifications require review and approval by the Registrar / College Dean.
            </p>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Select Student:</label>
                    <select id="gcr-student-select" onchange="onGcrStudentSelected()" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold focus:ring-1 focus:ring-primary">
                        <!-- Populated dynamically -->
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold text-gray-500 mb-1">Current Approved Grade:</label>
                        <input type="text" id="gcr-current-grade" readonly class="w-full bg-gray-100 border border-gray-300 rounded-xl px-3 py-2 font-mono font-bold text-gray-700">
                    </div>
                    <div>
                        <label class="block font-bold text-primary mb-1">New Proposed Grade:</label>
                        <input type="number" id="gcr-proposed-grade" step="0.25" min="1.00" max="5.00" placeholder="e.g. 1.50" class="w-full bg-white border border-gray-300 rounded-xl px-3 py-2 font-mono font-bold text-primary focus:ring-1 focus:ring-primary">
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-gray-700 mb-1">Detailed Justification / Reason for Correction:</label>
                    <textarea id="gcr-reason" rows="3" placeholder="Provide complete justification (e.g. overlooked project submission, calculation adjustment)..." class="w-full bg-white border border-gray-300 rounded-xl p-3 text-xs text-gray-800 focus:ring-1 focus:ring-primary"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t">
                <button onclick="closeGradeChangeModal()" class="px-4 py-2 border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button onclick="submitGradeChangeRequest()" class="px-4 py-2 bg-secondary text-white rounded-xl text-xs font-bold hover:bg-secondary/90 shadow-sm">
                    Submit Request to Registrar
                </button>
            </div>
        </div>
    </div>

    <!-- Notification Toast -->
    <div id="excel-toast" class="fixed bottom-6 right-6 bg-gray-900 text-white text-xs px-4 py-3 rounded-xl shadow-2xl z-50 flex items-center gap-2.5 transition-all duration-300 opacity-0 pointer-events-none transform translate-y-2">
        <span class="material-symbols-outlined text-[18px] text-emerald-400" id="toast-icon">check_circle</span>
        <span id="toast-message">Ready</span>
    </div>

    <!-- JavaScript Application Logic -->
    <script>
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        const currentTeacherName = <?= json_encode($teacher_name) ?>;
        const currentTeacherEmail = <?= json_encode($teacher_email) ?>;
        const isAdmin = <?= json_encode($is_admin) ?>;
        const csrfToken = <?= json_encode($csrf_token) ?>;

        let activeClasses = [];
        let currentClass = null;
        let isSheetLocked = false;

        // Multi-Sheet Workbook Data Model
        let currentSheet = 'master';
        let workbookSheets = {
            'master': {
                name: 'Master Summary',
                columns: [
                    { id: 'student_number', name: 'STUDENT #', width: 130, align: 'left', isKey: true, isReadOnly: true },
                    { id: 'student_name', name: 'STUDENT FULL NAME', width: 260, align: 'left', isReadOnly: true },
                    { id: 'prelim', name: 'PRELIM (30%)', width: 110, align: 'center', isGrade: true },
                    { id: 'midterm', name: 'MIDTERM (30%)', width: 110, align: 'center', isGrade: true },
                    { id: 'final', name: 'FINAL (40%)', width: 110, align: 'center', isGrade: true },
                    { id: 'gpa', name: 'FINAL GPA', width: 110, align: 'center', isComputed: true, isReadOnly: true },
                    { id: 'remark', name: 'REMARK', width: 130, align: 'center', isComputed: true, isReadOnly: true }
                ],
                data: []
            },
            'prelim': {
                name: 'Prelim Sheet',
                columns: [
                    { id: 'student_number', name: 'STUDENT #', width: 130, align: 'left', isKey: true, isReadOnly: true },
                    { id: 'student_name', name: 'STUDENT FULL NAME', width: 260, align: 'left', isReadOnly: true },
                    { id: 'p_quiz', name: 'QUIZZES (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'p_act', name: 'ACTIVITIES (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'p_att', name: 'ATTENDANCE (10%)', width: 120, align: 'center', isGrade: true },
                    { id: 'p_exam', name: 'PRELIM EXAM (50%)', width: 140, align: 'center', isGrade: true }
                ],
                data: []
            },
            'midterm': {
                name: 'Midterm Sheet',
                columns: [
                    { id: 'student_number', name: 'STUDENT #', width: 130, align: 'left', isKey: true, isReadOnly: true },
                    { id: 'student_name', name: 'STUDENT FULL NAME', width: 260, align: 'left', isReadOnly: true },
                    { id: 'm_quiz', name: 'QUIZZES (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'm_act', name: 'ACTIVITIES (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'm_att', name: 'ATTENDANCE (10%)', width: 120, align: 'center', isGrade: true },
                    { id: 'm_exam', name: 'MIDTERM EXAM (50%)', width: 140, align: 'center', isGrade: true }
                ],
                data: []
            },
            'final': {
                name: 'Final Sheet',
                columns: [
                    { id: 'student_number', name: 'STUDENT #', width: 130, align: 'left', isKey: true, isReadOnly: true },
                    { id: 'student_name', name: 'STUDENT FULL NAME', width: 260, align: 'left', isReadOnly: true },
                    { id: 'f_quiz', name: 'QUIZZES (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'f_proj', name: 'PROJECT (20%)', width: 120, align: 'center', isGrade: true },
                    { id: 'f_att', name: 'ATTENDANCE (10%)', width: 120, align: 'center', isGrade: true },
                    { id: 'f_exam', name: 'FINAL EXAM (50%)', width: 140, align: 'center', isGrade: true }
                ],
                data: []
            }
        };

        let activeRow = 0;
        let activeCol = 2;
        let isEditingCell = false;
        let undoStack = [];
        let redoStack = [];

        // 🚀 Initialize Application
        async function initExcelGradeSheet() {
            // 1. Fetch classes assigned to teacher
            const { data: classes } = await supabaseClient.from('classes').select('*').order('code', { ascending: true });
            const myEmail = (currentTeacherEmail || '').toLowerCase().trim();
            const myName = (currentTeacherName || '').toLowerCase().trim();

            if (isAdmin) {
                activeClasses = classes || [];
            } else {
                activeClasses = (classes || []).filter(c => {
                    const cEmail = (c.instructor_email || c.created_by_email || '').toLowerCase().trim();
                    const cInst = (c.instructor || '').toLowerCase().trim();
                    return (cEmail && cEmail === myEmail) || (cInst && myName && cInst.includes(myName));
                });
                if (activeClasses.length === 0 && classes && classes.length > 0) {
                    activeClasses = classes; // fallback
                }
            }

            const select = document.getElementById('active-class-select');
            select.innerHTML = '<option value="">Select a class gradebook...</option>' + 
                activeClasses.map(c => `<option value="${c.id}">${c.code} - ${c.title} (${c.section || 'AIS 2A'})</option>`).join('');

            const urlParams = new URLSearchParams(window.location.search);
            const initialClassId = urlParams.get('class_id');

            if (initialClassId && activeClasses.some(c => c.id === initialClassId)) {
                select.value = initialClassId;
                await loadClassGradebook(initialClassId);
            } else if (activeClasses.length > 0) {
                select.value = activeClasses[0].id;
                await loadClassGradebook(activeClasses[0].id);
            }
        }

        async function onClassDropdownChange() {
            const classId = document.getElementById('active-class-select').value;
            if (classId) {
                await loadClassGradebook(classId);
            }
        }

        // 📂 Load Class Gradebook via Server API
        async function loadClassGradebook(classId) {
            currentClass = activeClasses.find(c => c.id === classId);
            if (!currentClass) return;

            showToast('Loading class gradebook data...', 'info');

            // Update Header Information
            document.getElementById('banner-code').innerText = currentClass.code;
            document.getElementById('banner-section').innerText = currentClass.section || 'AIS 2A';
            document.getElementById('banner-title').innerText = currentClass.title;
            document.getElementById('banner-prof').innerText = currentClass.instructor || currentTeacherName;

            document.getElementById('print-course-code').innerText = currentClass.code;
            document.getElementById('print-course-title').innerText = currentClass.title;
            document.getElementById('print-course-section').innerText = currentClass.section || 'AIS 2A';

            try {
                const res = await fetch(`api_gradebook.php?action=get_class_grades&class_id=${classId}`);
                const resData = await res.json();

                if (!res.ok || !resData.success) {
                    throw new Error(resData.message || 'Failed to fetch class grades');
                }

                isSheetLocked = resData.is_locked;
                updateLockStatusBadge(resData.submission);

                // Build Student Data Rows
                const students = resData.students || [];
                document.getElementById('banner-enrolled').innerText = `${students.length} Enrolled`;

                // Map grades to master sheet
                const gradesMap = {};
                (resData.grades || []).forEach(g => {
                    gradesMap[g.student_number] = g;
                });

                const masterRows = students.map(s => {
                    const sNum = s.student_number || s.id;
                    const saved = gradesMap[sNum] || {};
                    return [
                        sNum,
                        s.full_name || 'Student Name',
                        saved.prelim > 0 ? saved.prelim : '',
                        saved.midterm > 0 ? saved.midterm : '',
                        saved.final > 0 ? saved.final : '',
                        saved.equivalent_grade > 0 ? saved.equivalent_grade.toFixed(2) : '—',
                        saved.remarks || 'Ongoing'
                    ];
                });

                workbookSheets['master'].data = masterRows;

                // Also populate prelim, midterm, final sheets with student names
                ['prelim', 'midterm', 'final'].forEach(sk => {
                    workbookSheets[sk].data = students.map(s => [
                        s.student_number || s.id,
                        s.full_name || 'Student Name',
                        '', '', '', ''
                    ]);
                });

                computeAllFormulas();
                renderSpreadsheetGrid();
                selectCell(0, 2);
                updateLiveStats();
                showToast(`Loaded ${students.length} students for ${currentClass.code}!`, 'success');

            } catch (err) {
                console.error('Error loading gradebook:', err);
                showToast('Error: ' + err.message, 'error');
            }
        }

        function updateLockStatusBadge(submission) {
            const badge = document.getElementById('grade-sheet-status-badge');
            const saveBtn = document.getElementById('btn-save-draft');
            const submitBtn = document.getElementById('btn-submit-grades');
            const reqBtn = document.getElementById('btn-request-change');

            const status = submission ? (submission.status || 'Draft') : 'Draft';

            if (status === 'Approved' || status === 'Published') {
                badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-status-success';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-status-success"></span> 🔒 Approved & Locked';
                saveBtn.classList.add('hidden');
                submitBtn.classList.add('hidden');
                reqBtn.classList.remove('hidden');
            } else if (status === 'Submitted') {
                badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> ⏳ Submitted (Under Review)';
                saveBtn.classList.add('hidden');
                submitBtn.classList.add('hidden');
                reqBtn.classList.add('hidden');
            } else {
                badge.className = 'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800';
                badge.innerHTML = '<span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> ✏️ Draft Mode (Editable)';
                saveBtn.classList.remove('hidden');
                submitBtn.classList.remove('hidden');
                reqBtn.classList.add('hidden');
            }
        }

        // 🎨 Render Spreadsheet Grid DOM
        function renderSpreadsheetGrid() {
            const sheet = workbookSheets[currentSheet];
            const thead = document.getElementById('excel-grid-thead');
            const tbody = document.getElementById('excel-grid-tbody');

            // Render Header Row
            let headerHtml = '<tr><th class="excel-corner">#</th>';
            sheet.columns.forEach((col, cIdx) => {
                const colLetter = getColumnLetter(cIdx);
                headerHtml += `
                    <th class="excel-col-header col-${cIdx}" style="width: ${col.width}px; min-width: ${col.width}px;" onclick="selectCell(0, ${cIdx})">
                        <div class="text-[10px] text-gray-400 font-mono">${colLetter}</div>
                        <div>${col.name}</div>
                    </th>
                `;
            });
            headerHtml += '</tr>';
            thead.innerHTML = headerHtml;

            // Render Body Rows
            if (sheet.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="${sheet.columns.length + 1}" class="p-12 text-center text-gray-400 font-mono">No student records enrolled in this class.</td></tr>`;
                return;
            }

            let bodyHtml = '';
            sheet.data.forEach((row, rIdx) => {
                bodyHtml += `<tr><td class="excel-row-header row-${rIdx}">${rIdx + 1}</td>`;
                sheet.columns.forEach((col, cIdx) => {
                    const val = row[cIdx] !== undefined ? row[cIdx] : '';
                    const isReadOnly = col.isReadOnly || isSheetLocked;
                    const bgClass = isReadOnly ? 'cell-readonly-bg' : '';
                    const alignClass = col.align === 'center' ? 'text-center' : (col.align === 'right' ? 'text-right' : 'text-left');
                    const isBold = col.bold ? 'font-bold' : '';

                    bodyHtml += `
                        <td class="excel-cell cell-r${rIdx}-c${cIdx} ${bgClass} ${alignClass} ${isBold}" 
                            onclick="selectCell(${rIdx}, ${cIdx})" 
                            ondblclick="onCellDoubleClick(${rIdx}, ${cIdx})">
                            <span class="cell-content">${escapeHtml(val)}</span>
                            <div class="cell-autofill-handle"></div>
                        </td>
                    `;
                });
                bodyHtml += '</tr>';
            });
            tbody.innerHTML = bodyHtml;

            highlightActiveCell();
        }

        function getColumnLetter(idx) {
            let letter = '';
            while (idx >= 0) {
                letter = String.fromCharCode((idx % 26) + 65) + letter;
                idx = Math.floor(idx / 26) - 1;
            }
            return letter;
        }

        function selectCell(row, col) {
            const sheet = workbookSheets[currentSheet];
            if (row < 0 || row >= sheet.data.length || col < 0 || col >= sheet.columns.length) return;

            document.querySelectorAll('.cell-active').forEach(el => el.classList.remove('cell-active'));
            document.querySelectorAll('.col-selected').forEach(el => el.classList.remove('col-selected'));
            document.querySelectorAll('.row-selected').forEach(el => el.classList.remove('row-selected'));

            activeRow = row;
            activeCol = col;

            highlightActiveCell();
            updateFormulaBar();
            updateLiveStats();
        }

        function highlightActiveCell() {
            const cellElem = document.querySelector(`.cell-r${activeRow}-c${activeCol}`);
            if (cellElem) {
                cellElem.classList.add('cell-active');
                cellElem.scrollIntoView({ block: 'nearest', inline: 'nearest' });
            }

            const colHeader = document.querySelector(`.excel-col-header.col-${activeCol}`);
            if (colHeader) colHeader.classList.add('col-selected');

            const rowHeader = document.querySelector(`.excel-row-header.row-${activeRow}`);
            if (rowHeader) rowHeader.classList.add('row-selected');

            const address = `${getColumnLetter(activeCol)}${activeRow + 1}`;
            document.getElementById('active-cell-address').value = address;
        }

        function updateFormulaBar() {
            const sheet = workbookSheets[currentSheet];
            const val = sheet.data[activeRow] ? (sheet.data[activeRow][activeCol] || '') : '';
            document.getElementById('formula-bar-input').value = val;
        }

        function onCellDoubleClick(row, col) {
            const sheet = workbookSheets[currentSheet];
            const colDef = sheet.columns[col];
            if ((colDef && colDef.isReadOnly) || isSheetLocked) return;

            isEditingCell = true;
            document.getElementById('excel-mode-indicator').innerHTML = '<span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span> EDIT';
            document.getElementById('excel-mode-indicator').className = 'flex items-center gap-1.5 font-bold text-amber-600';

            const cellElem = document.querySelector(`.cell-r${row}-c${col}`);
            if (!cellElem) return;

            const currentVal = sheet.data[row][col] || '';
            cellElem.innerHTML = `
                <input type="text" class="excel-cell-input" value="${escapeHtml(currentVal)}" onkeydown="onInlineInputKeyDown(event)" onblur="finishCellEdit(true)">
            `;

            const input = cellElem.querySelector('input');
            input.focus();
            input.select();
        }

        function finishCellEdit(commit = true) {
            if (!isEditingCell) return;
            const sheet = workbookSheets[currentSheet];
            const cellElem = document.querySelector(`.cell-r${activeRow}-c${activeCol}`);
            
            if (cellElem) {
                const input = cellElem.querySelector('input');
                if (input && commit) {
                    let newVal = input.value.trim();

                    /* ── Grade validation for score columns (Prelim/Midterm/Final) ── */
                    if ([2, 3, 4].includes(activeCol) && newVal !== '') {
                        const num = Number(newVal);
                        const isSpecialMark = /^(INC|NG|DRP|DROP)$/i.test(newVal);
                        if (!isSpecialMark && (isNaN(num) || num < 0 || num > 100)) {
                            showToast('Invalid grade "' + newVal + '" — enter 0–100, or INC / NG / DRP.', 'error', 4200);
                            cellElem.classList.add('npc-cell-invalid');
                            setTimeout(() => cellElem.classList.remove('npc-cell-invalid'), 700);
                            cellElem.innerHTML = `<span class="cell-content">${escapeHtml(sheet.data[activeRow][activeCol] || '')}</span><div class="cell-autofill-handle"></div>`;
                            isEditingCell = false;
                            highlightActiveCell();
                            return;
                        }
                        if (!isSpecialMark) newVal = String(num);
                    }

                    const prev = sheet.data[activeRow][activeCol];
                    setCellValue(activeRow, activeCol, newVal);
                    if (String(prev) !== String(newVal)) {
                        markGradeSheetDirty();
                        cellElem.classList.add('npc-cell-saved');
                        setTimeout(() => cellElem.classList.remove('npc-cell-saved'), 650);
                    }
                } else {
                    cellElem.innerHTML = `<span class="cell-content">${escapeHtml(sheet.data[activeRow][activeCol] || '')}</span><div class="cell-autofill-handle"></div>`;
                }
            }

            isEditingCell = false;
            document.getElementById('excel-mode-indicator').innerHTML = '<span class="w-2 h-2 rounded-full bg-status-success animate-pulse"></span> READY';
            document.getElementById('excel-mode-indicator').className = 'flex items-center gap-1.5 font-bold text-status-success';
            
            highlightActiveCell();
        }

        function setCellValue(row, col, val) {
            const sheet = workbookSheets[currentSheet];
            if (!sheet.data[row]) return;

            pushUndoState();
            sheet.data[row][col] = val;
            computeAllFormulas();

            const cellElem = document.querySelector(`.cell-r${row}-c${col}`);
            if (cellElem) {
                cellElem.innerHTML = `<span class="cell-content">${escapeHtml(sheet.data[row][col] || '')}</span><div class="cell-autofill-handle"></div>`;
            }

            updateFormulaBar();
            updateLiveStats();
        }

        function onFormulaBarInput(val) {
            const sheet = workbookSheets[currentSheet];
            if (!sheet.data[activeRow]) return;
            sheet.data[activeRow][activeCol] = val;
            computeAllFormulas();

            const cellElem = document.querySelector(`.cell-r${activeRow}-c${activeCol}`);
            if (cellElem && !isEditingCell) {
                cellElem.innerHTML = `<span class="cell-content">${escapeHtml(val)}</span><div class="cell-autofill-handle"></div>`;
            }
            updateLiveStats();
        }

        function onFormulaBarKeyDown(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                selectCell(activeRow + 1, activeCol);
                document.getElementById('grid-scroll-viewport').focus();
            }
        }

        // 🧮 Comprehensive NPC Grade Calculations & Statistics
        function computeAllFormulas() {
            const sheet = workbookSheets['master'];
            if (!sheet || !sheet.data) return;

            const pWeight = (parseFloat(document.getElementById('weight-prelim').value) || 30) / 100;
            const mWeight = (parseFloat(document.getElementById('weight-midterm').value) || 30) / 100;
            const fWeight = (parseFloat(document.getElementById('weight-final').value) || 40) / 100;

            let allGpas = [];
            let passedCount = 0;
            let incCount = 0;

            sheet.data.forEach((row, rIdx) => {
                const prelimVal = parseFloat(row[2]) || 0;
                const midtermVal = parseFloat(row[3]) || 0;
                const finalVal = parseFloat(row[4]) || 0;

                let compGpa = '—';
                let remark = 'Ongoing';

                if (finalVal > 0 || midtermVal > 0 || prelimVal > 0) {
                    let gpaCalc = 0;
                    if (prelimVal > 0 && midtermVal > 0 && finalVal > 0) {
                        gpaCalc = (prelimVal * pWeight) + (midtermVal * mWeight) + (finalVal * fWeight);
                    } else if (finalVal > 0) {
                        gpaCalc = finalVal;
                    } else if (midtermVal > 0) {
                        gpaCalc = (prelimVal > 0) ? (prelimVal * 0.5 + midtermVal * 0.5) : midtermVal;
                    } else {
                        gpaCalc = prelimVal;
                    }

                    // NPC Transmutation Scale: 97-100=1.00, 94-96=1.25, 91-93=1.50, 88-90=1.75, 85-87=2.00, 82-84=2.25, 79-81=2.50, 76-78=2.75, 75=3.00, <75=5.00
                    let npcEquivalent = 5.00;
                    if (gpaCalc >= 97.0) npcEquivalent = 1.00;
                    else if (gpaCalc >= 94.0) npcEquivalent = 1.25;
                    else if (gpaCalc >= 91.0) npcEquivalent = 1.50;
                    else if (gpaCalc >= 88.0) npcEquivalent = 1.75;
                    else if (gpaCalc >= 85.0) npcEquivalent = 2.00;
                    else if (gpaCalc >= 82.0) npcEquivalent = 2.25;
                    else if (gpaCalc >= 79.0) npcEquivalent = 2.50;
                    else if (gpaCalc >= 76.0) npcEquivalent = 2.75;
                    else if (gpaCalc >= 75.0) npcEquivalent = 3.00;
                    else if (gpaCalc <= 0) npcEquivalent = 0.00;

                    if (npcEquivalent > 0 && npcEquivalent <= 3.00) {
                        remark = 'PASSED';
                        passedCount++;
                        allGpas.push(npcEquivalent);
                    } else if (npcEquivalent === 5.00) {
                        remark = 'FAILED';
                        allGpas.push(npcEquivalent);
                    } else {
                        remark = 'INC';
                        incCount++;
                    }

                    compGpa = npcEquivalent > 0 ? npcEquivalent.toFixed(2) : '—';
                }

                row[5] = compGpa;
                row[6] = remark;

                if (currentSheet === 'master') {
                    const gpaCell = document.querySelector(`.cell-r${rIdx}-c5 .cell-content`);
                    if (gpaCell) gpaCell.innerText = compGpa;

                    const remarkCell = document.querySelector(`.cell-r${rIdx}-c6 .cell-content`);
                    if (remarkCell) {
                        remarkCell.innerText = remark;
                        const cellTd = remarkCell.closest('td');
                        if (cellTd) {
                            if (remark === 'PASSED') cellTd.className = 'excel-cell cell-r' + rIdx + '-c6 font-mono font-bold text-status-success text-center';
                            else if (remark === 'FAILED') cellTd.className = 'excel-cell cell-r' + rIdx + '-c6 font-mono font-bold text-error text-center';
                            else cellTd.className = 'excel-cell cell-r' + rIdx + '-c6 font-mono font-bold text-gray-500 text-center';
                        }
                    }
                }
            });

            // Update Statistics Dashboard
            const totalValid = allGpas.length;
            if (totalValid > 0) {
                const avgGpa = (allGpas.reduce((a, b) => a + b, 0) / totalValid).toFixed(2);
                const passingRate = ((passedCount / totalValid) * 100).toFixed(1);
                const minGpa = Math.min(...allGpas).toFixed(2);
                const maxGpa = Math.max(...allGpas).toFixed(2);

                document.getElementById('stat-class-avg').innerText = avgGpa;
                document.getElementById('stat-passing-rate').innerText = `${passingRate}%`;
                document.getElementById('stat-highest-grade').innerText = minGpa; // 1.00 is highest
                document.getElementById('stat-lowest-grade').innerText = maxGpa;
                document.getElementById('stat-inc-count').innerText = incCount;
            }
        }

        // 💾 Save Draft Grades to Server
        async function saveDraftGradesToServer() {
            if (!currentClass) return;

            const sheet = workbookSheets['master'];
            const gradesPayload = sheet.data.map(row => ({
                student_number: row[0],
                student_name: row[1],
                prelim: parseFloat(row[2]) || 0,
                midterm: parseFloat(row[3]) || 0,
                final: parseFloat(row[4]) || 0,
                remarks: row[6] || 'Ongoing'
            }));

            showToast('Saving draft to server...', 'info');

            try {
                const res = await fetch('api_gradebook.php?action=save_draft', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        class_id: currentClass.id,
                        class_code: currentClass.code,
                        section: currentClass.section || 'AIS 2A',
                        weight_prelim: document.getElementById('weight-prelim').value,
                        weight_midterm: document.getElementById('weight-midterm').value,
                        weight_final: document.getElementById('weight-final').value,
                        grades: gradesPayload,
                        csrf_token: csrfToken
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    showToast('✅ Draft saved successfully to database!', 'success');
                } else {
                    throw new Error(data.message || 'Failed to save draft');
                }
            } catch (err) {
                alert('Save Error: ' + err.message);
                showToast('Error saving draft.', 'error');
            }
        }

        // 🚀 Submit Grade Sheet for Approval
        async function submitGradeSheetForReview() {
            if (!currentClass) return;

            if (!confirm(`Are you sure you want to submit the official grade sheet for ${currentClass.code} (${currentClass.section || 'AIS 2A'}) to the Registrar for review?\n\nOnce submitted, the sheet will be locked from direct editing until approved.`)) {
                return;
            }

            // Save draft first
            await saveDraftGradesToServer();

            showToast('Submitting to Registrar for approval...', 'info');

            try {
                const res = await fetch('api_gradebook.php?action=submit_grades', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        class_id: currentClass.id,
                        class_code: currentClass.code,
                        section: currentClass.section || 'AIS 2A',
                        csrf_token: csrfToken
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert('✅ Grade sheet submitted successfully to the Registrar!\n\nStatus is now SUBMITTED. You will be notified when grades are approved and published.');
                    await loadClassGradebook(currentClass.id);
                } else {
                    throw new Error(data.message || 'Submission failed');
                }
            } catch (err) {
                alert('Submission Error: ' + err.message);
            }
        }

        // 📝 Grade Change Request Workflow Modal
        function openGradeChangeModal() {
            const sheet = workbookSheets['master'];
            const select = document.getElementById('gcr-student-select');

            select.innerHTML = sheet.data.map((r, idx) => `
                <option value="${idx}">${r[0]} - ${r[1]} (Current GPA: ${r[5]})</option>
            `).join('');

            onGcrStudentSelected();
            document.getElementById('grade-change-modal').classList.remove('hidden');
        }

        function closeGradeChangeModal() {
            document.getElementById('grade-change-modal').classList.add('hidden');
        }

        function onGcrStudentSelected() {
            const sheet = workbookSheets['master'];
            const idx = parseInt(document.getElementById('gcr-student-select').value) || 0;
            const studentRow = sheet.data[idx];
            if (studentRow) {
                document.getElementById('gcr-current-grade').value = studentRow[5] || '3.00';
            }
        }

        async function submitGradeChangeRequest() {
            const sheet = workbookSheets['master'];
            const idx = parseInt(document.getElementById('gcr-student-select').value) || 0;
            const studentRow = sheet.data[idx];
            const proposed = parseFloat(document.getElementById('gcr-proposed-grade').value);
            const reason = document.getElementById('gcr-reason').value.trim();

            if (!studentRow || isNaN(proposed) || !reason) {
                alert('Please enter a valid proposed grade and complete reason.');
                return;
            }

            try {
                const res = await fetch('api_gradebook.php?action=request_grade_change', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': csrfToken
                    },
                    body: JSON.stringify({
                        class_id: currentClass.id,
                        class_code: currentClass.code,
                        student_number: studentRow[0],
                        student_name: studentRow[1],
                        original_grade: parseFloat(studentRow[5]) || 3.00,
                        proposed_grade: proposed,
                        reason: reason,
                        csrf_token: csrfToken
                    })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    alert('✅ Grade change request submitted to the Registrar for review!');
                    closeGradeChangeModal();
                } else {
                    throw new Error(data.message || 'Request failed');
                }
            } catch (err) {
                alert('Error: ' + err.message);
            }
        }

        // 📤 Real Excel Export (.xlsx) with SheetJS
        function exportToExcelFile() {
            if (!currentClass) return;
            const wb = XLSX.utils.book_new();

            Object.keys(workbookSheets).forEach(sheetKey => {
                const sObj = workbookSheets[sheetKey];
                const headerRow = sObj.columns.map(c => c.name);
                const rowsData = [
                    ['NAVOTAS POLYTECHNIC COLLEGE - OFFICIAL GRADE SHEET'],
                    [`Subject: ${currentClass.code} - ${currentClass.title}`, `Section: ${currentClass.section || 'AIS 2A'}`],
                    [`Instructor: ${currentClass.instructor || currentTeacherName}`, `Semester: 1st Semester, 2026-2027`],
                    [],
                    headerRow
                ];

                sObj.data.forEach(row => rowsData.push(row));
                const ws = XLSX.utils.aoa_to_sheet(rowsData);
                ws['!cols'] = sObj.columns.map(c => ({ wch: Math.round((c.width || 120) / 7.5) }));
                XLSX.utils.book_append_sheet(wb, ws, sObj.name.substring(0, 31));
            });

            const fileName = `NPC_Grades_${currentClass.code}_${currentClass.section || 'AIS2A'}.xlsx`;
            XLSX.writeFile(wb, fileName);
            showToast(`Exported ${fileName} successfully!`, 'success');
        }

        // 📤 CSV Export
        function exportToCSVFile() {
            const sheet = workbookSheets[currentSheet];
            const headerRow = sheet.columns.map(c => `"${c.name}"`).join(',');
            const rows = sheet.data.map(r => r.map(cell => `"${cell || ''}"`).join(','));
            const csvContent = "data:text/csv;charset=utf-8," + [headerRow, ...rows].join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `NPC_Grades_${currentClass.code}_${sheet.name}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showToast('Exported CSV successfully!', 'success');
        }

        // 📥 Real Excel Import (.xlsx, .csv) with SheetJS
        function importFromExcelFile(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(evt) {
                try {
                    const data = new Uint8Array(evt.target.result);
                    const workbook = XLSX.read(data, { type: 'array' });
                    const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                    const json = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                    if (!json || json.length === 0) {
                        alert('Uploaded Excel sheet is empty.');
                        return;
                    }

                    let matchedCount = 0;
                    const sheet = workbookSheets['master'];

                    json.forEach(rowArr => {
                        if (!rowArr || rowArr.length === 0) return;
                        const sNum = String(rowArr[0] || rowArr[1] || '').trim();
                        const targetRow = sheet.data.find(r => String(r[0]).trim() === sNum);

                        if (targetRow) {
                            if (rowArr[2] !== undefined && rowArr[2] !== '') targetRow[2] = rowArr[2];
                            if (rowArr[3] !== undefined && rowArr[3] !== '') targetRow[3] = rowArr[3];
                            if (rowArr[4] !== undefined && rowArr[4] !== '') targetRow[4] = rowArr[4];
                            matchedCount++;
                        }
                    });

                    computeAllFormulas();
                    renderSpreadsheetGrid();
                    showToast(`Excel imported! Matched ${matchedCount} students.`, 'success');

                } catch (err) {
                    alert('Error parsing Excel: ' + err.message);
                }
            };
            reader.readAsArrayBuffer(file);
            e.target.value = '';
        }

        function printGradeSheet() { window.print(); }

        // Keyboard & Cell Event Handlers
        function onGridKeyDown(e) {
            if (isEditingCell) return;
            if (e.key === 'ArrowDown') { e.preventDefault(); selectCell(activeRow + 1, activeCol); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); selectCell(activeRow - 1, activeCol); }
            else if (e.key === 'ArrowRight') { e.preventDefault(); selectCell(activeRow, activeCol + 1); }
            else if (e.key === 'ArrowLeft') { e.preventDefault(); selectCell(activeRow, activeCol - 1); }
            else if (e.key === 'Tab') { e.preventDefault(); selectCell(activeRow, e.shiftKey ? activeCol - 1 : activeCol + 1); }
            else if (e.key === 'Enter') { e.preventDefault(); selectCell(e.shiftKey ? activeRow - 1 : activeRow + 1, activeCol); }
            else if (e.key === 'F2') { e.preventDefault(); onCellDoubleClick(activeRow, activeCol); }
            else if (e.key === 'Delete') { e.preventDefault(); setCellValue(activeRow, activeCol, ''); }
            else if (e.ctrlKey && (e.key === 'z' || e.key === 'Z')) { e.preventDefault(); undoAction(); }
            else if (e.ctrlKey && (e.key === 'y' || e.key === 'Y')) { e.preventDefault(); redoAction(); }
            else if (e.key.length === 1 && !e.ctrlKey && !e.altKey && !e.metaKey) { onCellDoubleClick(activeRow, activeCol); }
        }

        function onInlineInputKeyDown(e) {
            if (e.key === 'Enter') { e.preventDefault(); finishCellEdit(true); selectCell(activeRow + 1, activeCol); document.getElementById('grid-scroll-viewport').focus(); }
            else if (e.key === 'Tab') { e.preventDefault(); finishCellEdit(true); selectCell(activeRow, e.shiftKey ? activeCol - 1 : activeCol + 1); document.getElementById('grid-scroll-viewport').focus(); }
            else if (e.key === 'Escape') { e.preventDefault(); finishCellEdit(false); document.getElementById('grid-scroll-viewport').focus(); }
        }

        function updateLiveStats() {
            const sheet = workbookSheets[currentSheet];
            const colValues = [];
            sheet.data.forEach(row => {
                const val = parseFloat(row[activeCol]);
                if (!isNaN(val)) colValues.push(val);
            });

            const put = (id, txt) => { const el = document.getElementById(id); if (el) el.innerText = txt; };

            if (colValues.length > 0) {
                const sum = colValues.reduce((a, b) => a + b, 0);
                const avg = sum / colValues.length;
                /* NPC passing line is 75 for score-based sheets */
                const passing = colValues.filter(v => v >= 75).length;
                const failing = colValues.length - passing;
                put('stat-sum', `Sum: ${sum.toFixed(2)}`);
                put('stat-count', `Count: ${colValues.length}`);
                put('stat-min', `Min: ${Math.min(...colValues).toFixed(2)}`);
                put('stat-max', `Max: ${Math.max(...colValues).toFixed(2)}`);

                const avgEl = document.getElementById('stat-avg');
                if (avgEl) {
                    avgEl.innerText = `Average: ${avg.toFixed(2)}`;
                    avgEl.classList.remove('text-status-success','text-status-warning','text-error');
                    avgEl.classList.add(avg >= 90 ? 'text-status-success' : (avg >= 75 ? 'text-status-warning' : 'text-error'));
                }
                const passEl = document.getElementById('stat-pass');
                if (passEl) passEl.innerText = `Passing: ${passing}/${colValues.length}`;
                const failEl = document.getElementById('stat-fail');
                if (failEl) {
                    failEl.innerText = `Failing: ${failing}`;
                    failEl.classList.toggle('animate-pulse', failing > 0);
                }
            } else {
                put('stat-sum', 'Sum: 0.00');
                put('stat-count', 'Count: 0');
                put('stat-min', 'Min: 0.00');
                put('stat-max', 'Max: 0.00');
                const avgEl = document.getElementById('stat-avg');
                if (avgEl) { avgEl.innerText = 'Average: 0.00'; avgEl.classList.remove('text-status-success','text-status-warning','text-error'); }
                const passEl = document.getElementById('stat-pass');
                if (passEl) passEl.innerText = 'Passing: —';
                const failEl = document.getElementById('stat-fail');
                if (failEl) failEl.innerText = 'Failing: 0';
            }
        }

        function switchSheetTab(sheetKey, tabElem) {
            if (!workbookSheets[sheetKey]) return;
            currentSheet = sheetKey;
            document.querySelectorAll('.sheet-tab').forEach(t => t.classList.remove('active'));
            tabElem.classList.add('active');
            renderSpreadsheetGrid();
            selectCell(0, 2);
        }

        function switchRibbonTab(tabName, elem) {
            document.querySelectorAll('.excel-ribbon-tab').forEach(t => t.classList.remove('active'));
            elem.classList.add('active');
            ['home', 'formulas', 'data', 'weights'].forEach(t => {
                const el = document.getElementById(`ribbon-tab-${t}`);
                if (el) el.className = (t === tabName) ? 'flex flex-wrap items-center gap-2' : 'hidden';
            });
        }

        function toggleBold() {
            const sheet = workbookSheets[currentSheet];
            sheet.columns[activeCol].bold = !sheet.columns[activeCol].bold;
            renderSpreadsheetGrid();
        }
        function toggleItalic() {
            const cellElem = document.querySelector(`.cell-r${activeRow}-c${activeCol}`);
            if (cellElem) cellElem.classList.toggle('italic');
        }
        function setCellAlignment(align) {
            const sheet = workbookSheets[currentSheet];
            sheet.columns[activeCol].align = align;
            renderSpreadsheetGrid();
        }
        function clearActiveCell() { setCellValue(activeRow, activeCol, ''); }
        function applyOfficialNPCGradeFormula() { computeAllFormulas(); renderSpreadsheetGrid(); showToast('Recalculated GPAs!', 'success'); }
        function applyCustomWeights() { computeAllFormulas(); renderSpreadsheetGrid(); showToast('Grading weights applied!', 'success'); }

        function promptAddCustomColumn() {
            const title = prompt('Enter new column title (e.g. QUIZ 3, PROJECT):');
            if (!title) return;
            const sheet = workbookSheets[currentSheet];
            sheet.columns.push({ id: 'col_' + Date.now(), name: title.toUpperCase(), width: 120, align: 'center' });
            sheet.data.forEach(row => row.push(''));
            renderSpreadsheetGrid();
            showToast(`Added column ${title}`, 'success');
        }

        function deleteActiveColumn() {
            const sheet = workbookSheets[currentSheet];
            if (sheet.columns.length <= 1) return alert('Cannot delete the last column.');
            if (sheet.columns[activeCol].isKey) return alert('Student ID columns cannot be deleted.');
            if (confirm(`Delete column "${sheet.columns[activeCol].name}"?`)) {
                sheet.columns.splice(activeCol, 1);
                sheet.data.forEach(row => row.splice(activeCol, 1));
                if (activeCol >= sheet.columns.length) activeCol = sheet.columns.length - 1;
                renderSpreadsheetGrid();
            }
        }

        function pushUndoState() {
            const sheet = workbookSheets[currentSheet];
            undoStack.push(JSON.stringify(sheet.data));
            if (undoStack.length > 30) undoStack.shift();
            redoStack = [];
        }
        function undoAction() {
            if (undoStack.length === 0) return;
            const sheet = workbookSheets[currentSheet];
            redoStack.push(JSON.stringify(sheet.data));
            sheet.data = JSON.parse(undoStack.pop());
            computeAllFormulas();
            renderSpreadsheetGrid();
        }
        function redoAction() {
            if (redoStack.length === 0) return;
            const sheet = workbookSheets[currentSheet];
            undoStack.push(JSON.stringify(sheet.data));
            sheet.data = JSON.parse(redoStack.pop());
            computeAllFormulas();
            renderSpreadsheetGrid();
        }

        function showToast(msg, type = 'success') {
            const toast = document.getElementById('excel-toast');
            const toastMsg = document.getElementById('toast-message');
            const toastIcon = document.getElementById('toast-icon');
            toastMsg.innerText = msg;
            toastIcon.innerText = type === 'error' ? 'error' : (type === 'info' ? 'info' : 'check_circle');
            toastIcon.className = type === 'error' ? 'material-symbols-outlined text-[18px] text-red-400' : (type === 'info' ? 'material-symbols-outlined text-[18px] text-blue-400' : 'material-symbols-outlined text-[18px] text-emerald-400');
            toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
            clearTimeout(window.toastTimeout);
            window.toastTimeout = setTimeout(() => toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2'), 3000);
        }

        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
        }

        initExcelGradeSheet();

        /* ═══ Export: Excel (xls) & CSV of the master sheet ═══ */
        function exportGradeSheet(fmt) {
            const sheet = workbookSheets['master'];
            if (!sheet || !sheet.data || !sheet.data.length) { showToast('Nothing to export.', 'error'); return; }
            const headers = (sheet.headers && sheet.headers.length ? sheet.headers : ['Student No.', 'Name', 'Prelim', 'Midterm', 'Final', 'Grade', 'Remarks']);
            const rows = [headers].concat(sheet.data);
            const cls = currentClass ? (currentClass.code + '-' + (currentClass.section || '')) : 'class';

            if (fmt === 'csv') {
                const csv = rows.map(r => r.map(v => '"' + String(v == null ? '' : v).replace(/"/g, '""') + '"').join(',')).join('\r\n');
                downloadBlob(csv, 'npc-grades-' + cls + '.csv', 'text/csv');
            } else {
                /* Excel-compatible HTML table (.xls) — preserves columns */
                let html = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head><meta charset="UTF-8"></head><body>';
                html += '<div class="overflow-x-auto -mx-1 px-1"><table border="1"><thead><tr>' + headers.map(h => '<th style="background:#107c41;color:#fff;">' + esc(h) + '</th>').join('') + '</tr></thead><tbody>';
                sheet.data.forEach(r => {
                    html += '<tr>' + r.map((v, ci) => '<td' + (ci >= 2 && ci <= 4 ? ' style="mso-number-format:\'0.00\'"' : '') + '>' + esc(v) + '</td>').join('') + '</tr>';
                });
                html += '</tbody></table></div></body></html>';
                downloadBlob(html, 'npc-grades-' + cls + '.xls', 'application/vnd.ms-excel');
            }
            showToast('Exported ' + fmt.toUpperCase() + ' ✓', 'success');
        }

        function downloadBlob(content, filename, mime) {
            const blob = new Blob(['\ufeff' + content], { type: mime + ';charset=utf-8' });
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = filename;
            a.click();
            URL.revokeObjectURL(a.href);
        }

        /* Ctrl+E exports Excel */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'e' || e.key === 'E')) {
                e.preventDefault();
                exportGradeSheet('xls');
            }
        });

        /* ═══ Grade-encoding safety layer v3 ═══ */
        let __gradesDirty = false;

        function markGradeSheetDirty() {
            if (__gradesDirty) return;
            __gradesDirty = true;
            const b = document.getElementById('btn-save-draft');
            if (b) {
                if (!b.dataset.origHtml) b.dataset.origHtml = b.innerHTML;
                b.innerHTML = '<span class="material-symbols-outlined text-[16px] text-status-warning">pending</span> Save Draft •';
                b.classList.add('ring-2', 'ring-status-warning/50');
            }
            document.title = '● ' + document.title.replace(/^●\s*/, '');
        }

        async function autoSaveDraft() {
            if (!__gradesDirty || !currentClass) return;
            const btn = document.getElementById('btn-save-draft');
            if (btn && btn.disabled) return; /* a save is already running */
            try { await saveDraftGradesToServer(); markGradeSheetClean(); } catch (e) { /* silent */ }
        }

        function markGradeSheetClean() {
            __gradesDirty = false;
            const b = document.getElementById('btn-save-draft');
            if (b && b.dataset.origHtml) {
                b.innerHTML = b.dataset.origHtml;
                delete b.dataset.origHtml;
                b.classList.remove('ring-2', 'ring-status-warning/50');
            }
            document.title = document.title.replace(/^●\s*/, '');
        }

        /* Ctrl+S saves the draft */
        document.addEventListener('keydown', function (e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'S')) {
                e.preventDefault();
                if (__gradesDirty && currentClass) saveDraftGradesToServer().then(() => { markGradeSheetClean(); showToast('Draft saved ✓', 'success'); });
            }
        });

        /* Warn before leaving with unsaved edits */
        window.addEventListener('beforeunload', function (e) {
            if (!__gradesDirty) return;
            e.preventDefault();
            e.returnValue = '';
        });

        /* Autosave every 45 s when dirty */
        setInterval(autoSaveDraft, 45000);
    </script>
</body>
</html>
