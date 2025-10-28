import { ref, onMounted } from 'vue';

const isDark = ref(false);

export function useTheme() {
    onMounted(() => {
        const saved = localStorage.getItem('color-theme');
        isDark.value = saved === 'dark' || document.documentElement.classList.contains('dark');
        updateTheme();
    });

    function toggleTheme() {
        isDark.value = !isDark.value;
        updateTheme();
    }

    function updateTheme() {
        if (isDark.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('color-theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('color-theme', 'light');
        }
    }

    return { isDark, toggleTheme };
}