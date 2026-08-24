let currentUser = null;

// Initialize dynamic date string
function updateHeaderDate() {
    const dateEl = document.getElementById('current-date');
    if (dateEl) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        dateEl.innerText = new Date().toLocaleDateString('en-US', options);
    }
}
updateHeaderDate();

// Navigation & Routing logic
const navItems = document.querySelectorAll('.nav-item[data-target]');
const views = document.querySelectorAll('.view');
const sidebar = document.getElementById('sidebar');
const topbar = document.getElementById('topbar');
const pageTitle = document.getElementById('page-title');

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', () => {
    // API Configuration
    const API_BASE_URL = 'http://localhost:8000'; // Python FastAPI backend on port 8000
    
    // UI Elements
    navItems.forEach(item => {
        item.addEventListener('click', () => {
            const targetId = item.getAttribute('data-target');
            if (targetId) {
                navigateTo(targetId);
            }
        });
    });
});

function navigateTo(viewId) {
    views.forEach(view => view.classList.remove('active'));
    const targetView = document.getElementById(viewId);
    if (targetView) {
        targetView.classList.add('active');
    }

    navItems.forEach(item => {
        if (item.getAttribute('data-target') === viewId) {
            item.classList.add('bg-secondary-container', 'text-on-secondary-container');
            item.classList.remove('text-on-primary-fixed-variant');
            const textSpan = item.querySelector('span:not(.material-symbols-outlined)');
            if (pageTitle && textSpan) {
                pageTitle.innerText = textSpan.innerText;
            }
        } else {
            item.classList.remove('bg-secondary-container', 'text-on-secondary-container');
            item.classList.add('text-on-primary-fixed-variant');
        }
    });
}

// Authentication is now handled server-side via PHP.

// AI Question Prompt Chip Setter
function setQuestionPrompt(promptText) {
    const input = document.getElementById('chat-input');
    if (input) {
        input.value = promptText;
        sendMessage();
    }
}

// AI Chatbot Logic
async function sendMessage() {
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    if (!input || !messages) return;

    const text = input.value.trim();
    if (!text) return;

    // Append User Message
    messages.innerHTML += `
        <div class="flex flex-col items-end w-full">
            <div class="max-w-[85%] md:max-w-[70%] bg-primary-container text-on-primary-container p-4 rounded-xl rounded-tr-none shadow-sm">
                <p class="text-base">${escapeHtml(text)}</p>
            </div>
            <span class="font-mono text-xs font-medium text-on-surface-variant mt-1 mr-1">Now</span>
        </div>
    `;
    input.value = '';
    messages.scrollTop = messages.scrollHeight;

    // Append Loading State
    const loadingId = 'msg-' + Date.now();
    messages.innerHTML += `
        <div id="${loadingId}" class="flex items-start w-full opacity-70">
            <div class="flex gap-3">
                <div class="w-8 h-8 rounded-full bg-primary/20 flex items-center justify-center shrink-0 mt-1">
                    <span class="material-symbols-outlined text-primary" style="font-size: 18px;">smart_toy</span>
                </div>
                <div class="bg-surface-container-lowest border border-outline-variant px-4 py-3 rounded-xl rounded-tl-none shadow-sm flex items-center gap-1 typing-indicator h-10">
                    <span class="w-1.5 h-1.5 bg-on-surface-variant rounded-full block"></span>
                    <span class="w-1.5 h-1.5 bg-on-surface-variant rounded-full block"></span>
                    <span class="w-1.5 h-1.5 bg-on-surface-variant rounded-full block"></span>
                </div>
            </div>
        </div>
    `;
    messages.scrollTop = messages.scrollHeight;

    try {
        const response = await fetch('ask.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ question: text })
        });
        const data = await response.json();

        if (!response.ok) throw new Error(data.detail || 'Error getting response');

        const answerEl = document.getElementById(loadingId);
        if (answerEl) {
            let sourcesHtml = '';
            if (data.sources && data.sources.length > 0) {
                sourcesHtml = `
                    <div class="mt-2 pt-3 border-t border-outline-variant/60 flex items-start gap-2 bg-surface-subtle p-2 rounded-lg">
                        <span class="material-symbols-outlined text-status-info shrink-0" style="font-size: 16px;">library_books</span>
                        <div>
                            <p class="font-mono text-xs text-on-surface-variant font-semibold">Source References:</p>
                            ${data.sources.map((s, i) => `<a class="font-mono text-xs text-primary hover:underline block ${i > 0 ? '' : 'mt-0.5'}" href="#">${i + 1}. ${escapeHtml(s.file)} (score: ${s.score})</a>`).join('')}
                        </div>
                    </div>
                `;
            }

            answerEl.className = 'flex flex-col items-start w-full';
            answerEl.innerHTML = `
                <div class="flex gap-3 max-w-[90%] md:max-w-[80%]">
                    <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0 mt-1 shadow-sm">
                        <span class="material-symbols-outlined text-on-primary" style="font-size: 18px;">smart_toy</span>
                    </div>
                    <div class="bg-surface-container-lowest border border-outline-variant p-4 rounded-xl rounded-tl-none shadow-sm flex flex-col gap-3">
                        <div class="text-base text-on-surface space-y-3" style="white-space: pre-wrap;">${escapeHtml(data.answer)}</div>
                        ${sourcesHtml}
                    </div>
                </div>
                <span class="font-mono text-xs font-medium text-on-surface-variant mt-1 ml-11">Now</span>
            `;
        }
    } catch (err) {
        const answerEl = document.getElementById(loadingId);
        if (answerEl) {
            answerEl.className = 'flex flex-col items-start w-full';
            answerEl.innerHTML = `
                <div class="flex gap-3 max-w-[90%] md:max-w-[80%]">
                    <div class="w-8 h-8 rounded-full bg-error flex items-center justify-center shrink-0 mt-1 shadow-sm">
                        <span class="material-symbols-outlined text-on-error" style="font-size: 18px;">error</span>
                    </div>
                    <div class="bg-error-container border border-error/30 p-4 rounded-xl rounded-tl-none shadow-sm flex flex-col gap-3">
                        <div class="text-base text-error" style="white-space: pre-wrap;">${escapeHtml(err.message)}</div>
                    </div>
                </div>
                <span class="font-mono text-xs font-medium text-on-surface-variant mt-1 ml-11">Now</span>
            `;
        }
    }
    messages.scrollTop = messages.scrollHeight;
}

