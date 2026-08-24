<?php
require_once 'auth.php';
require_admin();
$admin_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Administrator';
$admin_email = isset($_SESSION['email']) ? (string)$_SESSION['email'] : '';
$admin_initial = strtoupper(substr($admin_name, 0, 1));
$csrf_token = getCsrfToken();
$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>NPC Connect - User & Role Management</title>
    <!-- Theme bootstrap -->
    <script>
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
    <script src="npc.js?v=<?= filemtime(__DIR__ . '/npc.js') ?>"></script>
    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/styles.css') ?>">
    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>

    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Content Area -->
    <main class="flex-1 lg:ml-64 bg-surface min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Admin</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">User & Role Management</h2>
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

        <!-- Canvas -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-6 flex-1">

            <!-- Intro + Add button -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-outline-variant/60 pb-5">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight"><span class="text-shimmer text-primary">Accounts & Access Control</span></h1>
                    <p class="text-sm text-on-surface-variant mt-1">Create admin, faculty, or student accounts using their official NPC Gmail. Roles decide which portal each person can enter.</p>
                </div>
                <button id="btn-add-user" class="ripple press bg-primary text-on-primary px-5 py-3 rounded-xl text-sm font-bold flex items-center gap-2 shadow-md hover:opacity-90 transition-all hover:-translate-y-0.5 cursor-pointer shrink-0 npc-navy-card">
                    <span class="material-symbols-outlined text-[19px]">person_add</span>
                    Add New Account
                </button>
            </div>

            <!-- Role stat chips -->
            <section class="grid grid-cols-2 lg:grid-cols-4 gap-4" id="role-stats">
                <button data-filter-role="all" class="role-stat npc-card press text-left bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-surface-container-high text-primary flex items-center justify-center"><span class="material-symbols-outlined">groups</span></div>
                    <div><p class="text-xl font-extrabold text-primary tabular-nums" id="stat-all">0</p><p class="text-[11px] font-mono uppercase text-on-surface-variant">All accounts</p></div>
                </button>
                <button data-filter-role="student" class="role-stat npc-card press text-left bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-primary-container text-on-primary flex items-center justify-center npc-navy-card"><span class="material-symbols-outlined">school</span></div>
                    <div><p class="text-xl font-extrabold text-primary tabular-nums" id="stat-student">0</p><p class="text-[11px] font-mono uppercase text-on-surface-variant">Students</p></div>
                </button>
                <button data-filter-role="teacher" class="role-stat npc-card press text-left bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-secondary-container text-on-secondary-container flex items-center justify-center"><span class="material-symbols-outlined">cast_for_education</span></div>
                    <div><p class="text-xl font-extrabold text-primary tabular-nums" id="stat-teacher">0</p><p class="text-[11px] font-mono uppercase text-on-surface-variant">Faculty</p></div>
                </button>
                <button data-filter-role="admin" class="role-stat npc-card press text-left bg-surface-container-lowest rounded-2xl border border-outline-variant p-4 shadow-sm flex items-center gap-3 cursor-pointer">
                    <div class="w-10 h-10 rounded-xl bg-error/10 text-error flex items-center justify-center"><span class="material-symbols-outlined">shield_person</span></div>
                    <div><p class="text-xl font-extrabold text-primary tabular-nums" id="stat-admin">0</p><p class="text-[11px] font-mono uppercase text-on-surface-variant">Admins</p></div>
                </button>
            </section>

            <!-- Search bar -->
            <div class="bg-surface-container-lowest p-4 rounded-2xl border border-outline-variant shadow-sm flex items-center gap-3">
                <span class="material-symbols-outlined text-on-surface-variant text-[20px]">search</span>
                <input type="text" id="user-search" placeholder="Search by name, email, or student number…"
                       class="flex-1 bg-transparent border-none outline-none text-sm text-on-surface placeholder:text-on-surface-variant/60 focus:ring-0" />
                <span class="kbd">Ctrl K</span>
            </div>

            <!-- Accounts table -->
            <section class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[760px]">
                        <thead>
                            <tr class="bg-surface-container-low font-mono text-xs text-on-surface uppercase tracking-wider">
                                <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Person</th>
                                <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">NPC Gmail</th>
                                <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">ID / Dept</th>
                                <th class="py-3.5 px-6 font-semibold border-b border-outline-variant">Role</th>
                                <th class="py-3.5 px-6 font-semibold border-b border-outline-variant text-right">Change Role</th>
                            </tr>
                        </thead>
                        <tbody id="users-tbody" class="divide-y divide-outline-variant/30">
                            <tr><td colspan="5" class="p-10 text-center text-on-surface-variant">
                                <span class="inline-block animate-spin material-symbols-outlined align-middle mr-2">progress_activity</span>
                                Loading accounts…
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <p class="text-xs text-on-surface-variant flex items-start gap-2 -mt-2">
                <span class="material-symbols-outlined text-[15px] text-status-info mt-0.5">info</span>
                Protected system administrator emails cannot be demoted here. Deleting an account entirely requires Supabase Auth removal (contact IT). All role changes are written to the security audit log.
            </p>
        </div>
    </main>

    <!-- ══════════ Add-Account Modal ══════════ -->
    <div id="add-user-modal" class="npc-modal-backdrop hidden">
        <div class="npc-modal-card max-w-lg" role="dialog" aria-label="Add new account">
            <div class="px-6 py-4 border-b border-outline-variant flex items-center justify-between bg-surface-subtle">
                <h3 class="font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">person_add</span> Create New Account
                </h3>
                <button id="modal-close" class="p-1.5 rounded-full hover:bg-surface-container transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <form id="add-user-form" class="p-6 overflow-y-auto flex flex-col gap-4 text-sm">
                <!-- Role picker -->
                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-2">Account Type *</label>
                    <div class="grid grid-cols-3 gap-2" id="role-picker">
                        <label class="cursor-pointer">
                            <input type="radio" name="new-role" value="student" class="peer sr-only" checked>
                            <div class="rounded-xl border border-outline-variant p-3 text-center transition-all peer-checked:border-primary peer-checked:bg-primary/10 peer-checked:text-primary hover:bg-surface-container-low">
                                <span class="material-symbols-outlined block mx-auto mb-1 text-[22px]">school</span>
                                <span class="text-xs font-bold">Student</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="new-role" value="teacher" class="peer sr-only">
                            <div class="rounded-xl border border-outline-variant p-3 text-center transition-all peer-checked:border-secondary peer-checked:bg-secondary-container/40 peer-checked:text-on-secondary-container hover:bg-surface-container-low">
                                <span class="material-symbols-outlined block mx-auto mb-1 text-[22px]">cast_for_education</span>
                                <span class="text-xs font-bold">Teacher</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="new-role" value="admin" class="peer sr-only">
                            <div class="rounded-xl border border-outline-variant p-3 text-center transition-all peer-checked:border-error peer-checked:bg-error/10 peer-checked:text-error hover:bg-surface-container-low">
                                <span class="material-symbols-outlined block mx-auto mb-1 text-[22px]">shield_person</span>
                                <span class="text-xs font-bold">Admin</span>
                            </div>
                        </label>
                    </div>
                    <p id="role-hint" class="text-[11px] text-on-surface-variant mt-2 leading-snug"></p>
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Full Name *</label>
                    <input type="text" id="nu-name" required placeholder="e.g. Juan A. Dela Cruz"
                           class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>

                <div>
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Official NPC Gmail *</label>
                    <div class="flex items-center rounded-xl border border-outline-variant bg-surface-container-low overflow-hidden focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20">
                        <input type="text" id="nu-email-prefix" required placeholder="juandelacruz"
                               class="flex-1 bg-transparent px-3 py-2.5 text-sm font-mono focus:outline-none">
                        <span class="px-3 py-2.5 text-xs font-mono text-on-surface-variant bg-surface-container select-none">@navotaspolytechniccollege.edu.ph</span>
                    </div>
                </div>

                <div id="student-fields" class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Student No.</label>
                        <input type="text" id="nu-number" placeholder="e.g. 251505"
                               class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Program / Section</label>
                        <div class="flex gap-2">
                            <input type="text" id="nu-program" placeholder="BSIS" class="w-full min-w-0 bg-surface-container-low border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary">
                            <input type="text" id="nu-section" placeholder="2A" class="w-16 bg-surface-container-low border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary">
                        </div>
                    </div>
                </div>

                <div id="staff-fields" class="hidden">
                    <label class="block font-mono text-[10px] uppercase tracking-widest text-on-surface-variant mb-1">Department / Designation</label>
                    <input type="text" id="nu-dept" placeholder="e.g. College of Computer Studies"
                           class="w-full bg-surface-container-low border border-outline-variant rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-primary">
                </div>

                <label class="flex items-center gap-2.5 rounded-xl border border-outline-variant bg-surface-subtle px-3 py-2.5 cursor-pointer">
                    <input type="checkbox" id="nu-invite" checked class="w-4 h-4 accent-blue-600 cursor-pointer">
                    <span class="text-xs text-on-surface-variant"><strong class="text-on-surface">Send Supabase invite email</strong> so they can sign in right away</span>
                </label>
            </form>

            <div class="px-6 py-4 border-t border-outline-variant bg-surface-subtle flex justify-end items-center gap-3">
                <button type="button" id="modal-cancel" class="text-sm font-semibold text-on-surface-variant hover:text-on-surface transition-colors cursor-pointer px-3 py-2">Cancel</button>
                <button type="submit" form="add-user-form" id="btn-create" class="ripple press bg-primary text-on-primary px-6 py-2.5 rounded-xl text-sm font-bold hover:opacity-90 cursor-pointer flex items-center gap-2 npc-navy-card">
                    <span class="material-symbols-outlined text-[17px]">check_circle</span> Create Account
                </button>
            </div>
        </div>
    </div>

    <script>
        const CSRF = <?= json_encode($csrf_token) ?>;
        const MY_EMAIL = <?= json_encode(strtolower($admin_email)) ?>;

        /* ── State ─────────────────────────────────────────── */
        let USERS = [];
        let filterRole = 'all';
        let searchText = '';

        const ROLE_BADGE = {
            student: { label: 'Student', icon: 'school', cls: 'bg-primary/10 text-primary border border-primary/25' },
            teacher: { label: 'Faculty', icon: 'cast_for_education', cls: 'bg-secondary-container text-on-secondary-container border border-secondary-container' },
            admin:   { label: 'Admin', icon: 'shield_person', cls: 'bg-error/10 text-error border border-error/30' }
        };

        /* ── Load users ────────────────────────────────────── */
        async function loadUsers() {
            const tbody = document.getElementById('users-tbody');
            try {
                const res = await fetch('api_admin.php?action=list_users', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Failed to load');
                USERS = data.users || [];
                renderStats();
                renderTable();
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-10 text-center text-error text-sm">${(err.message||'Could not load accounts.')}</td></tr>`;
            }
        }

        function initials(name) {
            return String(name || '?').trim().split(/\s+/).slice(0, 2).map(w => w[0]).join('').toUpperCase() || '?';
        }

        function esc(s) {
            const d = document.createElement('div');
            d.textContent = s == null ? '' : String(s);
            return d.innerHTML;
        }

        function renderStats() {
            document.getElementById('stat-all').setAttribute('data-countup', USERS.length);
            document.getElementById('stat-student').setAttribute('data-countup', USERS.filter(u => u.role === 'student').length);
            document.getElementById('stat-teacher').setAttribute('data-countup', USERS.filter(u => u.role === 'teacher').length);
            document.getElementById('stat-admin').setAttribute('data-countup', USERS.filter(u => u.role === 'admin').length);
            ['stat-all','stat-student','stat-teacher','stat-admin'].forEach(id => {
                const el = document.getElementById(id);
                if (window.npcCountUp) window.npcCountUp(el); else el.textContent = el.getAttribute('data-countup');
            });
        }

        function filteredUsers() {
            const q = searchText.toLowerCase().trim();
            return USERS.filter(u => {
                if (filterRole !== 'all' && u.role !== filterRole) return false;
                if (!q) return true;
                return [u.full_name, u.email, u.student_number].join(' ').toLowerCase().includes(q);
            });
        }

        function isProtected(email) {
            const protectedList = [
                'admin@navotaspolytechniccollege.edu.ph',
                'jderramas251505@navotaspolytechniccollege.edu.ph'
            ];
            return protectedList.includes(String(email || '').toLowerCase());
        }

        function renderTable() {
            const tbody = document.getElementById('users-tbody');
            const rows = filteredUsers();

            if (!rows.length) {
                tbody.innerHTML = '<tr><td colspan="5" class="p-10 text-center text-on-surface-variant">No accounts match your filters.</td></tr>';
                return;
            }

            tbody.innerHTML = rows.map((u, i) => {
                const badge = ROLE_BADGE[u.role] || ROLE_BADGE.student;
                const prot = isProtected(u.email);
                const self = String(u.email || '').toLowerCase() === MY_EMAIL;
                const sub = u.role === 'student'
                    ? `${esc(u.program || '—')} · ${esc(u.section || '—')} · ${esc(u.student_number || '')}`
                    : esc(u.program || u.student_number || 'Staff');
                const opts = ['student','teacher','admin'].map(r =>
                    `<option value="${r}" ${r === u.role ? 'selected' : ''}>${ROLE_BADGE[r].label}</option>`).join('');
                return `
                <tr class="hover:bg-surface-container-low/60 transition-colors user-row" data-search="${esc([u.full_name,u.email,u.student_number].join(' ').toLowerCase())}">
                    <td class="py-3 px-6">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-xs shrink-0 npc-navy-card">${initials(u.full_name)}</div>
                            <div>
                                <p class="font-semibold text-on-surface leading-tight">${esc(u.full_name)}${self ? ' <span class=\"ml-1 text-[10px] font-mono text-status-success\">YOU</span>' : ''}${prot ? ' <span class=\"ml-1 text-[10px] font-mono text-error\">PROTECTED</span>' : ''}</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-6 font-mono text-xs text-on-surface-variant break-all">${esc(u.email)}</td>
                    <td class="py-3 px-6 text-xs text-on-surface-variant">${sub}</td>
                    <td class="py-3 px-6">
                        <span class="npc-role-chip ${badge.cls}"><span class="material-symbols-outlined" style="font-size:13px;">${badge.icon}</span>${badge.label}</span>
                    </td>
                    <td class="py-3 px-6 text-right">
                        ${prot ? '<span class="text-[11px] font-mono text-outline">locked</span>' :
                          `<select onchange="changeRole('${esc(u.id)}', this.value, '${esc(u.full_name)}')" ${self ? 'disabled title=\"You cannot change your own role\"' : ''}
                                  class="bg-surface border border-outline-variant rounded-lg text-xs font-semibold text-primary px-2 py-1.5 focus:outline-none focus:border-primary cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed">
                              ${opts}
                           </select>`}
                    </td>
                </tr>`;
            }).join('');
        }

        /* ── Change role ───────────────────────────────────── */
        async function changeRole(id, newRole, name) {
            if (!confirm(`Change role for ${name}?\nThey will land in the ${newRole.toUpperCase()} portal on their next sign-in.`)) {
                loadUsers();
                return;
            }
            try {
                const res = await fetch('api_admin.php?action=set_user_role', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
                    body: JSON.stringify({ id, role: newRole })
                });
                const data = await res.json();
                if (window.notify) window.notify(data.message || 'Role updated.', data.success ? 'success' : 'error');
            } catch (err) {
                if (window.notify) window.notify('Request failed: ' + err.message, 'error');
            }
            loadUsers();
        }

        /* ── Filters ───────────────────────────────────────── */
        document.querySelectorAll('.role-stat').forEach(btn => {
            btn.addEventListener('click', () => {
                filterRole = btn.getAttribute('data-filter-role');
                document.querySelectorAll('.role-stat').forEach(b =>
                    b.classList.toggle('ring-2', b === btn) || b.classList.toggle('ring-primary', b === btn));
                renderTable();
            });
        });
        document.getElementById('user-search').addEventListener('input', e => {
            searchText = e.target.value;
            renderTable();
        });

        /* ── Add-account modal ─────────────────────────────── */
        const modal = document.getElementById('add-user-modal');
        const roleHint = document.getElementById('role-hint');
        const HINTS = {
            student: 'Students see schedules, grades, QR attendance and the campus AI — nothing else.',
            teacher: 'Teachers get the Faculty portal: classes, attendance QR, grade encoding and their teaching AI.',
            admin:   'Admins get full access: every portal, approvals, directory, announcements and user management.'
        };

        function openModal() {
            modal.classList.remove('hidden');
            updateHints();
            setTimeout(() => document.getElementById('nu-name').focus(), 60);
        }
        function closeModal() { modal.classList.add('hidden'); }
        function updateHints() {
            const sel = document.querySelector('input[name="new-role"]:checked').value;
            roleHint.textContent = HINTS[sel];
            document.getElementById('student-fields').classList.toggle('hidden', sel !== 'student');
            document.getElementById('staff-fields').classList.toggle('hidden', sel === 'student');
        }
        document.getElementById('role-picker').addEventListener('change', updateHints);
        document.getElementById('btn-add-user').addEventListener('click', openModal);
        document.getElementById('modal-close').addEventListener('click', closeModal);
        document.getElementById('modal-cancel').addEventListener('click', closeModal);
        modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

        /* ── Submit create ─────────────────────────────────── */
        document.getElementById('add-user-form').addEventListener('submit', async e => {
            e.preventDefault();
            const btn = document.getElementById('btn-create');
            const role = document.querySelector('input[name="new-role"]:checked').value;

            const payload = {
                role,
                full_name: document.getElementById('nu-name').value.trim(),
                email: document.getElementById('nu-email-prefix').value.trim().toLowerCase() + '@navotaspolytechniccollege.edu.ph',
                number: document.getElementById('nu-number').value.trim(),
                program: role === 'student' ? document.getElementById('nu-program').value.trim()
                                            : document.getElementById('nu-dept').value.trim(),
                section: document.getElementById('nu-section').value.trim(),
                send_invite: document.getElementById('nu-invite').checked
            };
            if (!payload.number && role !== 'student') delete payload.number;
            if (!payload.section) delete payload.section;

            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined text-[17px] animate-spin">progress_activity</span> Creating…';

            try {
                const res = await fetch('api_admin.php?action=create_user', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (window.notify) window.notify(data.message || (data.success ? 'Account created.' : 'Failed.'), data.success ? 'success' : 'error', 5200);
                if (data.success) {
                    if (window.npcConfetti) window.npcConfetti.burst(70);
                    closeModal();
                    e.target.reset();
                    loadUsers();
                }
            } catch (err) {
                if (window.notify) window.notify('Request failed: ' + err.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-[17px]">check_circle</span> Create Account';
            }
        });

        loadUsers();
    </script>
</body>
</html>
