/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./index.php",
    "./app/Views/**/*.php",
    "./install/**/*.php",
  ],
  safelist: [
    // Gradien kartu dashboard (dirakit dinamis di PHP)
    { pattern: /(from|to)-(indigo|violet|emerald|amber|rose|slate)-(500|600|700)/ },
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          50: "#eef2ff",
          100: "#e0e7ff",
          200: "#c7d2fe",
          500: "#6366f1",
          600: "#4f46e5",
          700: "#4338ca",
          900: "#312e81",
        },
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "-apple-system", "Segoe UI", "sans-serif"],
      },
    },
  },
  plugins: [],
};
