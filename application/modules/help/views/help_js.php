<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">

<style>
/* Раскрываем подменю через CSS только во время тура */
.introjs-showMenu > .dropdown-menu {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
}
</style>

<script>
function startHelpTour() {
    const settingsMenu = document.getElementById('menu-settings');
    if (!settingsMenu) return;

    // Раскрываем подменю через CSS
    settingsMenu.classList.add('introjs-showMenu');

    const tour = introJs();

    tour.setOptions({
        steps: [
            { 
                // Привязываем tooltip к видимому подменю, а не к родителю
                element: '#menu-settings > .dropdown-menu', 
                intro: 'Настройки InvoicePlane. Подменю видно.', 
                position: 'bottom'
            },
            { 
                element: '#menu-users', 
                intro: 'Управление пользователями.', 
                position: 'right'
            },
            { 
                element: '#menu-invoice-settings', 
                intro: 'Настройки счетов.', 
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

    // Скрываем меню после выхода из тура
    tour.onexit(function() {
        settingsMenu.classList.remove('introjs-showMenu');
    });

    tour.start();
}

// Запуск по клику на кнопку Help
document.addEventListener('DOMContentLoaded', function() {
    const helpBtn = document.getElementById('help-menu-btn');
    if(helpBtn) helpBtn.addEventListener('click', startHelpTour);
});
</script>
