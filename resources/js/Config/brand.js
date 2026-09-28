/**
 * Wappiyo Official Brand Identity & Configuration
 * Extracted directly from official Logo & Favicon assets.
 */

export const BRAND_CONFIG = {
    name: 'Wappiyo',
    tagline: 'WhatsApp Marketing, Automation & CRM Platform',
    
    // Official Asset Paths
    assets: {
        logo: '/images/logo.png',
        logoLight: '/images/logo-light.png',
        logoDark: '/images/logo-dark.png',
        logoMark: '/images/logo-mark.png',
        favicon: '/favicon.ico',
        faviconPng: '/images/favicon.png',
        ogImage: '/images/og-image.png',
        appleTouchIcon: '/apple-touch-icon.png',
    },

    // Official Sampled Brand Colors
    colors: {
        primary: '#22C55E',      // Smiling Chat Bubble Emerald / WhatsApp Green
        primaryHover: '#16A34A', // Deep green on hover
        secondary: '#022828',    // Official Logo Wordmark / Deep Dark Slate Teal
        accent: '#34D399',       // Mint / Emerald accent highlight
        dark: '#022828',         // Brand dark tone
        light: '#F0FDF4',        // Brand light tint / wash
        surfaceDark: '#09090B',  // Zinc-950 dark canvas
        surfaceElevated: '#111113', // Elevated surface
    },

    // Brand Gradients
    gradients: {
        primary: 'linear-gradient(135deg, #22C55E 0%, #10B981 100%)',
        brandDark: 'linear-gradient(135deg, #022828 0%, #064E3B 100%)',
        accent: 'linear-gradient(135deg, #34D399 0%, #22C55E 100%)',
        glow: 'radial-gradient(circle at 50% 50%, rgba(34, 197, 94, 0.15) 0%, transparent 70%)',
    }
};

export const BRAND = {
    name: BRAND_CONFIG.name,
    logo: BRAND_CONFIG.assets.logo,
    logoLight: BRAND_CONFIG.assets.logoLight,
    logoDark: BRAND_CONFIG.assets.logoDark,
    mark: BRAND_CONFIG.assets.logoMark,
    favicon: BRAND_CONFIG.assets.favicon,
};

export default BRAND_CONFIG;
