<?php
require_once 'auth.php';
require_admin();
?>
<!DOCTYPE html>
<html class="light" lang="en">

<head>
        <meta charset="utf-8">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <title>NPC Connect - Admin Dashboard</title>
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
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com" rel="preconnect">
        <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect">
        <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;600;700&amp;family=JetBrains+Mono:wght@400;500&amp;display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
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

<body class="bg-background text-on-background font-body-md min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>
            <!-- SideNavBar Desktop -->
    <?php $NPC_PORTAL = 'admin'; include __DIR__ . '/_sidebar.php'; ?>
        <!-- Main Content Canvas -->
        <main class="flex-1 lg:ml-64 bg-surface min-h-screen">
                <!-- TopNavBar (from JSON) for Desktop Context -->
                <header class="hidden md:flex fixed top-0 left-0 lg:left-64 right-0 h-16 bg-surface dark:bg-inverse-surface border-b border-outline-variant dark:border-outline z-30 px-margin-desktop items-center justify-between">
                        <div class="flex items-center gap-4 flex-1">
                                <div class="relative w-96">
                                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
                                        <input class="w-full pl-10 pr-4 py-2 bg-surface-subtle border border-outline-variant rounded-full font-label-md text-label-md text-on-surface focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary" placeholder="Search students, courses..." type="text">
                                </div>
                        </div>
                        <div class="flex items-center gap-6">
                                <button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer active:opacity-80">
                                        <span class="material-symbols-outlined">notifications</span>
                                </button>
                                <button class="text-on-surface-variant hover:text-primary transition-colors cursor-pointer active:opacity-80">
                                        <span class="material-symbols-outlined">help</span>
                                </button>
                                <img class="w-10 h-10 rounded-full border border-outline-variant object-cover" data-alt="A professional headshot of a female academic administrator with a warm smile, wearing glasses and a navy blazer, in a bright modern office setting with subtle depth of field." src="https://lh3.googleusercontent.com/aida-public/AB6AXuDGXbxWVonFni4AlEzvwDngoGWicGzWh9C-P6C0ois34tT8WJBRSi_BE3S-w4HBt9XIA2gcu8vbyZU9v2PJLXsjJEmksjPF3w4Gzc76t4nmLvWUUMjskMb9f3xCxSyUaHRe7Wmxng59WYlBfx29j1K0oEaMW1qBwp84NooexG9GNGx003FqwGVvASFvxZzF4Rh0CwwT1rVbmUAcWMR2k_qPr7wXX3D2CuNhAxUnUAwaZo8qDgY8PGIhkg">
                        </div>
                </header>
                <!-- Top Nav Mobile Replacement padding -->
                <div class="md:h-16"></div>
                <div class="p-margin-mobile md:p-margin-desktop max-w-container-max mx-auto h-full">
