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
    <title>System Settings & Document Services - NPC Connect Admin</title>
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
    </script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
    <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>

    <!-- Main Workspace -->
    <main class="flex-1 lg:pl-64 bg-surface min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-primary lg:hidden">NPC Admin</span>
                <h2 class="text-xl font-bold text-primary hidden lg:block">System Configuration & Student Services</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                    <?= htmlspecialchars($admin_initial) ?>
                </div>
                <span class="text-sm font-semibold text-primary hidden sm:inline"><?= htmlspecialchars($admin_name) ?></span>
            </div>
        </header>

        <!-- Canvas Container -->
        <div class="p-6 md:p-10 max-w-7xl w-full mx-auto space-y-8 flex-1">
            <div class="border-b border-outline-variant/60 pb-6">
                <h1 class="text-2xl font-bold text-primary">Student Document Requests & System Settings</h1>
                <p class="text-sm text-on-surface-variant mt-1">Process registrar document requests, update statuses, and configure institutional portal security parameters.</p>
            </div>

            <!-- 1. REGISTRAR DOCUMENT REQUEST QUEUE -->
            <section class="bg-surface-container-lowest border border-outline-variant rounded-2xl overflow-hidden shadow-sm">
                <div class="p-6 border-b border-outline-variant flex justify-between items-center bg-surface-subtle">
                    <div>
                        <h3 class="font-bold text-primary text-base flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">receipt_long</span>
                            Registrar Document Processing Queue
                        </h3>
                        <p class="text-xs text-on-surface-variant mt-0.5">Manage student requests for COR, COE, Good Moral, and Transcript certifications.</p>
                    </div>
                    <span class="text-xs font-mono font-bold bg-primary/10 text-primary px-3 py-1 rounded-full" id="doc-queue-count">0 Requests</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-surface-container-low font-mono text-on-surface uppercase tracking-wider">
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Reference #</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Student</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Document</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Purpose</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Date</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant">Status</th>
                                <th class="py-3 px-6 font-semibold border-b border-outline-variant text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="document-requests-tbody" class="divide-y divide-outline-variant/30">
                            <tr><td colspan="7" class="p-8 text-center text-gray-400">Loading document requests...</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 2. INSTITUTIONAL SETTINGS -->
            <section class="bg-surface-container-lowest border border-outline-variant rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-outline-variant/60 pb-3">
                    <span class="material-symbols-outlined text-primary text-[20px]">domain</span>
                    <h3 class="font-bold text-primary text-base">Institutional Configuration</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Institution Name:</label>
                        <input type="text" value="Navotas Polytechnic College" readonly class="w-full bg-surface-container-low border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Active Academic Term:</label>
                        <input type="text" value="1st Semester, AY 2026-2027" readonly class="w-full bg-surface-container-low border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Registrar Contact Email:</label>
                        <input type="text" value="registrar@navotaspolytechniccollege.edu.ph" readonly class="w-full bg-surface-container-low border border-gray-300 rounded-xl px-3 py-2 text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block font-bold text-gray-700 mb-1">Security Authentication Mode:</label>
                        <input type="text" value="Hardened Supabase JWT Server-Side Verification" readonly class="w-full bg-emerald-50 text-emerald-800 border border-emerald-300 rounded-xl px-3 py-2 text-xs font-bold font-mono">
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const csrfToken = <?= json_encode($csrf_token) ?>;

        async function loadDocumentRequests() {
            try {
                const res = await fetch('api_admin.php?action=get_document_requests');
                const data = await res.json();
                const tbody = document.getElementById('document-requests-tbody');
                const countBadge = document.getElementById('doc-queue-count');

                if (data.success && data.requests && data.requests.length > 0) {
                    countBadge.innerText = `${data.requests.length} Requests`;
                    tbody.innerHTML = data.requests.map(r => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-6 font-mono font-bold text-primary">${r.reference_no}</td>
                            <td class="py-3 px-6">
                                <div class="font-bold">${r.student_name || 'Student'}</div>
                                <div class="font-mono text-[10px] text-gray-500">${r.student_number || ''}</div>
                            </td>
                            <td class="py-3 px-6 font-bold text-gray-800">${r.document_type}</td>
                            <td class="py-3 px-6 text-gray-600">${r.purpose}</td>
                            <td class="py-3 px-6 font-mono text-gray-500">${new Date(r.requested_at).toLocaleDateString()}</td>
                            <td class="py-3 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold ${r.status === 'Ready for Pickup' || r.status === 'Released' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'}">${r.status}</span>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <select onchange="updateDocStatus('${r.id}', this.value)" class="bg-white border border-gray-300 rounded-lg px-2 py-1 text-[11px] font-semibold">
                                    <option value="">Update Status...</option>
                                    <option value="Processing">Processing</option>
                                    <option value="Ready for Pickup">Ready for Pickup</option>
                                    <option value="Released">Released</option>
                                    <option value="Rejected">Rejected</option>
                                </select>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    countBadge.innerText = '0 Requests';
                    tbody.innerHTML = '<tr><td colspan="7" class="p-8 text-center text-gray-400">No document requests submitted yet.</td></tr>';
                }
            } catch (err) {
                console.error(err);
            }
        }

        async function updateDocStatus(id, status) {
            if (!status) return;
            const remarks = prompt(`Enter optional remarks for setting status to ${status}:`, '');
            if (remarks === null) return;

            try {
                const res = await fetch('api_admin.php?action=update_document_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrfToken },
                    body: JSON.stringify({ request_id: id, status: status, remarks: remarks, csrf_token: csrfToken })
                });

                const data = await res.json();
                if (data.success) {
                    await loadDocumentRequests();
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (err) {
                alert('Update Error: ' + err.message);
            }
        }

        loadDocumentRequests();
    </script>
</body>
</html>
