<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>

<script>
function startHelpTour() {
    introJs().setOptions({
        steps: [
            {
                intro: "Добро пожаловать в InvoicePlane! Сейчас я покажу основные меню."
            },
            {
                element: document.querySelector('#menu-clients'),
                intro: "Здесь вы можете управлять клиентами."
            },
            {
                element: document.querySelector('#menu-invoices'),
                intro: "А это раздел для счетов."
            },
            {
                element: document.querySelector('#menu-settings'),
                intro: "В настройках вы управляете параметрами системы."
            }
        ],
        nextLabel: 'Далее',
        prevLabel: 'Назад',
        doneLabel: 'Готово'
    }).start();
}
</script>