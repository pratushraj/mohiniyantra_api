// Centralized Configuration
const BASE_URL = "https://apiyantra.rajaranimohiniyantra.com/be";

const CONFIG = {
    BASE_URL: BASE_URL,
    APP_NAME: "Mohini Group",
    LOGIN_URL: `${BASE_URL}/login.php`,
    WALLET_BALANCE_URL: `${BASE_URL}/wallet-balance.php`,
    GIFT_EVENT_CODE_URL: `${BASE_URL}/gift_events_code.php`,
    TICKET_PRICE_URL: `${BASE_URL}/ticket-price.php`,
    LOGOUT_URL: `${BASE_URL}/logout.php`,
    UPCOMING_EVENTS_URL: `${BASE_URL}/upcoming_events.php`,
    WALLET_REQUEST_URL: `${BASE_URL}/wallet_request.php`,
    CURRENT_GAME_DETAILS_URL: `${BASE_URL}/current_game_details.php`,
    TICKET_PURCHASE_URL: `${BASE_URL}/purchase-tickets.php`,
    CANCEL_TICKETS_URL: `${BASE_URL}/cancel-tickets.php`,
    CHANGE_PASSWORD_URL: `${BASE_URL}/change-password.php`,
    PURCHASE_SUMMARY_URL: `${BASE_URL}/purchase-summary.php`,
    WINNING_REPORT_URL: `${BASE_URL}/user_winning_report.php`,
    FETCH_GAMES_URL: `${BASE_URL}/fetch_games.php`,
    TICKET_DETAILS_URL: `${BASE_URL}/ticket-details.php`,
    DATE_FORMAT: 'd-m-Y'
};

function formatDate(date) {
    if (!date) return '---';
    const d = (date instanceof Date) ? date : new Date(date);
    if (isNaN(d.getTime())) return date; // Return as is if invalid

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();

    return CONFIG.DATE_FORMAT
        .replace('d', day)
        .replace('m', month)
        .replace('Y', year);
}

const SHARED_GROUPS = [
    { id: 'RM', name: 'Mohini Group RM', code: 'RM73', time: '09:30' },
    { id: 'RG', name: 'Mohini Group RG', code: 'RG17', time: '09:30' },
    { id: 'RY', name: 'Mohini Group RY', code: 'RY72', time: '09:30' },
    { id: 'RS', name: 'Mohini Group RS', code: 'RS42', time: '09:30' }
];

async function syncSharedGroups() {
    try {
        const res = await fetch(CONFIG.FETCH_GAMES_URL);
        const json = await res.json();
        if (json.status && json.data.length > 0) {
            // Update array in-place to maintain references in appConfig
            SHARED_GROUPS.length = 0;
            json.data.forEach(game => {
                SHARED_GROUPS.push({
                    id: game.id, // Database game_type_id
                    name: game.name,
                    code: game.code,
                    time: '09:30'
                });
            });

            // Refresh UI components on the current page
            if (typeof initUI === 'function') initUI();
            if (typeof updateDynamicUI === 'function') updateDynamicUI();
        }
    } catch (e) {
        console.error("Failed to sync shared groups", e);
    }
}

// Start sync immediately
syncSharedGroups();


