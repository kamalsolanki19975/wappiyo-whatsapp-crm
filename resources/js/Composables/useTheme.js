import { ref, watch, onMounted } from 'vue';

const isDark = ref(false);
const theme = ref('light');

function initTheme() {
    try {
        const storedTheme = localStorage.getItem('wappiyo_theme');
        if (storedTheme === 'dark' || (!storedTheme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            isDark.value = true;
            theme.value = 'dark';
            document.documentElement.classList.add('dark');
        } else {
            isDark.value = false;
            theme.value = 'light';
            document.documentElement.classList.remove('dark');
        }
    } catch (_) {}
}

export function useTheme() {
    const toggleTheme = () => {
        isDark.value = !isDark.value;
        theme.value = isDark.value ? 'dark' : 'light';
        
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('wappiyo_theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('wappiyo_theme', 'light');
        }
    };

    const setTheme = (newTheme) => {
        if (newTheme === 'dark') {
            isDark.value = true;
            theme.value = 'dark';
            document.documentElement.classList.add('dark');
            localStorage.setItem('wappiyo_theme', 'dark');
        } else {
            isDark.value = false;
            theme.value = 'light';
            document.documentElement.classList.remove('dark');
            localStorage.setItem('wappiyo_theme', 'light');
        }
    };

    return {
        isDark,
        theme,
        toggleTheme,
        setTheme,
        initTheme,
    };
}
