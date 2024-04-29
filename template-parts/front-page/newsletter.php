<div class="wrapper py-12">
    <div class="flex mb-8 justify-between items-center">
        <h3 class="title" id="newsletter">Бъди информиран</h3>
    </div>

    <div class="w-full lg:rounded-br-8xl lg:border-b-20 lg:border-r-20 lg:pb-5 lg:pr-5 lg:border-white/10">
        <div class="bg-black p-10 bg-no-repeat bg-right-top lg:bg-newsletter lg:rounded-br-6xl">
            <div class="lg:w-1/2">
                <h5 class="uppercase text-brand-lightgrey">Newsletter</h5>
                <h2 class="mt-4">Готов ли си да получиш<br />нашите нови неща?</h2>
                <form class="relative my-8" method="POST" action="https://6a339945.sibforms.com/serve/MUIFAM6tlAmdv7dycB8wWnMs0oQcHIPyDyZso1hmMOTEP8mxivD0FCgoDrq3uRRdgF9nnaNyoZYWJ6LZ49PSN2yHDhKuwouFUHuxXXo5r4YYA_BdvK89ko-eh-TcVIfRczeJUKRozYTEVNDiEtKvOpuBngus56DqA_HzCU5nbvBzSmedJ_arMlW6W-FTbjDkIZ1L3zoSTVJYh4qF" data-type="subscription">
                    <input type="email" name="EMAIL" required placeholder="Въведи имейл" class="bg-transparent text-white w-full text-lg block p-1 pb-4 focus:outline-none border-b-2 border-b-brand-button focus:border-b-brand-lightgrey placeholder:text-brand-grey rounded-none">
                    <input type="text" name="email_address_check" value="" class="hidden">
                    <input type="hidden" name="locale" value="en">
                    <input type="hidden" name="html_type" value="simple">
                    <button type="submit" class="absolute top-1 right-1 text-lg">Запиши се</button>
                </form>
                <?php if (isset($_GET['newsletter']) && $_GET['newsletter'] === 'success') { ?>
                    <p class="text-base text-green-600">Абонирахте се успешно!</p>
                <?php } else { ?>
                    <p class="text-base text-brand-lightgrey">Запишете се, за да получавате актуална<br />информация от света на електромобилите.</p>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="g-recaptcha invisible" data-sitekey="6Ld3r8MpAAAAAM36FmOmtv3IPkc8fIRblbbFebkx" data-callback="invisibleCaptchaCallback" data-size="invisible" onclick="executeCaptcha"></div>
</div>

<script>
    var AUTOHIDE = Boolean(0);
</script>
<script defer src="https://sibforms.com/forms/end-form/build/main.js"></script>
<script src="https://www.google.com/recaptcha/api.js?hl=en"></script>