import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Palet warna resmi JogjaTrip — pakai kelas ini di semua halaman
                // agar tampilan konsisten (jangan pakai warna Tailwind default).
                primary: {
                    DEFAULT: '#2F6B5A', // hijau tua
                    50: '#EAF2EF',
                    100: '#D4E5DF',
                    200: '#A9CBBF',
                    300: '#7EB19F',
                    400: '#53977F',
                    500: '#2F6B5A',
                    600: '#275A4C',
                    700: '#1F483D',
                    800: '#17362E',
                    900: '#0F241F',
                },
                accent: {
                    DEFAULT: '#E8D5DA', // pink lembut
                    50: '#FBF6F7',
                    100: '#F6EDEF',
                    200: '#E8D5DA',
                    300: '#DAB9C2',
                    400: '#C993A1',
                    500: '#B56E80',
                    600: '#9C5468',
                },
                cream: '#FAF8F3', // latar krem / putih gading
                ink: '#3A3A3A', // teks abu gelap
            },
        },
    },

    plugins: [forms],
};
