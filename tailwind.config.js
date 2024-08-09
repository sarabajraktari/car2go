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
		fontSize: {
        '10px': '10px',
      },
		screens: {
        'exsm': '200px',
      },
	},
  },
  plugins: [],
}