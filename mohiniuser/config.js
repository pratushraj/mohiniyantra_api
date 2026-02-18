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
                    id: game.code, // RM, RG, RY, RS
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
            <div class="custom-modal">
                <div class="custom-modal-header">
                    <span id="modalTitle">Notification</span>
                </div>
                <div class="custom-modal-body" id="modalMessage">
                    Message goes here...
                </div>
                <div class="custom-modal-footer">
                    <button class="modal-close-btn" onclick="closePopup()">OK</button>
                </div>
            </div>
        `;
        document.body.appendChild(overlay);
    }

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

            const dateSpans = document.querySelectorAll('.top-info span');
            dateSpans.forEach(span => {
                if (span.textContent.includes('Date :')) span.textContent = `Date : ${formatDate(window.appGameDetails.date)}`;
                if (span.textContent.includes('Gift Event Code :')) span.textContent = `Gift Event Code : ${window.appGameDetails.eventCode}`;
            });

            if (typeof updateDynamicUI === 'function') updateDynamicUI();
        }
    } catch (e) {
        console.error("Failed to fetch game details", e);
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
        if (res.ok && response.status === true) {
            showPopup(response.msg || success_msg, "Success");
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
            const balanceSpans = document.querySelectorAll('.top-info span');
            balanceSpans.forEach(span => {
                if (span.textContent.includes('Balance Point :')) span.textContent = `Balance Point : ${appGlobalConfig.balance}`;
            });
        }
    } catch (e) {
        console.error("Failed to fetch wallet balance", e);
    }
}

function startCountdown() {
    setInterval(() => {
        if (!window.appGameDetails || !window.appGameDetails.endTime) return;

        const now = new Date();
        const [hours, minutes, seconds] = window.appGameDetails.endTime.split(':');
        const target = new Date();
        target.setHours(hours, minutes, seconds, 0);

        let diff = target - now;

        // If target passed for today, target is next day? 
        // Based on current_game_details.php, it already handles date wrapping.
        // But if diff < 0 and the date is today, we might want to stay at 00:00:00 or handle it.
        if (diff < 0) diff = 0;

        const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
        const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
        const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');

        const countdownSpans = document.querySelectorAll('.top-info span');
        countdownSpans.forEach(span => {
            if (span.textContent.includes('Countdown :')) {
                span.textContent = `Countdown : ${h}:${m}:${s}`;
            }
        });
    }, 1000);
}

// Initialize live updates if logged in
if (appGlobalConfig.userId) {
    updateLiveHeader();
    updateLiveBalance();
    startCountdown();

    // Refresh header every 30s and balance every 3s
    setInterval(updateLiveHeader, 30000);
    setInterval(updateLiveBalance, 3000);
}

