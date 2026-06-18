// obtain cookieconsent plugin
var cc = initCookieConsent();

// example logo
var logo =
    '<img src="./wp-content/themes/carlifebydani/images/logo.svg" alt="Logo" loading="lazy" style="margin-left: -4px; margin-bottom: -7px; height: 50px">';

var logoen =
    '<img src="./wp-content/themes/carlifebydani/images/logo.svg" alt="Logo EN" loading="lazy" style="margin-left: -4px; margin-bottom: -7px; height: 50px">';

var cookie =
    '<img src="/wp-content/themes/carlifebydani/images/cookies-icon.png" alt="icon" loading="lazy" style="margin-left: -4px; margin-right: 5px; margin-bottom: -8px; height: 30px">';

// add darknide class to get proper styles
document.documentElement.classList.add('c_darkmode');
// run plugin with config object
cc.run({
    //current_lang : 'bg',
    autoclear_cookies: true, // default: false
    cookie_name: 'cc_cookie', // default: 'cc_cookie'
    cookie_expiration: 30, // default: 182
    page_scripts: true, // default: false

    auto_language: 'document', // default: null; could also be 'browser' or 'document'
    // autorun: true,                           // default: true
    // delay: 0,                                // default: 0
    // force_consent: false,
    // hide_from_bots: false,                   // default: false
    // remove_cookie_tables: false              // default: false
    // cookie_domain: location.hostname,        // default: current domain
    // cookie_path: "/",                        // default: root
    // cookie_same_site: "Lax",
    // use_rfc_cookie: false,                   // default: false
    // revision: 0,                             // default: 0

    gui_options: {
        consent_modal: {
            layout: 'box', // box,cloud,bar
            position: 'bottom left', // bottom,middle,top + left,right,center
            transition: 'zoom' // zoom,slide
        },
        settings_modal: {
            layout: 'box', // box,bar
            // position: 'left',                // right,left (available only if bar layout selected)
            transition: 'zoom' // zoom,slide
        }
    },

    onFirstAction: function () {},

    onAccept: function (cookie) {},

    onChange: function (cookie, changed_preferences) {},

    languages: {
        bg: {
            consent_modal: {
                title: ' Ние използваме бисквитки ',
                description:
                    'Здравейте, този уебсайт използва бисквитки за да може да функционира правилно. <button type="button" data-cc="c-settings" class="cc-link">Настройки на бисквитките</button>',
                primary_btn: {
                    text: 'Приемане',
                    role: 'accept_all' // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Отхвърляне',
                    role: 'accept_necessary' // 'settings' or 'accept_necessary'
                }
            },
            settings_modal: {
                title: logo,
                save_settings_btn: 'Запази настройките',
                accept_all_btn: 'Приемане',
                reject_all_btn: 'Отхвърляне',
                close_btn_label: 'Затвори',
                cookie_table_headers: [
                    { col1: 'Name' },
                    { col2: 'Domain' },
                    { col3: 'Expiration' },
                    { col4: 'Description' }
                ],
                blocks: [
                    {
                        title: 'Управление на Вашата поверителност',
                        description:
                            'Когато посетите даден уебсайт, той може да съхрани информация на Вашия компютър във връзка с Вашия браузър или да извлече информация, свързана с ползването на уебсайта, най-често под формата на т.нар. „бисквитки“. Тази информация може да се отнася до Вас, Вашите предпочитания, Вашето устройство или да бъде използвана с цел сайтът да работи съгласно Вашите очаквания.'
                    },
                    {
                        title: 'Строго необходими бисквитки',
                        description:
                            'Тези бисквитки са необходими за функционирането на уебсайта и не могат да бъдат изключени в нашите системи.',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    },
                    {
                        title: 'Аналитични бисквитки',
                        description:
                            'Тези бисквитки събират анонимна, обобщена информация и ни позволяват да идентифицираме най-популярните си страници и съдържание, за да подобрим сайта и как той функционира за потребителите.',
                        toggle: {
                            value: 'analytics', // there are no default categories => you specify them
                            enabled: true,
                            readonly: false
                        }
                    }
                ]
            }
        }
    }
});
