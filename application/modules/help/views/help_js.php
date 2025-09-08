<script src="https://cdn.jsdelivr.net/npm/intro.js/minified/intro.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intro.js/minified/introjs.min.css">

<style>
.introjs-showMenu > .dropdown-menu {
  display: block !important;
  opacity: 1 !important;
  visibility: visible !important;
}
</style>

<script>
const TOUR_NEXT_PAGE_KEY = 'tourNextPage';

function startTourInvoiceGroups(startStep = 0) {
    const settingsMenu = document.getElementById('menu-settings');
    if (!settingsMenu) return;

    const tour = introJs();

    const steps = [
        { element: '#menu-settings', intro: 'This is the settings menu of INVONOS.', position: 'right' },
        { element: '#menu', intro: 'To access invoice ', position: 'right' },
        { element: '#menu-invoice_groups', intro: 'Это можно сделать в разделе "Группы счетов". Нажмите Next для перехода.', position: 'right' },
        { intro: 'Теперь вы на странице групп счетов!', position: 'right' }
    ];

    tour.setOptions({
        steps,
        showStepNumbers: true,
        exitOnOverlayClick: true,
        showBullets: false,
        nextLabel: 'Next',
        prevLabel: 'Back',
        skipLabel: 'Skip',
        doneLabel: 'Done'
    });

    tour.onbeforechange(function(targetElement) {
        if (!targetElement) return;
        const dropdownIds = ['menu-users','menu-invoice-settings','menu-invoice_groups'];
        if (dropdownIds.includes(targetElement.id)) settingsMenu.classList.add('introjs-showMenu');
    });

    tour.onexit(function() {
        settingsMenu.classList.remove('introjs-showMenu');
    });

    tour.oncomplete(function() {
        // Динамический редирект с учётом субдиректории
        const currentPath = window.location.pathname; 
        const nextPage = currentPath.replace(/index\.php.*$/, 'index.php/invoice_groups');
        localStorage.setItem(TOUR_NEXT_PAGE_KEY, nextPage);
        window.location.href = nextPage;
    });

    tour.start().goToStep(startStep);
}

document.addEventListener('DOMContentLoaded', function() {
    const helpBtn = document.getElementById('help-invoiceid');
    if (helpBtn) {
        helpBtn.addEventListener('click', function(e) {
            e.preventDefault();
            startTourInvoiceGroups();
        });
    }

    // Запуск следующего тура на новой странице
    const nextPageTour = localStorage.getItem(TOUR_NEXT_PAGE_KEY);
    if (nextPageTour && nextPageTour === window.location.pathname) {
        localStorage.removeItem(TOUR_NEXT_PAGE_KEY);
        // здесь можно вызвать функцию нового тура для страницы "Группы счетов"
        // например: startTourInvoiceGroupsStep2();
    }
});
</script>
