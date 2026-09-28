/**
 * Comprehensive international country list with dial codes, ISO codes, and emoji flags.
 * India (IN, +91) is prioritized at index 0 as the primary default.
 */
export const defaultCountry = {
    name: 'India',
    code: 'IN',
    dialCode: '+91',
    flag: '🇮🇳',
    format: '##### #####',
    digits: 10,
};

export const countries = [
    defaultCountry,
    { name: 'United States', code: 'US', dialCode: '+1', flag: '🇺🇸', digits: 10 },
    { name: 'United Kingdom', code: 'GB', dialCode: '+44', flag: '🇬🇧', digits: 10 },
    { name: 'United Arab Emirates', code: 'AE', dialCode: '+971', flag: '🇦🇪', digits: 9 },
    { name: 'Saudi Arabia', code: 'SA', dialCode: '+966', flag: '🇸🇦', digits: 9 },
    { name: 'Canada', code: 'CA', dialCode: '+1', flag: '🇨🇦', digits: 10 },
    { name: 'Australia', code: 'AU', dialCode: '+61', flag: '🇦🇺', digits: 9 },
    { name: 'Singapore', code: 'SG', dialCode: '+65', flag: '🇸🇬', digits: 8 },
    { name: 'Malaysia', code: 'MY', dialCode: '+60', flag: '🇲🇾', digits: 9 },
    { name: 'Germany', code: 'DE', dialCode: '+49', flag: '🇩🇪', digits: 11 },
    { name: 'France', code: 'FR', dialCode: '+33', flag: '🇫🇷', digits: 9 },
    { name: 'Italy', code: 'IT', dialCode: '+39', flag: '🇮🇹', digits: 10 },
    { name: 'Spain', code: 'ES', dialCode: '+34', flag: '🇪🇸', digits: 9 },
    { name: 'Netherlands', code: 'NL', dialCode: '+31', flag: '🇳🇱', digits: 9 },
    { name: 'Switzerland', code: 'CH', dialCode: '+41', flag: '🇨🇭', digits: 9 },
    { name: 'Brazil', code: 'BR', dialCode: '+55', flag: '🇧🇷', digits: 11 },
    { name: 'Mexico', code: 'MX', dialCode: '+52', flag: '🇲🇽', digits: 10 },
    { name: 'South Africa', code: 'ZA', dialCode: '+27', flag: '🇿🇦', digits: 9 },
    { name: 'Nigeria', code: 'NG', dialCode: '+234', flag: '🇳🇬', digits: 10 },
    { name: 'Kenya', code: 'KE', dialCode: '+254', flag: '🇰🇪', digits: 9 },
    { name: 'Egypt', code: 'EG', dialCode: '+20', flag: '🇪🇬', digits: 10 },
    { name: 'Pakistan', code: 'PK', dialCode: '+92', flag: '🇵🇰', digits: 10 },
    { name: 'Bangladesh', code: 'BD', dialCode: '+880', flag: '🇧🇩', digits: 10 },
    { name: 'Sri Lanka', code: 'LK', dialCode: '+94', flag: '🇱🇰', digits: 9 },
    { name: 'Nepal', code: 'NP', dialCode: '+977', flag: '🇳🇵', digits: 10 },
    { name: 'Indonesia', code: 'ID', dialCode: '+62', flag: '🇮🇩', digits: 11 },
    { name: 'Philippines', code: 'PH', dialCode: '+63', flag: '🇵🇭', digits: 10 },
    { name: 'Vietnam', code: 'VN', dialCode: '+84', flag: '🇻🇳', digits: 9 },
    { name: 'Thailand', code: 'TH', dialCode: '+66', flag: '🇹🇭', digits: 9 },
    { name: 'Qatar', code: 'QA', dialCode: '+974', flag: '🇶🇦', digits: 8 },
    { name: 'Kuwait', code: 'KW', dialCode: '+965', flag: '🇰🇼', digits: 8 },
    { name: 'Oman', code: 'OM', dialCode: '+968', flag: '🇴🇲', digits: 8 },
    { name: 'Bahrain', code: 'BH', dialCode: '+973', flag: '🇧🇭', digits: 8 },
    { name: 'Turkey', code: 'TR', dialCode: '+90', flag: '🇹🇷', digits: 10 },
    { name: 'Japan', code: 'JP', dialCode: '+81', flag: '🇯🇵', digits: 10 },
    { name: 'South Korea', code: 'KR', dialCode: '+82', flag: '🇰🇷', digits: 10 },
    { name: 'New Zealand', code: 'NZ', dialCode: '+64', flag: '🇳🇿', digits: 9 },
    { name: 'Ireland', code: 'IE', dialCode: '+353', flag: '🇮🇪', digits: 9 },
    { name: 'Sweden', code: 'SE', dialCode: '+46', flag: '🇸🇪', digits: 9 },
    { name: 'Norway', code: 'NO', dialCode: '+47', flag: '🇳🇴', digits: 8 },
    { name: 'Denmark', code: 'DK', dialCode: '+45', flag: '🇩🇰', digits: 8 },
    { name: 'Finland', code: 'FI', dialCode: '+358', flag: '🇫🇮', digits: 9 },
    { name: 'Poland', code: 'PL', dialCode: '+48', flag: '🇵🇱', digits: 9 },
    { name: 'Portugal', code: 'PT', dialCode: '+351', flag: '🇵🇹', digits: 9 },
    { name: 'Greece', code: 'GR', dialCode: '+30', flag: '🇬🇷', digits: 10 },
    { name: 'Israel', code: 'IL', dialCode: '+972', flag: '🇮🇱', digits: 9 },
    { name: 'Argentina', code: 'AR', dialCode: '+54', flag: '🇦🇷', digits: 10 },
    { name: 'Chile', code: 'CL', dialCode: '+56', flag: '🇨🇱', digits: 9 },
    { name: 'Colombia', code: 'CO', dialCode: '+57', flag: '🇨🇴', digits: 10 },
    { name: 'Peru', code: 'PE', dialCode: '+51', flag: '🇵🇪', digits: 9 },
];

