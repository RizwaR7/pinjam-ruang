/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', '-apple-system', 'sans-serif'],
            },
            colors: {
                // UPR Brand Colors
                // Updated Brand Colors -> Indigo & Rose Theme
                navy: {
                    50: '#eef2ff',
                    100: '#e0e7ff',
                    200: '#c7d2fe',
                    300: '#a5b4fc',
                    400: '#818cf8',
                    500: '#6366f1',
                    600: '#4f46e5',
                    700: '#4338ca',
                    800: '#3730a3',
                    900: '#312e81',
                    950: '#1e1b4b',
                },
                gold: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#f43f5e',
                    600: '#e11d48',
                    700: '#be123c',
                    800: '#9f1239',
                    900: '#881337',
                    950: '#4c0519',
                },
                success: {
                    50: '#e8f5ea',
                    100: '#c8e6c9',
                    200: '#a5d6a7',
                    300: '#81c784',
                    400: '#66bb6a',
                    500: '#28A745',
                    600: '#239a3e',
                    700: '#1e8c36',
                    800: '#197d2f',
                    900: '#146f27',
                },
                danger: {
                    50: '#fce8ea',
                    100: '#f5c6cb',
                    200: '#f1aeb5',
                    300: '#ea868f',
                    400: '#e35d6a',
                    500: '#DC3545',
                    600: '#c6303e',
                    700: '#b02a37',
                    800: '#9a2530',
                    900: '#842029',
                },
            },
        },
    },
    safelist: [
        // Status badge colors used dynamically in Blade templates
        { pattern: /bg-(amber|emerald|rose|sky|slate|navy|gold|success|danger)-(50|100|200|400|500|600|700)/ },
        { pattern: /text-(amber|emerald|rose|sky|slate|navy|gold|success|danger)-(50|100|200|400|500|600|700|800)/ },
        { pattern: /border-(amber|emerald|rose|sky|slate|navy|gold|success|danger)-(100|200|400|500)/ },
        { pattern: /shadow-(navy|gold|success|danger)-(500)\/\d+/ },
        { pattern: /from-(navy|gold)-(400|500|600|700)/ },
        { pattern: /to-(navy|gold)-(400|500|600|700|800)/ },
        { pattern: /via-(navy|gold)-(500|600)/ },
    ],
    plugins: [],
};
