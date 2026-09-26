import { ref, onMounted, onUnmounted } from 'vue';

const isCommandPaletteOpen = ref(false);

export function useCommandPalette() {
    const openCommandPalette = () => {
        isCommandPaletteOpen.value = true;
    };

    const closeCommandPalette = () => {
        isCommandPaletteOpen.value = false;
    };

    const toggleCommandPalette = () => {
        isCommandPaletteOpen.value = !isCommandPaletteOpen.value;
    };

    const setupKeyboardListener = () => {
        const handleKeyDown = (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                toggleCommandPalette();
            } else if (e.key === 'Escape' && isCommandPaletteOpen.value) {
                closeCommandPalette();
            }
        };

        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    };

    return {
        isOpen: isCommandPaletteOpen,
        open: openCommandPalette,
        close: closeCommandPalette,
        toggle: toggleCommandPalette,
        setupKeyboardListener,
    };
}
