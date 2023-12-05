module.exports = {
    content: ['./template-parts/**/*.php', './*.php', './js/*.js'],
    theme: {
        extend: {
            container: { center: true },
            colors: {
                brand: {
                    red: '#FE3652',
                    lightgrey: '#86898A',
                    grey: '#262626',
                    darkgrey: '#202020',
                    button: '#505050'
                }
            }
        }
    },
    plugins: [require('@tailwindcss/typography')]
};
