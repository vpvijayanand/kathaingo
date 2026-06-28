import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    safelist: [
        // Ensure all burnt-orange and slate-gray utilities are ALWAYS compiled
        // Pattern with variants covers: bg-, text-, border-, ring-, hover:, focus:, active:, file:
        {
            pattern: /^(bg|text|border|ring|from|to)-(burnt-orange|slate-gray)(\/\d+)?$/,
            variants: ['hover', 'focus', 'active', 'focus-within', 'group-hover', 'file'],
        },
        // Explicit extras
        'bg-burnt-orange/20',
        'file:bg-burnt-orange',
        'file:text-white',
        'min-h-screen',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                serif: ['Marcellus', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                'slate-gray': '#0B0F19', // Rich Midnight Navy
                'brand-slate-gray': '#2A3E54', // Original Brand Slate Gray
                'burnt-orange': '#F28C28', // Primary Orange
                'brand-deep-brown': '#3A1F0B',
                'brand-warm-brown': '#8B4A12',
                'brand-ivory': '#FFF8ED',
            },
        },
    },

    plugins: [forms],
};
