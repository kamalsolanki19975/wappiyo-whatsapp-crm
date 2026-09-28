/**
 * Centralized Date & Time Utility for Wappiyo
 *
 * Defaults to India Standard Time (Asia/Kolkata) while dynamically respecting
 * the active organization's configured IANA timezone and user settings.
 */

export const DEFAULT_TIMEZONE = 'Asia/Kolkata';
export const DEFAULT_TIMEZONE_DISPLAY = 'India Standard Time (IST, UTC+05:30)';

/**
 * Safely resolve active timezone from options, Inertia page props, or fallback to Asia/Kolkata.
 */
export function getActiveTimezone(explicitTz = null) {
    if (explicitTz && typeof explicitTz === 'string' && explicitTz.trim()) {
        return explicitTz.trim();
    }

    try {
        // Attempt retrieval from global Inertia page state if available
        if (typeof window !== 'undefined' && window.__page?.props?.timezone) {
            return window.__page.props.timezone;
        }
    } catch (e) {
        // Ignore and fall back
    }

    return DEFAULT_TIMEZONE;
}

/**
 * Parse input into a valid Date object.
 */
export function parseDate(dateInput) {
    if (!dateInput) return null;
    if (dateInput instanceof Date) return isNaN(dateInput.getTime()) ? null : dateInput;

    const d = new Date(dateInput);
    return isNaN(d.getTime()) ? null : d;
}

/**
 * Format date and time (e.g., '27 Sep 2026, 09:30 PM').
 */
export function formatDateTime(dateInput, options = {}, tz = null) {
    const d = parseDate(dateInput);
    if (!d) return '';

    const timeZone = getActiveTimezone(tz);

    const defaultOptions = {
        timeZone,
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        ...options,
    };

    try {
        return new Intl.DateTimeFormat('en-US', defaultOptions).format(d);
    } catch (e) {
        // Fallback with default timezone if custom timezone was invalid
        return new Intl.DateTimeFormat('en-US', { ...defaultOptions, timeZone: DEFAULT_TIMEZONE }).format(d);
    }
}

/**
 * Format date only (e.g., '27 Sep 2026' or '27/09/2026').
 */
export function formatDate(dateInput, options = {}, tz = null) {
    const d = parseDate(dateInput);
    if (!d) return '';

    const timeZone = getActiveTimezone(tz);

    const defaultOptions = {
        timeZone,
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        ...options,
    };

    try {
        return new Intl.DateTimeFormat('en-US', defaultOptions).format(d);
    } catch (e) {
        return new Intl.DateTimeFormat('en-US', { ...defaultOptions, timeZone: DEFAULT_TIMEZONE }).format(d);
    }
}

/**
 * Format time only (e.g., '09:30 PM').
 */
export function formatTime(dateInput, options = {}, tz = null) {
    const d = parseDate(dateInput);
    if (!d) return '';

    const timeZone = getActiveTimezone(tz);

    const defaultOptions = {
        timeZone,
        hour: '2-digit',
        minute: '2-digit',
        hour12: true,
        ...options,
    };

    try {
        return new Intl.DateTimeFormat('en-US', defaultOptions).format(d);
    } catch (e) {
        return new Intl.DateTimeFormat('en-US', { ...defaultOptions, timeZone: DEFAULT_TIMEZONE }).format(d);
    }
}

/**
 * Format date with timezone suffix (e.g., '27 Sep 2026, 09:30 PM IST').
 */
export function formatWithTimezoneName(dateInput, tz = null) {
    return formatDateTime(dateInput, { timeZoneName: 'short' }, tz);
}

/**
 * Human-friendly relative time string with local timezone awareness.
 */
export function formatTimeAgo(dateInput) {
    const d = parseDate(dateInput);
    if (!d) return '';

    const now = new Date();
    const diffSeconds = Math.floor((now - d) / 1000);

    if (diffSeconds < 45) return 'Just now';
    if (diffSeconds < 90) return '1m ago';
    if (diffSeconds < 3600) return `${Math.floor(diffSeconds / 60)}m ago`;
    if (diffSeconds < 86400) return `${Math.floor(diffSeconds / 3600)}h ago`;
    if (diffSeconds < 172800) return 'Yesterday';
    if (diffSeconds < 604800) return `${Math.floor(diffSeconds / 86400)}d ago`;

    return formatDate(d);
}

export default {
    DEFAULT_TIMEZONE,
    DEFAULT_TIMEZONE_DISPLAY,
    getActiveTimezone,
    parseDate,
    formatDateTime,
    formatDate,
    formatTime,
    formatWithTimezoneName,
    formatTimeAgo,
};
