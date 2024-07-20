module.exports = {
    content: ['./template-parts/**/*.php', './*.php', './js/*.js'],
    safelist: ['wp-block-gallery', 'wp-block-image', 'nav-links'],
    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
            '2xl': '1440px'
        },
        fontFamily: {
            body: ['Sofia Sans', 'sans-serif']
        },
        extend: {
            // container: {
            //     center: true,
            //     padding: {
            //         DEFAULT: '1rem',
            //         lg: '1.5rem'
            //     }
            // },
            spacing: {
                112: '28rem',
                128: '32rem',
                144: '36rem',
                160: '40rem',
                192: '48rem',
                224: '56rem'
            },
            colors: {
                brand: {
                    red: '#FE3652',
                    green: '#87C840',
                    lightgrey: '#86898A',
                    button: '#505050',
                    grey: '#262626',
                    darkgrey: '#202020',
                    solidgrey: '#1C1C1C'
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
            zIndex: {
                999: '99999999'
            },
            backgroundImage: ({ theme }) => ({
                'carbon-stripe-black': "url('../images/bg/carbon-stripe-black.png')",
                'carbon-stripe-black-20': "url('../images/bg/carbon-stripe-black-20.png')",
                'carbon-stripe-white': "url('../images/bg/carbon-stripe-white.png')",
                'carbon-stripe-white-20': "url('../images/bg/carbon-stripe-white-20.png')",
                newsletter: "url('../images/bg/newsletter.png')",
                'from-black-80-gradient': 'linear-gradient(180deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);',
                'from-black-60-gradient': 'linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 80%)',
                'to-black-80-gradient': 'linear-gradient(0deg, rgba(0, 0, 0, 0.80) 0%, rgba(0, 0, 0, 0.00) 40%);',
                'to-black-gradient-post': 'linear-gradient(0deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 40%);',
                'to-black-gradient': 'linear-gradient(0deg, rgba(0,0,0,1) 50%, rgba(0, 0, 0, 0) 100%);',
                'to-black-gradient-mobile': 'linear-gradient(0deg, rgba(0,0,0,1) 70%, rgba(0, 0, 0, 0) 100%);',
                'to-solidgray-gradient-post': 'linear-gradient(0deg, rgba(28,28,28,1) 0%, rgba(28,28,28,0) 40%);',
                'from-red-gradient': 'linear-gradient(180deg, #FE3652 0%, rgba(254, 54, 82, 0) 100%);'
            }),
            boxShadow: {
                card: '0px -4px 10px rgba(0, 0, 0, 0.20)'
            },
            backgroundSize: {
                'size-1/4': '25%',
                'size-1/3': '33.33%',
                'size-1/2': '50%',
                'size-2/3': '66.67%',
                'size-3/4': '75%',
                'size-full': '100%',
                'size-5/4': '125%',
                'size-3/2': '150%',
                'size-7/8': '175%'
            }
        }
    },
    plugins: [require('@tailwindcss/typography')]
};