<!-- 3. NPC AI ASSISTANT CHAT VIEW -->
                <div id="chatbot-view" class="view active">
                    <div class="h-[calc(100vh-8rem)] flex overflow-hidden bg-surface border border-outline-variant rounded-2xl shadow-sm">
                        <!-- Conversation History Sidebar -->
                        <aside class="hidden md:flex flex-col w-72 bg-surface-subtle border-r border-outline-variant">
                            <div class="p-4 border-b border-outline-variant">
                                <button class="w-full flex items-center justify-center gap-2 bg-primary text-on-primary py-2.5 px-4 rounded-xl hover:bg-primary/90 transition-colors shadow-sm">
                                    <span class="material-symbols-outlined text-sm">add</span>
                                    <span class="font-mono text-sm font-semibold">New Conversation</span>
                                </button>
                            </div>
                            <div class="flex-1 overflow-y-auto p-2 space-y-1">
                                <div class="px-3 py-2 text-on-surface-variant font-mono text-xs font-semibold opacity-70 mt-2">Today</div>
                                <button class="w-full text-left px-3 py-2.5 rounded-lg bg-surface-container text-on-surface flex items-center gap-3 border border-outline-variant/50">
                                    <span class="material-symbols-outlined text-outline">chat_bubble</span>
                                    <span class="text-base truncate">Enrollment Deadlines</span>
                                </button>
                                <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                    <span class="text-base truncate">Degree Audit Help</span>
                                </button>
                                <div class="px-3 py-2 text-on-surface-variant font-mono text-xs font-semibold opacity-70 mt-4">Previous 7 Days</div>
                                <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                    <span class="text-base truncate">Library Access Hours</span>
                                </button>
                                <button class="w-full text-left px-3 py-2.5 rounded-lg hover:bg-surface-container-low text-on-surface-variant flex items-center gap-3 transition-colors">
                                    <span class="material-symbols-outlined text-outline-variant">chat_bubble</span>
                                    <span class="text-base truncate">Campus Wi-Fi Setup</span>
                                </button>
                            </div>
                        </aside>

                        <!-- Active Chat Canvas -->
                        <section class="flex-1 flex flex-col bg-surface relative">
                            <!-- Chat Header -->
                            <div class="px-6 py-4 border-b border-outline-variant bg-surface-container-lowest flex justify-between items-center shadow-sm z-10">
                                <div>
                                    <h2 class="text-2xl font-bold text-on-surface">NPC AI Assistant</h2>
                                    <p class="font-mono text-sm font-semibold text-on-surface-variant">Your official student information assistant</p>
                                </div>
                                <!-- Safety UI Indicator -->
                                <div class="flex items-center gap-2 bg-surface-container-high px-3 py-1.5 rounded-full border border-outline-variant">
                                    <span class="material-symbols-outlined text-status-info" style="font-size: 18px;">shield</span>
                                    <span class="font-mono text-xs font-semibold text-on-surface-variant">Warning Count: <span class="font-bold">0/3</span></span>
                                </div>
                            </div>

                            <!-- Chat Messages Area -->
                            <div id="chat-messages" class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar bg-background">
                                <!-- Intro Message -->
                                <div class="flex justify-center my-4">
                                    <div class="bg-surface-container-low px-4 py-2 rounded-full border border-outline-variant text-on-surface-variant font-mono text-xs font-semibold">
                                        Conversation started automatically
                                    </div>
                                </div>

                                <!-- Initial AI Message -->
                                <div class="flex flex-col items-start w-full">
                                    <div class="flex gap-3 max-w-[90%] md:max-w-[80%]">
                                        <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 mt-1 shadow-sm">
                                            <span class="material-symbols-outlined text-on-primary" style="font-size: 18px;">smart_toy</span>
                                        </div>
                                        <div class="bg-surface-container-lowest border border-outline-variant p-4 rounded-xl rounded-tl-none shadow-sm flex flex-col gap-3">
                                            <div class="text-base text-on-surface space-y-3">
                                                <p>Hello! I am your NPC academic assistant. You can ask me any question regarding campus policies, course requirements, or administrative documents in the knowledge base.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span class="font-mono text-xs font-semibold text-on-surface-variant mt-1 ml-11">Now</span>
                                </div>
                            </div>

                            <!-- Input Area -->
                            <div class="p-6 bg-surface-container-lowest border-t border-outline-variant shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
                                <div class="max-w-4xl mx-auto flex items-end gap-2 bg-surface-subtle border border-outline-variant rounded-2xl focus-within:border-primary focus-within:ring-1 focus-within:ring-primary overflow-hidden pr-2 pl-4 py-2 shadow-inner transition-shadow">
                                    <textarea id="chat-input" class="flex-1 bg-transparent border-none focus:outline-none focus:ring-0 resize-none text-base text-on-surface py-2 max-h-32 overflow-y-auto" placeholder="Ask about academic policies, campus services, or your records..." rows="1" style="min-height: 40px;"></textarea>
                                    <div class="flex items-center gap-1 pb-1">
                                        <button aria-label="Attach file" class="text-on-surface-variant p-2 hover:bg-surface-container-high hover:text-primary rounded-full transition-colors cursor-pointer">
                                            <span class="material-symbols-outlined">attach_file</span>
                                        </button>
                                        <button id="chat-send-btn" aria-label="Send message" onclick="sendMessage()" class="text-white bg-primary p-2 hover:bg-primary/90 rounded-full transition-colors flex items-center justify-center shadow-md cursor-pointer">
                                            <span class="material-symbols-outlined" style="font-size: 20px;">send</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-center mt-2">
                                    <p class="font-mono text-xs font-semibold text-on-surface-variant/70">AI Assistant may produce inaccurate information. Always verify against official documents.</p>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                </div>
</main>
        <!-- BottomNavBar (from JSON) for Mobile Context -->
        <nav class="lg:hidden fixed bottom-0 left-0 w-full z-50 flex justify-around items-center px-4 pb-safe h-16 bg-surface dark:bg-inverse-surface border-t border-outline-variant dark:border-outline shadow-lg rounded-t-full">
                <!-- Active Tab: Home (Dashboard intent) -->
                <a class="flex flex-col items-center justify-center bg-secondary-container text-on-secondary-container rounded-full px-4 py-1 scale-95 active:scale-90 transition-transform" href="#">
                        <span class="material-symbols-outlined">home</span>
                        <span class="font-label-sm text-label-sm">Home</span>
                </a>
                <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-container-high scale-95 active:scale-90 transition-transform px-2 py-1 rounded-xl" href="admin_docs.php">
                        <span class="material-symbols-outlined">folder_open</span>
                        <span class="font-label-sm text-label-sm">Docs</span>
                </a>
                <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-container-high scale-95 active:scale-90 transition-transform px-2 py-1 rounded-xl" href="#">
                        <span class="material-symbols-outlined">chat_bubble</span>
                        <span class="font-label-sm text-label-sm">AI Chat</span>
                </a>
                <a class="flex flex-col items-center justify-center text-on-surface-variant active:bg-surface-container-high scale-95 active:scale-90 transition-transform px-2 py-1 rounded-xl" href="#">
                        <span class="material-symbols-outlined">person</span>
                        <span class="font-label-sm text-label-sm">Profile</span>
                </a>
        </nav>
        <script src="/app.js"></script>
</body>

</html>