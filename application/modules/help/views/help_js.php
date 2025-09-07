<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">

<style>
/* Временное раскрытие подменю только при показе intro.js */
.introjs-showMenu > .dropdown-menu {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

<script>
function tourInvoiceGroups(startStep = 0) {
    const settingsMenu = document.getElementById('menu-settings');
    if (!settingsMenu) return;

    const tour = introJs();

    tour.setOptions({
        steps: [
            { 
                element: '#menu-settings',
                intro: 'Это меню настроек INVONOS.',
                position: 'right'
            },
            { 
                element: '#menu',
                intro: 'Первым делом нам необходимо настроить порядковый номер инвойсов',
                position: 'right'
            },

            { 
                element: '#menu-invoice_groups',
                intro: 'Это можно сделать в разделе "Группы счетов"',
                position: 'right'
            }
        ],
        showStepNumbers: true,
        exitOnOverlayClick: true,
        showBullets: false,
        nextLabel: 'Next',
        prevLabel: 'Back',
        skipLabel: 'Skip',
        doneLabel: 'Done'
    });

    function tourInvoiceGroupsID(startStep = 0) {
    const settingsMenu = document.getElementById('menu-settings');
    if (!settingsMenu) return;

    const tour = introJs();

    tour.setOptions({
        steps: [
            { 
                element: '#menu-settings',
                intro: 'Это меню настроек INVONOS.',
                position: 'right'
            },
            { 
                element: '#menu',
                intro: 'Первым делом нам необходимо настроить порядковый номер инвойсов',
                position: 'right'
            },

            { 
                element: '#menu-invoice_groups',
                intro: 'Это можно сделать в разделе "Группы счетов"',
                position: 'right'
            }
        ],
        showStepNumbers: true,
        exitOnOverlayClick: true,
        showBullets: false,
        nextLabel: 'Next',
        prevLabel: 'Back',
        skipLabel: 'Skip',
        doneLabel: 'Done'
    });

    // Перед шагами 2+ раскрываем меню
    tour.onbeforechange(function(targetElement) {
        if (
            targetElement.id === 'menu-users' ||
            targetElement.id === 'menu-invoice-settings' ||
            targetElement.id === 'menu-invoice_groups'
        ) {
            settingsMenu.classList.add('introjs-showMenu');
        }
    });

    // После выхода убираем класс
    tour.onexit(function() {
        settingsMenu.classList.remove('introjs-showMenu');
    });

    tour.start().goToStep(startStep).start();
}

// Вызов по кнопке Help
document.addEventListener('DOMContentLoaded', function() {
    const helpBtn = document.getElementById('help-invoiceid'); 
    if (helpBtn) helpBtn.addEventListener('click', function(e) {
        e.preventDefault();
        tourInvoiceGroups();
    });
});
</script>
