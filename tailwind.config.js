/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.vue",
    "./resources/**/*.js",
  ],
  theme: {
    extend: {
      colors: {
        'navy': '#1a1f3c',
        'navy-2': '#2d3561',
        'purple': '#534AB7',
        'purple-dark': '#3C3489',
        'purple-bg': '#EEEDFE',
        'blue': '#185FA5',
        'blue-bg': '#E6F1FB',
        'teal': '#0F6E56',
        'teal-bg': '#E1F5EE',
        'amber': '#854F0B',
        'amber-bg': '#FAEEDA',
      },
      fontFamily: {
        'display': ['Syne', 'sans-serif'],
        'body': ['DM Sans', 'sans-serif'],
        'mono': ['DM Mono', 'monospace'],
      }
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}