// Custom Popup Logic
function showPopup(message, title = 'Notification') {
    let overlay = document.getElementById('customOverlay');

    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'customOverlay';
        overlay.className = 'custom-modal-overlay';
        overlay.innerHTML = `
            <div class="custom-modal" id="customModalBox">
                <div class="custom-modal-header" id="customModalHeader">
                    <span class="modal-icon" id="modalIcon"></span>
                    <span id="modalTitle">Notification</span>
                </div>
                <div class="custom-modal-body" id="modalMessage">
                    Message goes here...
                </div>
                <div class="custom-modal-footer">
                    <button class="modal-close-btn" id="modalCloseBtn" onclick="closePopup()">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

    // Color-code by title
    const titleLower = title.toLowerCase();
    let headerBg, btnBg, btnShadow, icon;

    if (titleLower.includes('success')) {
        headerBg = 'linear-gradient(135deg, #1a9e4a, #16834a)';
        btnBg = 'linear-gradient(135deg, #1a9e4a, #16834a)';
        btnShadow = 'rgba(22, 131, 74, 0.45)';
        icon = '✅';
    } else if (titleLower.includes('error') || titleLower.includes('connection')) {
        headerBg = 'linear-gradient(135deg, #c0392b, #962d22)';
        btnBg = 'linear-gradient(135deg, #c0392b, #962d22)';
        btnShadow = 'rgba(192, 57, 43, 0.45)';
        icon = '❌';
    } else if (titleLower.includes('warning') || titleLower.includes('validation')) {
        headerBg = 'linear-gradient(135deg, #e67e22, #ca6f1e)';
        btnBg = 'linear-gradient(135deg, #e67e22, #ca6f1e)';
        btnShadow = 'rgba(230, 126, 34, 0.45)';
        icon = '⚠️';
    } else {
        headerBg = 'linear-gradient(135deg, #000165, #1a1a7a)';
        btnBg = 'linear-gradient(135deg, #000165, #1a1a7a)';
        btnShadow = 'rgba(0, 1, 102, 0.4)';
        icon = 'ℹ️';
    }

    const header = document.getElementById('customModalHeader');
    const closeBtn = document.getElementById('modalCloseBtn');
    if (header) header.style.background = headerBg;
    if (closeBtn) {
        closeBtn.style.background = btnBg;
        closeBtn.style.boxShadow = `0 4px 14px ${btnShadow}`;
    }

    document.getElementById('modalIcon').textContent = icon;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    overlay.style.display = 'flex';
}

// Auth Guard Logic
function checkAuth() {
    const isLoginPage = window.location.href.toLowerCase().includes('login.html');
    const rawLoginData = localStorage.getItem('loginInfo');

    let hasData = false;
    try {
        if (rawLoginData) {
            const parsed = JSON.parse(rawLoginData);
            if (parsed && parsed.id) {
                hasData = true;
            }
        }
    } catch (e) {
        hasData = false;
    }

    // A: No valid login but trying to access protected page
    if (!hasData && !isLoginPage) {
        window.location.href = 'login.html';
        return;
    }

    // B: Already logged in but trying to access login page
    if (hasData && isLoginPage) {
        window.location.href = 'index.html';
        return;
    }
}

// Global App Config derived from session
const rawSession = localStorage.getItem('loginInfo');
let loginData = {};
try {
    loginData = JSON.parse(rawSession || '{}') || {};
} catch (e) { }

const appGlobalConfig = {
    appName: localStorage.getItem('appName') || "Mohini Group",
    userName: loginData.name || 'User',
    unique_id: loginData.unique_id || 'UserID',
    balance: loginData.wallet_balance || '0.00',
    userId: loginData.id || '',
    userCache: loginData.cache || ''
};

// Start logic
checkAuth();

function logout() {
    setCookie('cache', appGlobalConfig.userCache, 3650); // Store for 10 years (lifetime)
    localStorage.removeItem('loginInfo');
    window.location.href = 'login.html';
}

function setCookie(name, value, days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') c = c.substring(1, c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function closePopup() {
    const overlay = document.getElementById('customOverlay');
    if (overlay) overlay.style.display = 'none';
}

// Live Data Implementation
window.appGameDetails = {
    date: '---',
    eventCode: '---',
    endTime: null,
    timeSlotId: null,
    prev_game_results: []
};

async function updateLiveHeader() {
    try {
        const res = await fetch(CONFIG.CURRENT_GAME_DETAILS_URL);
        const json = await res.json();
        if (json.status) {
            window.appGameDetails.date = json.data.date;
            window.appGameDetails.endTime = json.data.end_time;
            window.appGameDetails.timeSlotId = json.data.timeSlotId;
            window.appGameDetails.eventCode = json.data.end_time.substring(0, 5);
            window.appGameDetails.prev_game_results = json.data.prev_game_results || [];

            const dateSpans = document.querySelectorAll('span');
            dateSpans.forEach(span => {
                if (span.textContent.includes('Date :')) span.textContent = `Date : ${formatDate(window.appGameDetails.date)}`;
                if (span.textContent.includes('Gift Event Code :')) span.textContent = `Gift Event Code : ${window.appGameDetails.eventCode}`;
            });

            if (typeof updateDynamicUI === 'function') updateDynamicUI();
            if (typeof updateCountdownUI === 'function') updateCountdownUI();

            updateUpcomingBadge();
        }
    } catch (e) {
        console.error("Failed to fetch game details", e);
    }
}

async function updateUpcomingBadge() {
    if (!appGlobalConfig.userId) return;
    try {
        const date = window.appGameDetails.date !== '---' ? window.appGameDetails.date : new Date().toISOString().split('T')[0];

        // Parallel fetch for speed
        const [selRes, upRes] = await Promise.all([
            fetch(`${CONFIG.BASE_URL}/get_selected_draws.php?userId=${appGlobalConfig.userId}&date=${date}`),
            fetch(CONFIG.UPCOMING_EVENTS_URL)
        ]);

        const selJson = await selRes.json();
        const upJson = await upRes.json();

        if (selJson.status && upJson.status) {
            const selectedIds = selJson.data.map(id => id.toString());
            const upcomingIds = upJson.data.map(item => item.time_slot_id.toString());

            // Count how many of the user's selected draws are still in the upcoming list
            const activeCount = selectedIds.filter(id => upcomingIds.includes(id)).length;

            const badge = document.getElementById('upcomingBadge');
            if (badge) {
                badge.textContent = activeCount;
                badge.style.display = activeCount > 0 ? 'flex' : 'none';
            }
        } else {
            const badge = document.getElementById('upcomingBadge');
            if (badge) badge.style.display = 'none';
        }
    } catch (e) {
        console.error("Badge update failed", e);
        const badge = document.getElementById('upcomingBadge');
        if (badge) badge.style.display = 'none';
    }
}

async function postData(url, data, success_msg) {
    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const response = await res.json();
        if (res.ok && response.status == true) {
            // Force use our message if provided
            let finalMsg = "Buy Successful";
            if (success_msg) {
                finalMsg = success_msg;
            } else if (response.msg) {
                finalMsg = response.msg;
            }
            showPopup(finalMsg, "Success");
            return response;
        } else {
            showPopup(response.msg || "Transaction failed", "Error");
            return null;
        }
    } catch (error) {
        console.error("Post Error:", error);
        showPopup("Network error or server unavailable", "Connection Error");
        return null;
    }
}

async function updateLiveBalance() {
    if (!appGlobalConfig.userId) return;
    try {
        const res = await fetch(CONFIG.WALLET_BALANCE_URL, {
            method: 'POST',
            body: JSON.stringify({ userId: appGlobalConfig.userId })
        });
        const json = await res.json();
        if (json.status) {
            appGlobalConfig.balance = json.data;
            // Update all balance spans
            const balanceSpans = document.querySelectorAll('span');
            balanceSpans.forEach(span => {
                if (span.textContent.includes('Balance Point :')) span.textContent = `Balance Point : ${appGlobalConfig.balance}`;
            });
        }
    } catch (e) {
        console.error("Failed to fetch wallet balance", e);
    }
}

let isFetchingResult = false;
function updateCountdownUI() {
    if (!window.appGameDetails || !window.appGameDetails.endTime || window.appGameDetails.date === '---') return;

    const now = new Date();

    // Parse the date and time from the server response
    const [y, month, d] = window.appGameDetails.date.split('-').map(Number);
    const [hours, minutes, seconds] = window.appGameDetails.endTime.split(':').map(Number);

    // Create target date in local time
    const target = new Date(y, month - 1, d, hours, minutes, seconds, 0);

    let diff = target - now;

    // If diff is negative but very small (e.g. less than 1 min), treat as 0
    // If diff is very large negative, it might be the next day's first slot, handled by server returning correct end_time
    if (diff < 0) diff = 0;

    // Trigger auto-publish logic when countdown hits 0
    if (diff === 0 && !isFetchingResult) {
        const currentEndTime = window.appGameDetails.endTime;
        isFetchingResult = true;

        // Poll for result every 1s for 5 seconds to ensure "automatic publish within 5s"
        let fetchCount = 0;
        const fetchInterval = setInterval(async () => {
            await updateLiveHeader();
            fetchCount++;
            // If the end time has changed, it means we moved to the next slot
            if (window.appGameDetails.endTime !== currentEndTime || fetchCount >= 6) {
                clearInterval(fetchInterval);
                isFetchingResult = false;
            }
        }, 1000);
    }

    const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
    const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
    const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');

    // Urgent state: 5 seconds or less left
    const isUrgent = diff <= 5100 && diff > 0;

    const countdownElements = document.querySelectorAll('#countdown-timer, .countdown-timer');
    const allSpans = document.querySelectorAll('span');

    // Helper to update element
    const updateEl = (el) => {
        el.textContent = `Countdown : ${h}:${m}:${s}`;
        if (isUrgent) el.classList.add('countdown-urgent');
        else el.classList.remove('countdown-urgent');
    };

    countdownElements.forEach(updateEl);
    allSpans.forEach(span => {
        if (span.textContent.includes('Countdown :')) updateEl(span);
    });
}

function startCountdown() {
    updateCountdownUI(); // Initial call
    setInterval(updateCountdownUI, 1000);
}

// Initialize live updates if logged in
if (appGlobalConfig.userId) {
    document.addEventListener('DOMContentLoaded', () => {
        updateLiveHeader();
        updateLiveBalance();
        startCountdown();

        // Refresh header every 30s and balance every 3s
        setInterval(updateLiveHeader, 30000);
        setInterval(updateLiveBalance, 3000);
    });
}

