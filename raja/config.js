// Centralized Configuration
const BASE_URL = "http://localhost/mohini/public_html/be";

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
    PURCHASE_SUMMARY_URL: `${BASE_URL}/user_purchase_summary.php`,
    WINNING_REPORT_URL: `${BASE_URL}/user_winning_report.php`
};

const SHARED_GROUPS = [
    { id: 'NV', name: 'Mohini Group NV', code: 'NV73', time: '09:30' },
    { id: 'RR', name: 'Mohini Group RR', code: 'RR17', time: '09:30' },
    { id: 'RY', name: 'Mohini Group RY', code: 'RY72', time: '09:30' },
    { id: 'CH', name: 'Mohini Group CH', code: 'CH42', time: '09:30' }
];

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
    balance: loginData.wallet_balance || '0.00',
    userId: loginData.id || ''
};

// Start logic
checkAuth();

function logout() {
    localStorage.removeItem('loginInfo');
    window.location.href = 'login.html';
}

function closePopup() {
    const overlay = document.getElementById('customOverlay');
    if (overlay) overlay.style.display = 'none';
}

