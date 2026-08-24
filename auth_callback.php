<?php
/**
 * auth_callback.php — OAuth Callback Handler
 *
 * After Google OAuth via Supabase, this page:
 *  1. Captures the Supabase session (client-side, after redirect)
 *     - Supports BOTH implicit (#access_token) and PKCE (?code) flows
 *  2. Sends ONLY the access_token to set_session.php (server-side verification)
 *  3. Redirects to the appropriate portal based on the server-verified role
 *
 * NO role determination happens here. The server decides everything.
 */
require_once __DIR__ . '/supabase_helper.php';
$jsConfig = getJsConfig();
?>
<!DOCTYPE html>
<html lang="en" class="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authenticating... - NPC Connect</title>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
</head>
<body style="font-family: 'Geist', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #f8f9ff; margin: 0;">
    <div id="status-box" style="text-align: center; max-width: 440px; padding: 32px; background: white; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
        <div id="spinner" style="width: 44px; height: 44px; border: 4px solid #e2e8f0; border-top: 4px solid #001736; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
        <h2 id="status-title" style="color: #001736; margin-bottom: 8px; font-size: 20px;">Authenticating...</h2>
        <p id="status-msg" style="color: #64748b; font-size: 14px; margin: 0;">Verifying your credentials securely.</p>
        <button id="retry-btn" onclick="window.location.href='/login.php'" style="display:none; margin-top:20px; padding:10px 22px; background:#001736; color:#fff; border:none; border-radius:10px; font-weight:600; cursor:pointer; font-size:14px;">
            Back to Sign In
        </button>
        <p id="err-detail" style="display:none; margin-top:14px; font-family:'JetBrains Mono',monospace; font-size:11px; color:#ba1a1a; background:#ffdad6; border-radius:8px; padding:8px 10px; word-break:break-word; text-align:left;"></p>
    </div>

    <style>
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>

    <script>
        // Supabase config injected server-side (publishable key only)
        const supabaseUrl = <?= json_encode($jsConfig['url']) ?>;
        const supabaseKey = <?= json_encode($jsConfig['key']) ?>;
        const supabaseClient = supabase.createClient(supabaseUrl, supabaseKey);

        const statusTitle = document.getElementById('status-title');
        const statusMsg   = document.getElementById('status-msg');
        const spinner     = document.getElementById('spinner');
        const retryBtn    = document.getElementById('retry-btn');
        const errDetail   = document.getElementById('err-detail');

        function setStatus(title, msg, isError = false, detail = '') {
            if (statusTitle) statusTitle.innerText = title;
            if (statusMsg) statusMsg.innerText = msg;
            if (isError) {
                if (spinner) spinner.style.display = 'none';
                if (statusTitle) statusTitle.style.color = '#ba1a1a';
                if (retryBtn) retryBtn.style.display = 'inline-block';
                if (detail && errDetail) {
                    errDetail.style.display = 'block';
                    errDetail.textContent = detail;
                }
                console.error('[NPC Auth]', title, '—', msg, detail || '');
            }
        }

        let isProcessing = false;

        async function finishLogin(accessToken) {
            if (isProcessing) return;
            isProcessing = true;
            try {
                setStatus('Verifying credentials...', 'Setting up your secure session...');
                const res = await fetch('/set_session.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ access_token: accessToken })
                });
                const result = await res.json();

                if (res.ok && result.success) {
                    if (result.csrf_token) sessionStorage.setItem('csrf_token', result.csrf_token);
                    setStatus('Welcome!', 'Redirecting to your portal...');
                    const role = result.role || 'student';
                    setTimeout(() => {
                        window.location.href = role === 'admin' ? '/admin.php'
                                              : role === 'teacher' ? '/teacher.php'
                                              : '/index.php';
                    }, 600);
                } else {
                    throw new Error(result.message || 'Server rejected the authentication.');
                }
            } catch (err) {
                setStatus('Login Failed', err.message || 'An unexpected error occurred.', true);
            }
        }

        /* ── Fallback A: implicit flow (#access_token=… in the URL fragment) ── */
        function tryImplicitToken() {
            const h = window.location.hash || '';
            if (!h.includes('access_token=')) return null;
            const params = new URLSearchParams(h.substring(1));
            const err = params.get('error');
            if (err) {
                setStatus('Sign-In Error', decodeURIComponent(params.get('error_description') || err), true,
                          'provider=' + err);
                return 'handled';
            }
            const at = params.get('access_token');
            return at || null;
        }

        /* ── Fallback B: surface ?error=… query errors (Google/Supabase bounce-backs) ── */
        function tryQueryError() {
            const q = new URLSearchParams(window.location.search);
            if (q.get('error')) {
                setStatus('Sign-In Error', q.get('error_description') || q.get('error'), true,
                          'code=' + q.get('error_code', ''));
                return true;
            }
            return false;
        }

        async function processSession(session) {
            if (isProcessing) return;
            isProcessing = true;
            try {
                if (!session || !session.access_token) {
                    throw new Error('No valid session returned from authentication.');
                }

                const userEmail = session.user?.email?.toLowerCase() || '';

                // Quick client-side domain check (server will re-verify)
                if (!userEmail.endsWith('@navotaspolytechniccollege.edu.ph')) {
                    setStatus('Access Denied', 'Only official @navotaspolytechniccollege.edu.ph accounts are allowed.', true);
                    await supabaseClient.auth.signOut();
                    setTimeout(() => { window.location.href = '/login.php'; }, 3000);
                    return;
                }

                await finishLogin(session.access_token);
            } catch (err) {
                console.error('Auth callback error:', err);
                setStatus('Login Error', err.message || 'An unexpected error occurred.', true);
                setTimeout(() => { window.location.href = '/login.php'; }, 3500);
            }
        }

        async function initAuth() {
            // Surface provider-level errors FIRST (wrong account, blocked, etc.)
            if (tryQueryError()) return;

            // Implicit-flow tokens arrive in the #fragment
            const implicit = tryImplicitToken();
            if (implicit === 'handled') return;
            if (implicit) { await finishLogin(implicit); return; }

            // Standard supabase-js handling (covers PKCE ?code=… exchange too)
            supabaseClient.auth.onAuthStateChange(async (event, session) => {
                if (session) await processSession(session);
                else if (event === 'SIGNED_OUT') {
                    /* noop */
                }
            });

            const { data, error } = await supabaseClient.auth.getSession();
            if (error) {
                setStatus('Auth Error', error.message, true);
                return;
            }

            if (data?.session) {
                await processSession(data.session);
            } else {
                // Give PKCE exchange & slow networks ample time before declaring failure
                setTimeout(() => {
                    if (!isProcessing) {
                        setStatus(
                            'Sign-in did not complete',
                            'No active session was returned. This usually means the app URL you are browsing (see address bar) is not added to the Supabase redirect allow-list, or third-party cookies are blocked.',
                            true,
                            'TIP: try opening the site via http://localhost instead of http://127.0.0.1 (or vice-versa), then sign in again.'
                        );
                    }
                }, 12000);
            }
        }

        initAuth();
    </script>
</body>
</html>
