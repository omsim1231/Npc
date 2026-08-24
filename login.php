<?php
require_once __DIR__ . '/auth.php';

// If already logged in with a valid session, redirect to appropriate portal
if (isSessionValid() && isset($_SESSION['role'])) {
    $role = $_SESSION['role'];
    if ($role === 'admin') {
        header("Location: /admin.php");
    } elseif ($role === 'teacher') {
        header("Location: /teacher.php");
    } else {
        header("Location: /index.php");
    }
    exit();
}

$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In · NPC Connect</title>
    <!-- Theme bootstrap: respect saved preference before first paint -->
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
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
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
    <link rel="stylesheet" href="styles.css?v=<?= filemtime(__DIR__ . '/styles.css') ?>">
    <script src="npc.js?v=<?= filemtime(__DIR__ . '/npc.js') ?>"></script>
    <!-- Supabase JS Client -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <style>
        /* Login-specific choreography */
        @keyframes heroWordUp {
            from { opacity: 0; transform: translateY(26px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .hero-word { animation: heroWordUp .7s cubic-bezier(.22,.68,.32,1) both; }
        .hero-word:nth-child(2) { animation-delay: .10s; }
        .hero-word:nth-child(3) { animation-delay: .20s; }
        .login-card { animation: popIn .55s cubic-bezier(.22,.68,.32,1) both .15s; }
        @keyframes ringSpin { to { transform: rotate(360deg); } }
        .emblem-ring {
            animation: ringSpin 14s linear infinite;
        }
        .feature-row { transition: transform .3s cubic-bezier(.34,1.56,.64,1), background-color .3s ease; }
        .feature-row:hover { transform: translateX(6px); background: rgba(255,255,255,.08); }
        @media (prefers-reduced-motion: reduce) {
            .hero-word, .login-card, .emblem-ring { animation: none !important; opacity: 1 !important; transform: none !important; }
            .aurora-scene .orb { display: none; }
        }
    </style>
</head>
<body class="font-sans min-h-screen antialiased">

    <!-- Floating dark-mode toggle -->
    <button id="npc-login-theme" type="button" aria-label="Toggle dark mode"
            class="fixed top-4 right-4 z-50 w-11 h-11 rounded-full glass-chip text-white flex items-center justify-center cursor-pointer press ripple hover:scale-105 transition-transform">
        <span class="material-symbols-outlined text-[20px]"></span>
    </button>

    <!-- ══════════ Aurora Hero Stage ══════════ -->
    <div class="relative min-h-screen w-full overflow-hidden flex items-center justify-center p-4"
         style="background: linear-gradient(150deg, #001736 0%, #012a5e 45%, #06457f 80%, #0a4d8c 100%);">

        <!-- Animated orbs -->
        <div class="aurora-scene absolute inset-0" aria-hidden="true">
            <span class="orb orb-gold"  style="width:420px;height:420px;top:-120px;left:-100px;"></span>
            <span class="orb orb-blue"  style="width:520px;height:520px;bottom:-180px;right:-140px;animation-delay:-5s;"></span>
            <span class="orb orb-azure" style="width:300px;height:300px;top:40%;left:58%;animation-delay:-9s;"></span>
        </div>

        <!-- Subtle grid texture -->
        <div class="absolute inset-0 opacity-[0.05] pointer-events-none" aria-hidden="true"
             style="background-image:linear-gradient(rgba(255,255,255,.7) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,.7) 1px, transparent 1px);background-size:44px 44px;"></div>

        <div class="relative z-10 w-full max-w-5xl grid lg:grid-cols-2 gap-10 items-center">

            <!-- ────────── Left: Brand story (desktop only) ────────── -->
            <div class="hidden lg:flex flex-col gap-8 text-white pr-6">
                <div class="flex items-center gap-4">
                    <div class="relative w-16 h-16 shrink-0">
                        <span class="emblem-ring absolute inset-0 rounded-full border border-dashed border-white/30"></span>
                        <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg"
                            alt="NPC Emblem" class="absolute inset-1.5 w-[52px] h-[52px] rounded-full object-cover bg-white/95 p-1 shadow-lg">
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold tracking-tight leading-none">NPC Connect</h1>
                        <p class="text-xs font-mono uppercase tracking-[0.25em] mt-1.5 text-white/60">Navotas Polytechnic College</p>
                    </div>
                </div>

                <h2 class="text-4xl xl:text-5xl font-extrabold tracking-tight leading-[1.08]">
                    <span class="hero-word block">One portal.</span>
                    <span class="hero-word block text-[#fed488]">Every campus journey.</span>
                    <span class="hero-word block">Zero queues.</span>
                </h2>

                <p class="text-white/70 max-w-md leading-relaxed hero-word">
                    Attendance, grades, schedules and announcements — unified in one real-time workspace powered by campus AI.
                </p>

                <ul class="flex flex-col gap-2.5 max-w-md stagger">
                    <li class="feature-row flex items-center gap-3 rounded-xl px-3 py-2.5 glass-chip text-sm font-medium">
                        <span class="material-symbols-outlined text-[#fed488] fill" style="font-size:20px;">qr_code_scanner</span>
                        QR-powered attendance in seconds
                    </li>
                    <li class="feature-row flex items-center gap-3 rounded-xl px-3 py-2.5 glass-chip text-sm font-medium">
                        <span class="material-symbols-outlined text-[#aac7ff]" style="font-size:20px;">insights</span>
                        Live gradebook &amp; academic analytics
                    </li>
                    <li class="feature-row flex items-center gap-3 rounded-xl px-3 py-2.5 glass-chip text-sm font-medium">
                        <span class="material-symbols-outlined text-[#fed488]" style="font-size:20px;">smart_toy</span>
                        AI assistant trained on official documents
                    </li>
                </ul>

                <div class="flex items-center gap-6 pt-2">
                    <div>
                        <p class="text-2xl font-extrabold text-shimmer" data-countup="1200" data-suffix="+">0</p>
                        <p class="text-[11px] font-mono uppercase tracking-wider text-white/50">Students served</p>
                    </div>
                    <div class="w-px h-10 bg-white/15"></div>
                    <div>
                        <p class="text-2xl font-extrabold text-shimmer" data-countup="99.9" data-decimals="1" data-suffix="%">0</p>
                        <p class="text-[11px] font-mono uppercase tracking-wider text-white/50">Uptime</p>
                    </div>
                    <div class="w-px h-10 bg-white/15"></div>
                    <div>
                        <p class="text-2xl font-extrabold text-shimmer" data-countup="24/7">—</p>
                        <p class="text-[11px] font-mono uppercase tracking-wider text-white/50">Campus AI</p>
                    </div>
                </div>
            </div>

            <!-- ────────── Right: Login Card ────────── -->
            <div class="flex justify-center">
                <div id="npc-login-card" class="login-card tilt relative w-full max-w-md rounded-3xl overflow-hidden
                            border border-white/20 shadow-2xl"
                     style="background: rgba(255,255,255,0.07); backdrop-filter: blur(22px); -webkit-backdrop-filter: blur(22px);">
                    <!-- top gradient edge -->
                    <div class="absolute top-0 left-0 right-0 h-1" style="background:var(--gold-gradient);background-size:200% 100%;animation:edgeFlow 5s linear infinite;"></div>

                    <div class="p-8 md:p-10">
                        <!-- Mobile emblem -->
                        <div class="flex lg:hidden flex-col items-center text-center mb-8">
                            <img src="https://lh3.googleusercontent.com/aida-public/AB6AXuBw2c0cnwCv_1oeRDX8RrHqB8stLSsvw54RTFe98wFq4BWHUYCUWe_n4VIn0TTBVuKRAIGEEstk3Ke_R0xZIOIGA7_KVCxmBnue7ebhQU5KAPQFjEYS4Q_1Od8flcRGIrJQJJ4_ZTwrY1ZB2LpoHuv_Tfu6eqPO7_bctjIIOYu6rZwcGbg5SKlN21OW-8M3k0Aebeq1lrjfeZMMH7m2opfoykjE6dUN9304WLzTxc2OwOn_cSbFUlisvg"
                                alt="NPC Emblem" class="w-20 h-20 rounded-full mb-4 object-cover bg-white p-1 border border-white/30 shadow-lg animate-float">
                            <h1 class="text-2xl font-bold text-white">NPC Connect</h1>
                            <p class="text-sm text-white/60 mt-1">Sign in to your academic portal</p>
                        </div>

                        <div class="mb-7">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-mono font-bold uppercase tracking-[0.18em] glass-chip text-white/85">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse-dot"></span>
                                Secure Campus Access
                            </span>
                            <h2 class="text-2xl font-bold text-white mt-4">Welcome back</h2>
                            <p class="text-sm text-white/60 mt-1">Sign in to continue to your dashboard.</p>
                        </div>

                        <div class="flex flex-col gap-4" id="login-container">
                            <button id="google-login-btn"
                                    class="ripple btn-shine press w-full bg-white hover:bg-gray-50 text-gray-800 border border-white/40 font-semibold py-4 px-4 rounded-2xl transition-all shadow-lg flex items-center justify-center gap-3 cursor-pointer group">
                                <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google Logo" class="w-5 h-5">
                                <span>Sign In with Google</span>
                                <span class="material-symbols-outlined text-[18px] opacity-0 -ml-2 group-hover:opacity-70 group-hover:ml-0 transition-all">arrow_forward</span>
                            </button>

                            <p class="text-xs text-center text-white/55 mt-1 flex items-center justify-center gap-1.5">
                                <span class="material-symbols-outlined text-[14px]">lock</span>
                                Use your official NPC Gmail account.
                            </p>

                            <p id="npc-login-error" style="display:none"
                               class="text-xs text-center font-semibold text-red-200 bg-red-500/20 border border-red-300/30 rounded-xl px-3 py-2 mt-1"></p>
                        </div>

                        <!-- Divider + help -->
                        <div class="mt-8 pt-5 border-t border-white/10 flex items-center justify-between text-[11px] text-white/45 font-mono">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[13px] text-emerald-400">verified_user</span> SSO protected</span>
                            <span>NPC IT Office · v2</span>
                        </div>
                    </div>

                    <!-- Loading overlay shown during OAuth redirect -->
                    <div id="npc-login-loading" class="absolute inset-0 hidden items-center justify-center flex-col gap-3 z-20"
                         style="background:rgba(0,23,54,0.72);backdrop-filter:blur(6px);">
                        <span class="material-symbols-outlined text-white text-[34px] animate-spin">progress_activity</span>
                        <p class="text-white/80 text-sm font-medium">Redirecting to Google Sign-In…</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer strip -->
        <footer class="absolute bottom-0 inset-x-0 py-4 text-center">
            <p class="text-[11px] text-white/35 font-mono tracking-wide">© <?php echo date('Y'); ?> Navotas Polytechnic College — NPC Connect Academic Portal</p>
        </footer>
    </div>

    <script>
        // Dark-mode toggle
        (function () {
            var btn = document.getElementById('npc-login-theme');
            if (!btn) return;
            function paint() {
                var icon = document.documentElement.classList.contains('dark') ? 'light_mode' : 'dark_mode';
                btn.querySelector('.material-symbols-outlined').textContent = icon;
                // keep card readable in light mode too
                var card = document.getElementById('npc-login-card');
                if (!document.documentElement.classList.contains('dark')) {
                    card.style.background = 'rgba(255,255,255,0.07)';
                }
            }
            paint();
            btn.addEventListener('click', function () {
                var html = document.documentElement;
                html.classList.add('theme-anim');
                html.classList.toggle('dark');
                try { localStorage.setItem('npc-theme', html.classList.contains('dark') ? 'dark' : 'light'); } catch (e) {}
                paint();
                setTimeout(function () { html.classList.remove('theme-anim'); }, 500);
            });
        })();

        // Supabase config injected server-side (publishable key only)
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        document.getElementById('google-login-btn').addEventListener('click', async () => {
            const btn = document.getElementById('google-login-btn');
            const overlay = document.getElementById('npc-login-loading');
            try {
                btn.disabled = true;
                btn.classList.add('opacity-70');
                overlay.classList.remove('hidden');
                overlay.classList.add('flex');

                // Use the current origin so it works seamlessly on localhost:8000,
                // Cloudflare tunnels, or any production domain.
                const origin = window.location.origin;
                const { error } = await supabaseClient.auth.signInWithOAuth({
                    provider: 'google',
                    options: {
                        redirectTo: origin + '/auth_callback.php',
                        queryParams: { prompt: 'select_account' }
                    }
                });
                if (error) throw error;
            } catch (err) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex');
                btn.disabled = false;
                btn.classList.remove('opacity-70');
                const box = document.getElementById('npc-login-error');
                if (box) {
                    box.textContent = 'Sign-in error: ' + err.message;
                    box.style.display = 'block';
                } else {
                    alert('Error: ' + err.message);
                }
            }
        });
    </script>
</body>
</html>
