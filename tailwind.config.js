import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                navy: { DEFAULT: '#1E3A8A', light: '#3B82F6', soft: '#DBEAFE' },
                surface: '#F8FAFC',
                border: '#E2E8F0',
                ink: '#0F172A',
                muted: '#64748B',
                success: '#22C55E',
                warning: '#F59E0B',
                danger: '#EF4444',
            },
            borderRadius: {
                'card': '12px',
                'card-lg': '16px',
            },
        },
    },
    plugins: [forms],
};
