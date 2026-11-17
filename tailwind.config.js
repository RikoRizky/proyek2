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
                sans: ['Plus Jakarta Sans', 'Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Exact palette swatches from user image:
                palladian: '#EEE9DF',
                oatmeal: '#C9C1B1',
                'blue-fantastic': '#2C3B4D',
                'burning-flame': '#FFB162',
                'truffle-trouble': '#A35139',
                'abyssal-blue': '#1B2632',
                abyssal: '#1B2632',
                flame: '#FFB162',
                truffle: '#A35139',

                // Full cohesive brand scale:
                brand: {
                    palladian: '#EEE9DF',
                    oatmeal: '#C9C1B1',
                    bluefantastic: '#2C3B4D',
                    burningflame: '#FFB162',
                    truffletrouble: '#A35139',
                    abyssal: '#1B2632',
                    50: '#FDFBF7',
                    100: '#EEE9DF',
                    200: '#E1D9CC',
                    300: '#C9C1B1',
                    400: '#FFB162',
                    500: '#D26B38',
                    600: '#A35139',
                    700: '#8A412C',
                    800: '#2C3B4D',
                    900: '#1B2632',
                    950: '#111821',
                },

                // Aliased to the palette to harmonize any remaining template classes:
                violet: {
                    50: '#FAF6F0',
                    100: '#EEE9DF',
                    200: '#E1D9CC',
                    300: '#C9C1B1',
                    400: '#FFB162',
                    500: '#D26B38',
                    600: '#A35139',
                    700: '#8A412C',
                    800: '#2C3B4D',
                    900: '#1B2632',
                    950: '#111821',
                },
                purple: {
                    50: '#FAF6F0',
                    100: '#EEE9DF',
                    200: '#FFD7AB',
                    300: '#FFC488',
                    400: '#FFB162',
                    500: '#D26B38',
                    600: '#A35139',
                    700: '#8A412C',
                    800: '#2C3B4D',
                    900: '#1B2632',
                    950: '#111821',
                },
                indigo: {
                    50: '#F5F7FA',
                    100: '#E4E8EE',
                    200: '#CAD4DF',
                    300: '#9FB3C7',
                    400: '#547291',
                    500: '#3D536B',
                    600: '#2C3B4D',
                    700: '#23303F',
                    800: '#1B2632',
                    900: '#131C26',
                    950: '#0C1219',
                },
            },
            boxShadow: {
                soft: '0 4px 24px -4px rgba(27, 38, 50, 0.08), 0 8px 32px -8px rgba(163, 81, 57, 0.06)',
            },
        },
    },

    plugins: [forms],
};
