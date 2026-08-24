/* ============================================================
   npc.js — NPC Connect UX Engine v2
   Global micro-interaction + data-viz layer. Auto-loads on
   every page via _head.php (and legacy pages that include it).
   Everything is defensive: missing DOM = no-op.
   ============================================================ */
(function () {
    'use strict';

    var REDUCED = false;
    try { REDUCED = window.matchMedia('(prefers-reduced-motion: reduce)').matches; } catch (e) {}

    /* --------------------------------------------------------
       1. COUNT-UP — animate any [data-countup] to its value.
          Usage: <span data-countup="1234">0</span>
                 <span data-countup="92.4" data-decimals="1" data-suffix="%">
       -------------------------------------------------------- */
    function animateCountUp(el) {
        if (el.__npcCounted) return;
        el.__npcCounted = true;
        var target = parseFloat(el.getAttribute('data-countup'));
        if (isNaN(target)) return;
        var decimals = parseInt(el.getAttribute('data-decimals') || '0', 10);
        var suffix = el.getAttribute('data-suffix') || '';
        var prefix = el.getAttribute('data-prefix') || '';
        var dur = parseInt(el.getAttribute('data-duration') || '1100', 10);

        if (REDUCED) { el.textContent = prefix + target.toFixed(decimals) + suffix; return; }

        var t0 = null;
        function frame(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3); /* easeOutCubic */
            el.textContent = prefix + (target * eased).toFixed(decimals) + suffix;
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }
    window.npcCountUp = animateCountUp;

    /* --------------------------------------------------------
       2. RING CHART — animate SVG circles with data-ring-pct.
          Usage: <circle data-ring-pct="78" r="15.9155" … />
       -------------------------------------------------------- */
    var RING_CIRCUM = 100; /* pathLength=100 convention used by NPC donuts */

    function animateRing(circle) {
        if (circle.__npcRingDone) return;
        circle.__npcRingDone = true;
        var pct = Math.max(0, Math.min(100, parseFloat(circle.getAttribute('data-ring-pct')) || 0));
        circle.setAttribute('pathLength', '100');
        circle.setAttribute('stroke-dasharray', '100');
        if (REDUCED) {
            circle.setAttribute('stroke-dashoffset', String(100 - pct));
            return;
        }
        circle.setAttribute('stroke-dashoffset', '100');
        var t0 = null;
        var dur = 1200;
        function frame(ts) {
            if (!t0) t0 = ts;
            var p = Math.min((ts - t0) / dur, 1);
            var eased = 1 - Math.pow(1 - p, 3);
            circle.setAttribute('stroke-dashoffset', String(100 - pct * eased));
            if (p < 1) requestAnimationFrame(frame);
        }
        requestAnimationFrame(frame);
    }

    /* Run all pending animations inside a root element */
    function runAnimations(root) {
        root = root || document;
        root.querySelectorAll('[data-countup]').forEach(animateCountUp);
        root.querySelectorAll('circle[data-ring-pct], path[data-ring-pct]').forEach(function (el) {
            /* wait a tick so layout settles before measuring/animating */
            setTimeout(function () { animateRing(el); }, 60);
        });
        root.querySelectorAll('.reveal:not(.revealed)').forEach(function (el) { revealObserver.observe(el); });
    }
    window.npcRunAnimations = runAnimations;

    /* --------------------------------------------------------
       3. SCROLL REVEAL
       -------------------------------------------------------- */
    var revealObserver = ('IntersectionObserver' in window)
        ? new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) {
                    en.target.classList.add('revealed');
                    revealObserver.unobserve(en.target);
                }
            });
        }, { threshold: 0.08 })
        : null;

    /* --------------------------------------------------------
       4. RIPPLE — attach to .ripple elements via event delegation
       -------------------------------------------------------- */
    document.addEventListener('pointerdown', function (e) {
        var host = e.target.closest ? e.target.closest('.ripple') : null;
        if (!host || REDUCED) return;
        var rect = host.getBoundingClientRect();
        var ink = document.createElement('span');
        ink.className = 'npc-ink';
        var size = Math.max(rect.width, rect.height);
        ink.style.width = ink.style.height = size + 'px';
        ink.style.left = (e.clientX - rect.left - size / 2) + 'px';
        ink.style.top = (e.clientY - rect.top - size / 2) + 'px';
        host.appendChild(ink);
        setTimeout(function () { ink.remove(); }, 600);
    }, { passive: true });

    /* --------------------------------------------------------
       5. 3D TILT — elements with class "tilt"
       -------------------------------------------------------- */
    function bindTilt(el) {
        if (el.__npcTilt || REDUCED) return;
        el.__npcTilt = true;
        el.addEventListener('pointermove', function (e) {
            var r = el.getBoundingClientRect();
            var x = (e.clientX - r.left) / r.width - 0.5;
            var y = (e.clientY - r.top) / r.height - 0.5;
            el.style.transform = 'perspective(800px) rotateX(' + (-y * 6).toFixed(2) + 'deg) rotateY(' + (x * 8).toFixed(2) + 'deg)';
        });
        el.addEventListener('pointerleave', function () { el.style.transform = ''; });
    }

    /* --------------------------------------------------------
       6. CONFETTI — canvas burst for celebrations
          window.npcConfetti.burst()
       -------------------------------------------------------- */
    window.npcConfetti = {
        burst: function (count) {
            if (REDUCED) return;
            count = count || 120;
            var canvas = document.createElement('canvas');
            canvas.style.cssText = 'position:fixed;inset:0;width:100vw;height:100vh;pointer-events:none;z-index:9999;';
            document.body.appendChild(canvas);
            var ctx = canvas.getContext('2d');
            var dpr = window.devicePixelRatio || 1;
            canvas.width = innerWidth * dpr; canvas.height = innerHeight * dpr;
            ctx.scale(dpr, dpr);
            var colors = ['#fed488', '#f7b955', '#aac7ff', '#0a4d8c', '#38bdf8', '#4ade80'];
            var parts = [];
            for (var i = 0; i < count; i++) {
                parts.push({
                    x: innerWidth / 2 + (Math.random() - 0.5) * innerWidth * 0.4,
                    y: innerHeight * 0.35,
                    vx: (Math.random() - 0.5) * 11,
                    vy: -Math.random() * 10 - 4,
                    w: 5 + Math.random() * 6,
                    h: 3 + Math.random() * 5,
                    rot: Math.random() * Math.PI,
                    vr: (Math.random() - 0.5) * 0.28,
                    color: colors[(Math.random() * colors.length) | 0],
                    life: 1
                });
            }
            var start = null;
            function step(ts) {
                if (!start) start = ts;
                var dt = Math.min((ts - start) / 16.7, 3); start = ts;
                ctx.clearRect(0, 0, innerWidth, innerHeight);
                var alive = 0;
                parts.forEach(function (p) {
                    if (p.life <= 0) return;
                    alive++;
                    p.vy += 0.32 * dt; p.x += p.vx * dt; p.y += p.vy * dt; p.rot += p.vr * dt;
                    if (p.y > innerHeight + 40) p.life = 0;
                    ctx.save();
                    ctx.translate(p.x, p.y);
                    ctx.rotate(p.rot);
                    ctx.globalAlpha = Math.max(p.life, 0);
                    ctx.fillStyle = p.color;
                    ctx.fillRect(-p.w / 2, -p.h / 2, p.w, p.h);
                    ctx.restore();
                });
                if (alive > 0) requestAnimationFrame(step);
                else canvas.remove();
            }
            requestAnimationFrame(step);
        }
    };

    /* --------------------------------------------------------
       7. COMMAND PALETTE — Ctrl/Cmd + K quick navigation.
          Pages can extend with window.NPC_COMMANDS = [...]
       -------------------------------------------------------- */
    function buildCommands() {
        var cmds = [
            { label: 'My Dashboard', icon: 'dashboard', href: 'index.php' },
            { label: 'My Schedule', icon: 'calendar_month', href: 'schedule.php' },
            { label: 'Academic Performance', icon: 'trending_up', href: 'academic.php' },
            { label: 'Scan Attendance QR', icon: 'qr_code_scanner', href: 'qrcode.php' },
            { label: 'AI Assistant', icon: 'smart_toy', href: 'ai_assistant.php' },
            { label: 'Settings', icon: 'settings', href: 'settings.php' },
            { label: 'Sign Out', icon: 'logout', href: 'logout.php' }
        ];
        var body = document.body.textContent || '';
        if (/Faculty Portal|Grade Encoding|Assigned Classes/.test(body)) {
            cmds.unshift(
                { label: 'Faculty Dashboard', icon: 'dashboard', href: 'teacher.php' },
                { label: 'My Assigned Classes', icon: 'school', href: 'teacher_classes.php' },
                { label: 'Live Attendance QR', icon: 'qr_code_scanner', href: 'teacher_attendance.php' },
                { label: 'Grade Encoding', icon: 'grade', href: 'teacher_grades.php' },
                { label: 'Teaching Assistant AI', icon: 'support_agent', href: 'teacher_ai_assistant.php' }
            );
        }
        if (/Admin Portal|Administrative Master Panel|System Security & Activity Logs/.test(body)) {
            cmds.unshift(
                { label: 'Admin Dashboard', icon: 'dashboard', href: 'admin.php' },
                { label: 'Grade Approvals', icon: 'verified', href: 'admin_grades.php' },
                { label: 'Classes & Rosters', icon: 'school', href: 'admin_classes.php' },
                { label: 'Student Schedules', icon: 'calendar_month', href: 'admin_schedules.php' },
                { label: 'Live Attendance QR', icon: 'qr_code_scanner', href: 'admin_attendance.php' },
                { label: 'Student Directory', icon: 'group', href: 'admin_students.php' },
                { label: 'User & Role Management', icon: 'manage_accounts', href: 'admin_users.php' },
                { label: 'Announcements', icon: 'campaign', href: 'admin_announcements.php' },
                { label: 'Documents & AI Knowledge Base', icon: 'description', href: 'admin_docs.php' },
                { label: 'System Settings', icon: 'settings', href: 'admin_settings.php' }
            );
        }
        /* de-dupe by href+label, page-provided extras first */
        var extra = Array.isArray(window.NPC_COMMANDS) ? window.NPC_COMMANDS : [];
        var seen = {};
        return extra.concat(cmds).filter(function (c) {
            var k = c.href + '|' + c.label;
            if (seen[k]) return false;
            seen[k] = true;
            return true;
        });
    }

    function openPalette() {
        var existing = document.getElementById('npc-cmdk');
        if (existing) { closePalette(); return; }
        var commands = buildCommands();
        var wrap = document.createElement('div');
        wrap.id = 'npc-cmdk';
        wrap.innerHTML =
            '<div class="npc-cmdk-panel">' +
            '  <div style="display:flex;align-items:center;gap:.6rem;padding:0 1rem;">' +
            '    <span class="material-symbols-outlined" style="color:rgb(var(--on-surface-variant-rgb));font-size:20px;">search</span>' +
            '    <input id="npc-cmdk-input" class="npc-cmdk-input" placeholder="Jump to… (type to search)" autocomplete="off" spellcheck="false">' +
            '    <span class="kbd">ESC</span>' +
            '  </div>' +
            '  <div id="npc-cmdk-list" class="npc-cmdk-list"></div>' +
            '</div>';
        document.body.appendChild(wrap);

        var list = wrap.querySelector('#npc-cmdk-list');
        var input = wrap.querySelector('#npc-cmdk-input');
        var active = 0;

        function render(filterText) {
            var q = (filterText || '').toLowerCase().trim();
            var items = commands.filter(function (c) {
                return !q || c.label.toLowerCase().indexOf(q) !== -1;
            });
            active = 0;
            if (!items.length) {
                list.innerHTML = '<div style="padding:1.2rem;text-align:center;color:rgb(var(--on-surface-variant-rgb));font-size:.85rem;">No matches found</div>';
                return;
            }
            list.innerHTML = items.map(function (c, i) {
                return '<button type="button" class="npc-cmdk-item' + (i === 0 ? ' active' : '') + '" data-href="' + c.href + '">' +
                    '<span class="material-symbols-outlined">' + c.icon + '</span><span>' + c.label + '</span>' +
                    '<span style="margin-left:auto;font-family:\'JetBrains Mono\',monospace;font-size:10px;color:rgb(var(--outline-rgb));">' + c.href.replace('.php', '') + '</span>' +
                    '</button>';
            }).join('');
            Array.prototype.forEach.call(list.children, function (btn, i) {
                btn.addEventListener('click', function () { location.href = btn.getAttribute('data-href'); });
                btn.addEventListener('pointerenter', function () { setActive(i); });
            });
        }

        function setActive(i) {
            active = i;
            Array.prototype.forEach.call(list.querySelectorAll('.npc-cmdk-item'), function (b, bi) {
                b.classList.toggle('active', bi === i);
            });
        }

        input.addEventListener('input', function () { render(input.value); });
        input.addEventListener('keydown', function (e) {
            var items = list.querySelectorAll('.npc-cmdk-item');
            if (e.key === 'ArrowDown') { e.preventDefault(); if (items.length) setActive(Math.min(active + 1, items.length - 1)); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); if (items.length) setActive(Math.max(active - 1, 0)); }
            else if (e.key === 'Enter') { e.preventDefault(); var it = items[active]; if (it) location.href = it.getAttribute('data-href'); }
            else if (e.key === 'Escape') { closePalette(); }
        });
        wrap.addEventListener('click', function (e) { if (e.target === wrap) closePalette(); });

        render('');
        setTimeout(function () { input.focus(); }, 30);
    }

    function closePalette() {
        var el = document.getElementById('npc-cmdk');
        if (el) el.remove();
    }
    window.npcClosePalette = closePalette;

    document.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
            e.preventDefault();
            openPalette();
        } else if (e.key === 'Escape') {
            closePalette();
        }
    });

    /* Add a subtle "Ctrl+K" hint chip into #topbar when present */
    function mountSearchHint() {
        var bar = document.getElementById('topbar');
        if (!bar || document.getElementById('npc-search-hint')) return;
        var clusters = bar.querySelectorAll(':scope > div');
        var target = clusters.length ? clusters[clusters.length - 1] : bar;
        var hint = document.createElement('button');
        hint.id = 'npc-search-hint';
        hint.type = 'button';
        hint.className = 'hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg border border-outline-variant bg-surface-container-low text-on-surface-variant text-xs font-semibold hover:bg-surface-container transition-colors cursor-pointer';
        hint.innerHTML = '<span class="material-symbols-outlined" style="font-size:15px;">search</span><span>Search</span><span class="kbd">Ctrl K</span>';
        hint.addEventListener('click', openPalette);
        target.insertBefore(hint, target.firstChild);
    }

    /* --------------------------------------------------------
       7b. SHARED CHROME — theme toggle, role badge, denied banner.
           Works on every portal page without touching markup.
       -------------------------------------------------------- */
    var ROLE_META = {
        student: { label: 'Student', icon: 'school', cls: 'bg-surface-container text-primary border-outline-variant' },
        teacher: { label: 'Faculty', icon: 'cast_for_education', cls: 'bg-secondary-container text-on-secondary-container border-secondary-container' },
        admin:   { label: 'Admin', icon: 'shield_person', cls: 'bg-error/10 text-error border-error/30' }
    };

    function mountThemeToggle() {
        if (document.getElementById('npc-theme-toggle') || document.getElementById('npc-theme-toggle-m')) return;

        var isDark = function () { return document.documentElement.classList.contains('dark'); };
        var btn = document.createElement('button');
        btn.id = 'npc-theme-toggle-m';
        btn.type = 'button';
        btn.setAttribute('data-tip', 'Toggle night mode');
        btn.setAttribute('aria-label', 'Toggle night mode');
        btn.className = 'press ripple';
        btn.innerHTML =
            '<span class="npc-theme-orb" style="width:38px;height:38px;display:inline-flex;align-items:center;justify-content:center;' +
            'border-radius:9999px;border:1px solid rgb(var(--outline-variant-rgb));' +
            'background:rgb(var(--surface-container-low-rgb));color:rgb(var(--on-surface-variant-rgb));cursor:pointer;' +
            'transition:transform .25s cubic-bezier(.34,1.56,.64,1);">' +
            '<span class="material-symbols-outlined" style="font-size:19px;">' + (isDark() ? 'light_mode' : 'dark_mode') + '</span></span>';

        function paint() {
            btn.querySelector('.material-symbols-outlined').textContent = isDark() ? 'light_mode' : 'dark_mode';
        }

        btn.addEventListener('click', function () {
            var html = document.documentElement;
            html.classList.add('theme-anim');
            html.classList.toggle('dark');
            try { localStorage.setItem('npc-theme', isDark() ? 'dark' : 'light'); } catch (e) {}
            paint();
            setTimeout(function () { html.classList.remove('theme-anim'); }, 500);
        });

        /* Strategy 1 — topbar cluster (student pages & some headers) */
        var bar = document.getElementById('topbar');
        var placed = false;
        if (bar) {
            var clusters = bar.querySelectorAll(':scope > div');
            var target = clusters.length ? clusters[clusters.length - 1] : bar;
            target.insertBefore(btn, target.firstChild);
            placed = true;
        }
        /* Strategy 2 — generic header with right-side flex group */
        if (!placed) {
            var hdrs = document.querySelectorAll('header');
            for (var i = 0; i < hdrs.length; i++) {
                var flexes = hdrs[i].querySelectorAll(':scope > div:last-child, :scope > div.flex.items-center.gap-3, :scope > div.flex.items-center.gap-4');
                if (flexes.length) {
                    flexes[flexes.length - 1].insertBefore(btn, flexes[flexes.length - 1].firstChild);
                    placed = true;
                    break;
                }
                if (hdrs[i].classList.contains('flex') && hdrs[i].className.indexOf('justify-between') !== -1) {
                    hdrs[i].appendChild(btn);
                    placed = true;
                    break;
                }
                /* bare header (no inner divs) */
                if (!hdrs[i].querySelector(':scope > div')) {
                    hdrs[i].appendChild(btn);
                    placed = true;
                    break;
                }
            }
        }
        if (!placed) return; /* nothing suitable on this page */
    }

    function mountRoleBadge() {
        var bar = document.getElementById('topbar');
        var metaEl = document.getElementById('npc-role-meta');
        if (!bar || !metaEl || bar.querySelector('.npc-role-chip')) return;
        var meta = {};
        try { meta = JSON.parse(metaEl.textContent || '{}'); } catch (e) {}
        var role = meta.role === 'teacher' ? 'teacher' : (meta.role === 'admin' ? 'admin' : 'student');
        var m = ROLE_META[role];
        var chip = document.createElement('span');
        chip.className = 'npc-role-chip hidden sm:inline-flex border ' + m.cls;
        chip.title = (meta.email || '') + ' — signed in via NPC Gmail SSO';
        chip.innerHTML = '<span class="material-symbols-outlined" style="font-size:13px;">' + m.icon + '</span>' + m.label;
        var clusters = bar.querySelectorAll(':scope > div');
        var target = clusters.length ? clusters[clusters.length - 1] : bar;
        target.insertBefore(chip, target.firstChild);
    }

    function mountDeniedBanner() {
        if (!document.getElementById('npc-denied-banner') || document.getElementById('npc-denied-banner-host')) return;
        var host = document.createElement('div');
        host.id = 'npc-denied-banner-host';
        host.style.cssText = 'position:relative;z-index:70;';
        host.style.width = '100%';
        fetch('_denied_banner.php').then(function (r) { return r.text(); }).then(function (html) {
            host.innerHTML = html;
            var main = document.querySelector('main') || document.body;
            main.insertBefore(host, main.firstChild);
        }).catch(function () {});
    }

    window.npcChrome = {
        refresh: function () { mountThemeToggle(); mountRoleBadge(); }
    };

    /* --------------------------------------------------------
       8. BOOTSTRAP on DOM ready
       -------------------------------------------------------- */
    /* --------------------------------------------------------
       7c. UNIVERSAL ENTRANCE CHOREOGRAPHY
           Staggers cards/sections/rows on every page load and on
           dynamic content injection. Skips chat bubbles & toasts.
       -------------------------------------------------------- */
    var __choreoBusy = false;
    function choreograph(root) {
        root = root || document;
        if (REDUCED || __choreoBusy) return;
        __choreoBusy = true;
        setTimeout(function () { __choreoBusy = false; }, 200);
                var sel = [
            'main section:not(.npc-choreo)', 'main > div > section:not(.npc-choreo)',
            '.grid > .npc-card:not(.npc-choreo)', 'section.grid > div:not(.npc-choreo)',
            'table tbody tr:not(.npc-choreo)', '.stagger > *:not(.npc-choreo)'
        ].join(',');
        /* skip delicate zones: excel grid, chat streams, toasts, modals */
        sel = sel.split(',').map(function (s) { return s + ':not(.no-choreo)'; }).join(',');
        var nodes = root.querySelectorAll(sel);
        var delay = 0;
        for (var i = 0; i < nodes.length && i < 24; i++) {
            (function (el, d) {
                el.classList.add('npc-choreo');
                el.style.opacity = '0';
                el.style.transform = 'translateY(14px)';
                setTimeout(function () {
                    el.style.transition = 'opacity .5s cubic-bezier(.22,.68,.32,1), transform .5s cubic-bezier(.22,.68,.32,1)';
                    el.style.opacity = '1';
                    el.style.transform = 'translateY(0)';
                }, 40 + d);
            })(nodes[i], delay);
            delay += 55;
        }
    }
    window.npcChoreograph = choreograph;

    /* --------------------------------------------------------
       7d. PORTAL SWITCHER — admins can jump roles from any page.
           Reads #npc-role-meta; renders a dropdown chip in topbar.
       -------------------------------------------------------- */
    function mountPortalSwitcher() {
        var metaEl = document.getElementById('npc-role-meta');
        if (!metaEl) return;
        var meta = {};
        try { meta = JSON.parse(metaEl.textContent || '{}'); } catch (e) {}
        if (meta.role !== 'admin') return;
        if (document.getElementById('npc-portal-switch')) return;

        var wrap = document.createElement('div');
        wrap.id = 'npc-portal-switch';
        wrap.className = 'relative hidden md:block';
        wrap.innerHTML =
            '<button type="button" id="npc-portal-switch-btn"' +
            ' class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-outline-variant' +
            ' bg-surface-container-low text-primary text-xs font-bold hover:bg-surface-container transition-colors cursor-pointer ripple press">' +
            '<span class="material-symbols-outlined" style="font-size:16px;">switch_account</span>' +
            '<span>View as…</span>' +
            '<span class="material-symbols-outlined" style="font-size:15px;">expand_more</span>' +
            '</button>' +
            '<div id="npc-portal-menu" class="npc-popover hidden" style="min-width:230px;">' +
            '<div class="npc-popover-arrow"></div>' +
            '<p class="px-4 pt-3 pb-1.5 font-mono text-[10px] uppercase tracking-widest text-on-surface-variant">Open portal</p>' +
            '<a href="index.php" class="npc-switch-item flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-on-surface hover:bg-surface-container-low transition-colors"><span class="material-symbols-outlined text-[18px] text-primary">school</span> Student Portal</a>' +
            '<a href="teacher.php" class="npc-switch-item flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-on-surface hover:bg-surface-container-low transition-colors"><span class="material-symbols-outlined text-[18px] text-secondary">cast_for_education</span> Faculty Portal</a>' +
            '<a href="admin.php" class="npc-switch-item flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-on-surface hover:bg-surface-container-low transition-colors"><span class="material-symbols-outlined text-[18px] text-error">shield_person</span> Admin Portal</a>' +
            '</div>';

        /* Place into the first header's right-side cluster (any layout) */
        var hdr = document.getElementById('topbar') || document.querySelector('header');
        if (!hdr) return;
        var clusters = hdr.querySelectorAll(':scope > div');
        var target = clusters.length ? clusters[clusters.length - 1] : hdr;
        target.insertBefore(wrap, target.firstChild);

        var btn = wrap.querySelector('#npc-portal-switch-btn');
        var menu = wrap.querySelector('#npc-portal-menu');
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            menu.classList.toggle('hidden');
        });
        document.addEventListener('click', function (e) {
            if (!menu.classList.contains('hidden') && !wrap.contains(e.target)) menu.classList.add('hidden');
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') menu.classList.add('hidden');
        });
    }

    /* --------------------------------------------------------
       7e. MOBILE DRAWER — shared sidebar works on phones too.
           Injects a hamburger button into the page header.
       -------------------------------------------------------- */
    function mountMobileDrawer() {
        if (document.getElementById('npc-mobile-menu-btn')) return;
        var sidebar = document.getElementById('npc-sidebar');
        var overlay = document.getElementById('npc-drawer-overlay');
        if (!sidebar) return;

        /* Make sidebar drawer-capable on small screens */
        sidebar.classList.add('npc-drawer');
        if (!document.getElementById('npc-drawer-style')) {
            var st = document.createElement('style');
            st.id = 'npc-drawer-style';
            st.textContent =
                '.npc-drawer{transform:translateX(-100%);transition:transform .3s cubic-bezier(.22,.68,.32,1);z-index:46}' +
                '@media(min-width:1024px){.npc-drawer{transform:none !important;}}' +
                '.npc-drawer.open{transform:translateX(0);}';
            document.head.appendChild(st);
        }

        /* Hamburger — place in first header found */
        var hdr = document.querySelector('header');
        if (!hdr) return;
        var btn = document.createElement('button');
        btn.id = 'npc-mobile-menu-btn';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Open menu');
        btn.className = 'lg:hidden p-2 rounded-xl border border-outline-variant bg-surface-container-low text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer press';
        btn.innerHTML = '<span class="material-symbols-outlined" style="font-size:20px;">menu</span>';
        btn.addEventListener('click', function () {
            sidebar.classList.add('open');
            if (overlay) overlay.classList.remove('hidden');
        });
        if (overlay) overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { sidebar.classList.remove('open'); if (overlay) overlay.classList.add('hidden'); }
        });
        hdr.insertBefore(btn, hdr.firstChild);
    }

    /* --------------------------------------------------------
       7f. UNIVERSAL NOTIFICATION CENTER
           Bell on every portal page. Feed = published announcements
           (api_notifications.php). Read/unread tracked per user
           in localStorage. Falls back gracefully if API offline.
       -------------------------------------------------------- */
    function mountNotificationCenter() {
        if (document.getElementById('npc-notif-center')) return;
        var hdr = document.getElementById('topbar') || document.querySelector('header');
        if (!hdr) return;

        var meta = {};
        var metaEl = document.getElementById('npc-role-meta');
        try { meta = JSON.parse((metaEl && metaEl.textContent) || '{}'); } catch (e) {}
        var userKey = 'npc-notif-read-' + (meta.email || 'anon');

        var wrap = document.createElement('div');
        wrap.id = 'npc-notif-center';
        wrap.className = 'relative';
        wrap.innerHTML =
            '<button type="button" id="npc-notif-btn" data-tip="Notifications" aria-label="Notifications"' +
            ' class="relative p-2 rounded-full border border-outline-variant bg-surface-container-low text-on-surface-variant hover:bg-surface-container transition-colors cursor-pointer press" style="overflow:visible;">' +
            '<span class="material-symbols-outlined" style="font-size:19px;">notifications</span>' +
            '<span id="npc-notif-badge" class="npc-badge-pill hidden">0</span>' +
            '</button>' +
            '<div id="npc-notif-panel" class="npc-popover hidden" style="min-width:340px;">' +
            '<div class="npc-popover-arrow"></div>' +
            '<div class="px-4 py-3 border-b border-outline-variant/60 flex items-center justify-between bg-surface-subtle">' +
            '<p class="text-sm font-bold text-primary">Notifications</p>' +
            '<button id="npc-notif-markall" class="text-[11px] font-mono font-bold text-on-surface-variant hover:text-primary transition-colors cursor-pointer">MARK ALL READ</button>' +
            '</div>' +
            '<div id="npc-notif-list" class="max-h-96 overflow-y-auto divide-y divide-outline-variant/40 custom-scroll">' +
            '<div class="p-5 text-center text-xs text-on-surface-variant animate-pulse">Loading…</div>' +
            '</div>' +
            '<div class="px-4 py-2 text-center bg-surface-subtle border-t border-outline-variant/60">' +
            '<span class="text-[10px] font-mono text-outline">Campus announcements feed</span>' +
            '</div></div>';

        /* place before the theme toggle cluster start */
        var clusters = hdr.querySelectorAll(':scope > div');
        var target = clusters.length ? clusters[clusters.length - 1] : hdr;
        target.insertBefore(wrap, target.firstChild);

        var btn = wrap.querySelector('#npc-notif-btn');
        var panel = wrap.querySelector('#npc-notif-panel');
        var badge = wrap.querySelector('#npc-notif-badge');
        var list = wrap.querySelector('#npc-notif-list');
        var ITEMS = [];
        var readMap = {};
        try { readMap = JSON.parse(localStorage.getItem(userKey) || '{}'); } catch (e) { readMap = {}; }

        function saveRead() {
            try { localStorage.setItem(userKey, JSON.stringify(readMap)); } catch (e) {}
        }
        function unreadCount() {
            var n = 0;
            ITEMS.forEach(function (it) { if (!readMap[it.id]) n++; });
            return n;
        }
        function paintBadge() {
            var n = unreadCount();
            if (n > 0) { 
                badge.textContent = n > 9 ? '9+' : String(n); 
                badge.classList.remove('hidden'); 
            } else {
                badge.classList.add('hidden');
            }
        }
        function timeAgo(iso) {
            var s = Math.floor((Date.now() - new Date(iso).getTime()) / 1000);
            if (isNaN(s)) return '';
            if (s < 60) return 'just now';
            if (s < 3600) return Math.floor(s / 60) + 'm ago';
            if (s < 86400) return Math.floor(s / 3600) + 'h ago';
            return Math.floor(s / 86400) + 'd ago';
        }
        function esc(s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; }

        function renderList() {
            if (!ITEMS.length) {
                list.innerHTML = '<div class="p-6 text-center flex flex-col items-center gap-2">' +
                    '<span class="material-symbols-outlined text-[30px] text-outline-variant">notifications_off</span>' +
                    '<p class="text-xs text-on-surface-variant">No announcements yet.</p></div>';
                return;
            }
            list.innerHTML = ITEMS.map(function (a) {
                var unread = !readMap[a.id];
                var icon = a.category === 'emergency' ? 'warning' : (a.category === 'academic' ? 'school' : 'campaign');
                var color = a.category === 'emergency' ? 'text-error' : (a.category === 'academic' ? 'text-secondary' : 'text-status-info');
                return '<div class="npc-notif-item flex items-start gap-3 px-4 py-3 hover:bg-surface-container-low/60 transition-colors cursor-pointer' + (unread ? ' bg-primary/5' : '') + '" data-id="' + esc(a.id) + '">' +
                    '<span class="material-symbols-outlined ' + color + ' text-[19px] mt-0.5 shrink-0">' + icon + '</span>' +
                    '<div class="min-w-0 flex-1 overflow-hidden">' +
                    '<p class="text-xs font-bold text-on-surface break-words leading-tight">' + (unread ? '<span class="inline-block w-1.5 h-1.5 rounded-full bg-error mr-1.5 align-middle"></span>' : '') + esc(a.title) + '</p>' +
                    '<p class="text-[11px] text-on-surface-variant break-words leading-snug mt-1" style="overflow-wrap:anywhere;">' + esc(a.excerpt) + '</p>' +
                    '<p class="text-[10px] font-mono text-outline mt-1">' + timeAgo(a.created_at) + '</p>' +
                    '</div></div>';
            }).join('');
            Array.prototype.forEach.call(list.querySelectorAll('.npc-notif-item'), function (el) {
                el.addEventListener('click', function () {
                    readMap[el.getAttribute('data-id')] = 1;
                    saveRead();
                    paintBadge();
                    el.classList.remove('bg-primary/5');
                    var dot = el.querySelector('.bg-error.rounded-full');
                    if (dot) dot.remove();
                });
            });
        }

        function load() {
            fetch('api_notifications.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    ITEMS = (d && d.notifications) || [];
                    renderList();
                    paintBadge();
                })
                .catch(function () {
                    list.innerHTML = '<div class="p-5 text-center text-xs text-on-surface-variant">Notifications unavailable right now.</div>';
                });
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            panel.classList.toggle('hidden');
            if (!panel.classList.contains('hidden')) { load(); }
        });
        wrap.querySelector('#npc-notif-markall').addEventListener('click', function () {
            ITEMS.forEach(function (it) { readMap[it.id] = 1; });
            saveRead();
            paintBadge();
            renderList();
        });
        document.addEventListener('click', function (e) {
            if (!panel.classList.contains('hidden') && !wrap.contains(e.target)) panel.classList.add('hidden');
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') panel.classList.add('hidden');
        });
        /* preload quietly */
        setTimeout(load, 800);
    }

    function boot() {
        runAnimations(document);
        document.querySelectorAll('.tilt').forEach(bindTilt);
        mountSearchHint();
        try { mountThemeToggle(); } catch (e) {}
        try { mountRoleBadge(); } catch (e) {}
        try { mountPortalSwitcher(); } catch (e) {}
        try { mountMobileDrawer(); } catch (e) {}
        try { mountNotificationCenter(); } catch (e) {}
        setTimeout(function () { choreograph(document); }, 30);
        /* NOTE: no MutationObserver here on purpose — grids/chats rebuild DOM
           constantly and re-animating causes flicker. Pages that render
           dynamic sections can call window.npcChoreograph() explicitly. */
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
