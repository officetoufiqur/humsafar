import { onMounted, ref } from 'vue';

export function updateTheme() {
    if (typeof window === 'undefined') {
        return;
    }

    // Always remove dark class to ensure light mode
    document.documentElement.classList.remove('dark');
}

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const appearance = ref<'light'>('light');

export function useAppearance() {
    onMounted(() => {
        // Optional: you could restore saved appearance, but we force 'light' anyway
        appearance.value = 'light';
        updateTheme();
    });

    function updateAppearance() {
        // We only support light, so do nothing else
        localStorage.setItem('appearance', 'light');
        setCookie('appearance', 'light');
        updateTheme();
    }

    return {
        appearance,
        updateAppearance,
    };
}

export function initializeTheme() {
    updateTheme(); // always force light
}
