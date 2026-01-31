import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js'
    ],

    theme: {
        extend: {
            colors: {
                'clucky-dark': '000000',
                'clucky-gray': 'E5E/EB',
            }
        },
    },

    plugins: [],
};
