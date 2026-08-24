<?php
require_once __DIR__ . '/auth.php';
require_student_area();
// Refresh session-derived identity after the hardened guard
$is_logged_in = isset($_SESSION['user_id']);
$raw_name = (isset($_SESSION['name']) && $_SESSION['name'] !== null) ? (string)$_SESSION['name'] : 'Guest User';
$user_name = $is_logged_in ? explode(' ', trim($raw_name))[0] : 'Guest';
$full_name = $is_logged_in ? (string)$_SESSION['name'] : 'Guest User';
$user_id_display = $is_logged_in && isset($_SESSION['student_number']) ? (string)$_SESSION['student_number'] : 'GUEST';
?>
<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NPC Connect - Campus AI Assistant</title>
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

    <!-- Tailwind Config - Stitch Academic Nexus Design Tokens -->
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
        .scanner-frame {
            position: relative;
            overflow: hidden;
        }
        .scanner-frame::before, .scanner-frame::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            border-color: #002b5c; /* primary-container */
            border-style: solid;
            z-index: 10;
        }
        .scanner-frame::before {
            top: 0;
            left: 0;
            border-width: 4px 0 0 4px;
        }
        .scanner-frame::after {
            bottom: 0;
            right: 0;
            border-width: 0 4px 4px 0;
        }
        .scanner-frame-tr {
            position: absolute;
            top: 0;
            right: 0;
            width: 40px;
            height: 40px;
            border-color: #002b5c;
            border-style: solid;
            border-width: 4px 4px 0 0;
            z-index: 10;
        }
        .scanner-frame-bl {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 40px;
            border-color: #002b5c;
            border-style: solid;
            border-width: 0 0 4px 4px;
            z-index: 10;
        }
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background-color: #fed488; /* secondary-container */
            box-shadow: 0 0 8px #fed488;
            top: 0;
            left: 0;
            animation: scan 2s linear infinite;
            z-index: 5;
        }
        @keyframes scan {
            0% { top: 0; opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
    </style>    <script id="npc-role-meta" type="application/json"><?= json_encode(['role' => $_SESSION['role'] ?? 'student', 'email' => $_SESSION['email'] ?? '', 'name' => $_SESSION['name'] ?? '']) ?></script>
</head>

<body class="bg-surface text-on-surface font-sans min-h-screen flex antialiased">
    <?php include __DIR__ . '/_denied_banner.php'; ?>

    <!-- App Container -->
    <div class="flex min-h-screen w-full" id="app-root">

        <!-- SideNavBar (Sticky Desktop navigation) -->
        <?php $NPC_PORTAL = 'student'; include __DIR__ . '/_sidebar.php'; ?>

        <!-- Main Workspace Area -->
        <div class="flex-1 flex flex-col min-w-0 bg-surface" id="main-wrapper">

            <!-- TopNavBar Header -->
            <header class="h-16 bg-surface-container-lowest border-b border-outline-variant px-6 md:px-10 flex items-center justify-between sticky top-0 z-30 shadow-sm" id="topbar">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-primary lg:hidden">NPC Connect</span>
                    <h2 class="text-xl font-bold text-primary hidden lg:block" id="page-title">AI Assistant</h2>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-semibold bg-surface-container px-3 py-1.5 rounded-md border border-outline-variant text-primary" id="user-id-chip">ID: <?php echo htmlspecialchars($user_id_display); ?></span>
                        <img alt="User profile avatar" class="w-9 h-9 rounded-full object-cover border border-outline-variant shadow-sm"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDY0YlFx4kB5x_Rj0yQKvW09upvbRIgsxiTOfv_YigLlswS0RXYGufPqUxoUuVd_bLZ_KE0GB0Ptj2-vztXffkP_cnsXegpkXH74h3zlnDr9hKjw2qrYP-VCz3m7k_WVJkoXu3TTogTQrvCuK0foEFWF6UW_ls96NG-zedSKfDJmwR-nGFSKnjpKJtj_siJzuRiXlEkZKKfUHgQqSYXq-qqp9U-UFk-qgYsAClMs8P3C9NGcDm1eQMFNg">
                        <span class="text-sm font-semibold text-primary hidden sm:inline" id="user-name-display"><?php echo htmlspecialchars($full_name); ?></span>
                    </div>
                </div>
            </header>

            <!-- Page Canvas -->
            <main class="flex-1 p-6 md:p-10 max-w-7xl w-full mx-auto lg:pl-64" id="canvas-container">
            
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
                            <div class="flex-1 overflow-y-auto p-2 space-y-1" id="conversation-list">
                                <div class="flex flex-col items-center justify-center py-8 text-center px-4">
                                    <span class="material-symbols-outlined text-[36px] text-outline-variant mb-2">forum</span>
                                    <p class="text-sm text-on-surface-variant">No conversations yet. Start a new one!</p>
                                </div>
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

                </main>
        </div>
    </div>
</body>
</html>
