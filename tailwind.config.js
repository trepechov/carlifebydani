module.exports = {
    content: ['./template-parts/**/*.php', './*.php', './js/*.js'],
    safelist: ['wp-block-gallery', 'wp-block-image'],
    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px'
            // '2xl': '1536px' remove screen larger than x
        },
        fontFamily: {
            body: ['Sofia Sans', 'sans-serif']
        },
        extend: {
            container: {
                center: true
            },
            colors: {
                brand: {
                    red: '#FE3652',
                    lightgrey: '#86898A',
                    grey: '#262626',
                    darkgrey: '#202020',
                    solidgrey: '#1C1C1C',
                    button: '#505050'
                }
            },
            lineHeight: {
                11: '2.75rem',
                12: '3rem'
            },
            borderWidth: {
                10: '10px',
                20: '20px'
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '4rem',
                '6xl': '6rem',
                '8xl': '8rem'
            },
            backgroundImage: ({ theme }) => ({
                'carbon-stripe-black': "url('../images/bg/carbon-stripe-black.png')",
                'carbon-stripe-black-20': "url('../images/bg/carbon-stripe-black-20.png')",
                'carbon-stripe-white': "url('../images/bg/carbon-stripe-white.png')",
                'carbon-stripe-white-20': "url('../images/bg/carbon-stripe-white-20.png')",
                'from-black-gradient': 'linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 50%)',
                'to-black-gradient': 'linear-gradient(0deg, rgba(0, 0, 0, 0.80) 0%, rgba(0, 0, 0, 0.00) 40%);',
                'to-black-gradient-post': 'linear-gradient(360deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 40%);',
                'to-solidgray-gradient-post': 'linear-gradient(360deg, rgba(28,28,28,1) 0%, rgba(28,28,28,0) 40%);',
                'from-black-gradient': 'linear-gradient(180deg, rgba(27, 27, 27, 0.9) 0%, rgba(27, 27, 27, 0) 100%);',
                'from-red-gradient': 'linear-gradient(180deg, #FE3652 0%, rgba(254, 54, 82, 0) 100%);',
                'grey-stripe-gradient': 'linear-gradient(180deg, rgba(38,38,38,0) 80.99%, rgba(38,38,38,1) 81%);'
            }),
            dropShadow: {
                card: '0px -4px 10px rgba(0, 0, 0, 0.10)'
            },
            backgroundSize: {
                'size-1/4': '25%',
                'size-1/3': '33.33%',
                'size-1/2': '50%',
                'size-2/3': '66.67%',
                'size-3/4': '75%',
                'size-full': '100%'
            }
        }
    },
    plugins: [require('@tailwindcss/typography')]
};
