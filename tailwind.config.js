import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        // As cores da patente do doador vêm do model
        './app/Models/Doador.php',
    ],

    theme: {
        extend: {
            // Mesma paleta da tela do mapa: sangue venoso, arterial e plasma sobre o branco frio clínico
            colors: {
                sangue: { DEFAULT: '#9B1027', escuro: '#7E0C1F', claro: '#FBEFF1' },
                arterial: '#D21F3C',
                plasma: { DEFAULT: '#E9B949', claro: '#FBF0D2', escuro: '#6B4E00' },
                gaze: '#F3F5F6',
                tinta: '#1D2433',
                cinza: '#5B6474',
                linha: '#DDE2E8',
                // Borda de campos: contraste de 3:1 com o branco (WCAG para controles)
                borda: '#878F9D',
            },
            fontFamily: {
                titulo: ['"Familjen Grotesk"', ...defaultTheme.fontFamily.sans],
                sans: ['"Atkinson Hyperlegible"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