/**
 * Given a raw phone number (like "+919876543210" or "9876543210"),
 * attempts to find the matching country object and local number portion.
 */
export function parsePhoneNumber(raw) {
    if (!raw) {
        return { country: defaultCountry, localNumber: '' };
    }

    const clean = String(raw).trim();

    // If starts with +, match dial code
    if (clean.startsWith('+')) {
        // Sort dial codes by descending length to match +971 before +9 etc.
        const sorted = [...countries].sort((a, b) => b.dialCode.length - a.dialCode.length);
        for (const c of sorted) {
            if (clean.startsWith(c.dialCode)) {
                let rest = clean.slice(c.dialCode.length).replace(/\D/g, '');
                return { country: c, localNumber: rest };
            }
        }
    }

    // If clean is all digits without +, check if it starts with 91 and has 12 digits (Indian number)
    const digitsOnly = clean.replace(/\D/g, '');
    if (digitsOnly.startsWith('91') && digitsOnly.length === 12) {
        return { country: defaultCountry, localNumber: digitsOnly.slice(2) };
    }

    // Default to India with the digits
    return { country: defaultCountry, localNumber: digitsOnly };
}

/**
 * Normalizes and formats an Indian phone number.
 * Standard format: 10 digits, formatted as "98765 43210".
 */
export function formatIndianNumber(digits) {
    const clean = digits.replace(/\D/g, '');
    if (clean.length <= 5) return clean;
    return clean.slice(0, 5) + ' ' + clean.slice(5, 10);
}

/**
 * Validates whether an Indian number is valid.
 * Must be exactly 10 digits and start with 6, 7, 8, or 9.
 */
export function validateIndianNumber(digits) {
    const clean = digits.replace(/\D/g, '');
    if (clean.length === 0) return { valid: true }; // empty is handled by required validator
    if (clean.length !== 10) {
        return { valid: false, message: 'Indian mobile numbers must be exactly 10 digits.' };
    }
    if (!/^[6-9]/.test(clean)) {
        return { valid: false, message: 'Indian mobile numbers typically start with 6, 7, 8, or 9.' };
    }
    return { valid: true };
}
