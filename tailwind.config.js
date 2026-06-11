import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
                titulo: ['Cabin', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#F2B84B',
                secondary: '#47AAD5',
                dark: '#061C32',
                cream: '#F7FAFC',
            },
        },
    },

    darkMode: 'class',

    plugins: [forms],
};