<?php
require_once 'auth.php';
require_teacher();
$teacher_name = isset($_SESSION['name']) ? (string)$_SESSION['name'] : 'Faculty Professor';
$teacher_initial = strtoupper(substr($teacher_name, 0, 1));
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Faculty AI Teaching Assistant - NPC Connect</title>
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
    </script>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
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
                <h2 class="text-xl font-bold text-primary hidden lg:block">Faculty AI Assistant</h2>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-sm shadow-sm npc-navy-card">
                    <?php echo htmlspecialchars($teacher_initial); ?>
                </div>
                <span class="text-sm font-semibold text-primary hidden sm:inline"><?php echo htmlspecialchars($teacher_name); ?></span>
            </div>
        </header>

        <!-- Canvas -->
        <div class="p-6 md:p-10 max-w-5xl w-full mx-auto space-y-6 flex-1 flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-surface-container-lowest p-6 rounded-2xl border border-outline-variant shadow-sm">
                <div>
                    <h1 class="text-2xl font-bold text-primary tracking-tight">AI Teaching & Course Assistant</h1>
                    <p class="text-xs text-on-surface-variant mt-0.5">Generate exam questions, draft lesson outlines, or ask institutional academic policy queries.</p>
                </div>
            </div>

            <!-- Suggested Quick Chips -->
            <div class="flex flex-wrap gap-2">
                <button onclick="setPrompt('Generate a 5-item quiz for Business Process Management (DM103)')" class="bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold px-3 py-1.5 rounded-xl border border-outline-variant/50 transition-colors">
                    📝 Generate Quiz for DM103
                </button>
                <button onclick="setPrompt('Create a lesson outline for Computer Programming 3 (CC107)')" class="bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold px-3 py-1.5 rounded-xl border border-outline-variant/50 transition-colors">
                    💡 Lesson Plan for CC107
                </button>
                <button onclick="setPrompt('What are the NPC grading criteria and attendance rules?')" class="bg-surface-container-low hover:bg-surface-container text-primary text-xs font-semibold px-3 py-1.5 rounded-xl border border-outline-variant/50 transition-colors">
                    📜 NPC Academic Policy
                </button>
            </div>

            <!-- Chat Window -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-2xl shadow-sm flex-1 flex flex-col overflow-hidden min-h-[500px]">
                <div id="teacher-chat-box" class="flex-1 p-6 overflow-y-auto space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-xs shrink-0 npc-navy-card">AI</div>
                        <div class="bg-surface-container-low p-4 rounded-2xl max-w-lg text-xs leading-relaxed text-on-surface">
                            Hello Professor <?php echo htmlspecialchars(explode(' ', trim($teacher_name))[0]); ?>! How can I assist you with your course syllabus, lesson plans, or exam preparations today?
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-outline-variant/40 bg-surface-subtle flex gap-3">
                    <input type="text" id="teacher-chat-input" onkeypress="if(event.key==='Enter') sendTeacherMessage()" placeholder="Ask AI to create quiz questions, lesson outlines, or grading rules..." class="flex-1 px-4 py-2.5 bg-surface border border-outline-variant/60 rounded-xl text-xs focus:outline-none focus:border-primary">
                    <button onclick="sendTeacherMessage()" id="teacher-send-btn" class="px-5 py-2.5 bg-primary text-on-primary rounded-xl text-xs font-bold hover:opacity-90 flex items-center gap-1 shadow-sm npc-navy-card">
                        <span class="material-symbols-outlined text-[16px]">send</span> Send
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        function setPrompt(text) {
            document.getElementById('teacher-chat-input').value = text;
            sendTeacherMessage();
        }

        async function sendTeacherMessage() {
            const input = document.getElementById('teacher-chat-input');
            const chatBox = document.getElementById('teacher-chat-box');
            const msg = input.value.trim();
            if (!msg) return;

            // Append User Message
            chatBox.innerHTML += `
                <div class="flex items-start justify-end gap-3">
                    <div class="bg-primary text-on-primary p-3.5 rounded-2xl max-w-lg text-xs leading-relaxed shadow-sm npc-navy-card">
                        ${msg.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")}
                    </div>
                </div>
            `;
            input.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            // Append Typing Indicator
            const loadingId = 'loading-' + Date.now();
            chatBox.innerHTML += `
                <div id="${loadingId}" class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-primary/20 text-primary flex items-center justify-center font-bold text-xs shrink-0">
                        <span class="material-symbols-outlined text-[16px]">smart_toy</span>
                    </div>
                    <div class="bg-surface-container-low p-3.5 rounded-2xl text-xs text-on-surface-variant flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce"></span>
                        <span class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                        <span class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                    </div>
                </div>
            `;
            chatBox.scrollTop = chatBox.scrollHeight;

            try {
                const res = await fetch('ask.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ question: msg, role: 'faculty' })
                });
                const data = await res.json();
                const reply = data.answer || "I am here to assist you with your classes.";

                let sourcesHtml = '';
                if (data.sources && data.sources.length > 0) {
                    sourcesHtml = `
                        <div class="mt-2 pt-2 border-t border-outline-variant/40 text-[10px] text-on-surface-variant font-mono">
                            <strong>Reference Documents:</strong> ${data.sources.map(s => s.file).join(', ')}
                        </div>
                    `;
                }

                const loadingEl = document.getElementById(loadingId);
                if (loadingEl) {
                    loadingEl.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-primary text-on-primary flex items-center justify-center font-bold text-xs shrink-0 shadow-sm npc-navy-card">
                            <span class="material-symbols-outlined text-[16px]">smart_toy</span>
                        </div>
                        <div class="bg-surface-container-low p-4 rounded-2xl max-w-xl text-xs leading-relaxed text-on-surface whitespace-pre-wrap shadow-sm border border-outline-variant/30">
                            ${reply}
                            ${sourcesHtml}
                        </div>
                    `;
                }
            } catch (err) {
                const loadingEl = document.getElementById(loadingId);
                if (loadingEl) {
                    loadingEl.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-error text-white flex items-center justify-center font-bold text-xs shrink-0">
                            <span class="material-symbols-outlined text-[16px]">error</span>
                        </div>
                        <div class="bg-error/10 text-error p-3.5 rounded-2xl max-w-lg text-xs leading-relaxed">
                            Error communicating with AI service.
                        </div>
                    `;
                }
            }
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>
