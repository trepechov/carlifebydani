module.exports = {
    content: [
        './template-parts/**/*.php',
        './*.php',
        './js/*.js',
    ],
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
            borderWidth: {
                16: '16px'
            },
            borderRadius: {
                '4xl': '2rem',
                '5xl': '4rem',
                '6xl': '6rem',
                '8xl': '8rem'
            },
            backgroundImage: ({ theme }) => ({
                pattern: "url('../images/bg-pattern.png')",
                'from-black-gradient': 'linear-gradient(180deg, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0) 50%)',
                'to-black-gradient': 'linear-gradient(0deg, rgba(0, 0, 0, 0.80) 0%, rgba(0, 0, 0, 0.00) 40%);',
                'to-black-gradient-post': 'linear-gradient(360deg, rgba(0,0,0,1) 0%, rgba(0,0,0,0) 40%);',
                'to-solidgray-gradient-post': 'linear-gradient(360deg, rgba(28,28,28,1) 0%, rgba(28,28,28,0) 40%);'
            }),
            backgroundSize: {
                'size-200': '200% 200%'
            },
            backgroundPosition: {
                'pos-0': '0% 0%',
                'pos-100': '100% 100%'
            }
        }
    },
    plugins: [require('@tailwindcss/typography')]
};
