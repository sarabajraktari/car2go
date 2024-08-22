/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './views/**/*.twig',
  ],

  safelist:[
    'text-red-200'
  ],
  theme: {
    extend: {
      keyframes: {
        slideIn: {
          '0%': { transform: 'translateX(100%)', opacity: '0' },
          '100%': { transform: 'translateX(0)', opacity: '1' },
        },
      },
      animation: {
        'slide-in': 'slideIn 1s ease-out forwards',
      },
      maxWidth: {
        '5.5xl': '67rem',
      },
    },
  },
  plugins: [],
}