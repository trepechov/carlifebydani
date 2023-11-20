module.exports = {
    content: ['./template-parts/**/*.php', './*.php', './js/*.js'],
    theme: {
        extend: { container: { center: true } }
    },
    plugins: [require('@tailwindcss/typography')]
};