const chatInput = document.getElementById('chat-input');
if (chatInput) {
    chatInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    chatInput.addEventListener('input', function () {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight < 120 ? this.scrollHeight : 120) + 'px';
    });
}

// Admin Document Listing & Upload Logic
async function loadDocuments() {
    try {
        const response = await fetch('http://localhost:8000/documents');
        const data = await response.json();
        const list = document.getElementById('document-list');
        if (!list) return;

        list.innerHTML = '';

        if (data.documents && data.documents.length > 0) {
            data.documents.forEach(doc => {
                const date = new Date(doc.modified).toLocaleString();
                const size = (doc.size / 1024).toFixed(2) + ' KB';
                list.innerHTML += `
                    <tr class="hover:bg-surface-container-low transition-colors">
                        <td class="p-4 font-medium text-primary flex items-center gap-2">
                            <span class="material-symbols-outlined text-on-surface-variant">description</span>
                            <span>${escapeHtml(doc.name)}</span>
                        </td>
                        <td class="p-4 font-mono text-sm">${size}</td>
                        <td class="p-4 text-sm text-on-surface-variant">${date}</td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded bg-success-container text-success font-mono text-xs font-semibold">
                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                <span>Indexed</span>
                            </span>
                        </td>
                    </tr>
                `;
            });
        } else {
            list.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center text-on-surface-variant p-6 font-body-md">
                        No documents currently indexed in knowledge base.
                    </td>
                </tr>
            `;
        }
    } catch (err) {
        console.error('Failed to load documents', err);
    }
}

async function uploadDocument() {
    const input = document.getElementById('document-upload');
    const status = document.getElementById('upload-status');
    if (!input || !status) return;

    if (!input.files.length) {
        status.innerText = 'Please select a file first.';
        status.className = 'mt-3 font-label-md text-label-md text-error';
        return;
    }

    const file = input.files[0];
    const formData = new FormData();
    formData.append('file', file);

    status.innerText = 'Uploading and indexing knowledge base...';
    status.className = 'mt-3 font-label-md text-label-md text-primary';

    try {
        const response = await fetch('http://localhost:8000/upload', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (!response.ok) throw new Error(data.detail || 'Upload failed');

        status.innerText = `✓ Successfully uploaded and indexed ${data.filename}`;
        status.className = 'mt-3 font-label-md text-label-md text-success';
        input.value = '';
        loadDocuments();
    } catch (err) {
        status.innerText = 'Error: ' + err.message;
        status.className = 'mt-3 font-label-md text-label-md text-error';
    }
}

const adminNavBtn = document.getElementById('nav-admin-doc');
if (adminNavBtn) {
    adminNavBtn.addEventListener('click', loadDocuments);
}

// Attendance Kiosk Logic
async function checkIn() {
    const input = document.getElementById('kiosk-id');
    const status = document.getElementById('kiosk-status');
    if (!input || !status) return;

    const id = input.value.trim();
    if (!id) return;

    status.innerText = 'Verifying Student ID and logging attendance...';
    status.className = 'mt-6 font-label-md text-label-md text-on-surface-variant';

    try {
        const response = await fetch('http://localhost:8000/api/attendance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ student_id: id, method: 'kiosk' })
        });
        const data = await response.json();
        
        if (data.success) {
            status.innerText = `✓ Student ${id} checked in successfully at ${new Date().toLocaleTimeString()}`;
            status.className = 'mt-6 font-label-md text-label-md text-success font-semibold';
            input.value = '';
        } else {
            status.innerText = `Error: ${data.message || 'Failed to check in'}`;
            status.className = 'mt-6 font-label-md text-label-md text-error font-semibold';
        }
    } catch(err) {
        status.innerText = `Error connecting to server.`;
        status.className = 'mt-6 font-label-md text-label-md text-error font-semibold';
    }
}

const kioskIdEl = document.getElementById('kiosk-id');
if (kioskIdEl) {
    kioskIdEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') checkIn();
    });
}

// Utility function to prevent XSS
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


