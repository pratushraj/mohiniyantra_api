// resultpublish/config.js

// Centralized Configuration for Public Results
const BASE_URL = "https://apiyantra.rajaranimohiniyantra.com/be";

const CONFIG = {
    BASE_URL: BASE_URL,
    APP_NAME: "Golden Navratna Coupon",

    // API Endpoints
    GIFT_EVENT_CODE_URL: `${BASE_URL}/gift_events_code.php`,
    GAME_DETAILS_URL: `${BASE_URL}/current_game_details.php`,

    // UI Helpers
    DATE_FORMAT: 'd-m-Y'
};

/**
 * Global helper to format date
 * @param {Date|string} date 
 * @returns {string} formatted date string
 */
function formatDate(date) {
    if (!date) return '---';
    const d = (date instanceof Date) ? date : new Date(date);
    if (isNaN(d.getTime())) return date;

    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();

    return CONFIG.DATE_FORMAT
        .replace('d', day)
        .replace('m', month)
        .replace('Y', year);
}
