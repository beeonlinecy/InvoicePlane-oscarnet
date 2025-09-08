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
        { element: '#menu-settings', intro: 'This is the <b>settings menu</b> of INVONOS.', position: 'right' },
        { element: '#menu', intro: 'You can change <b>invoice number order</b> and I will show you where', position: 'right' },
        { element: '#menu-invoice_groups', intro: 'To change <b>invoice number order</b> you have to visit <b>Invoice groups</b> setting.', position: 'right' }
        
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

function startTourInvoiceGroupsPage(startStep = 0) {
    const tour = introJs();

    // Динамически находим первый элемент группы инвойсов
    const firstGroupBtnSelector = '.options:first-child';

    function buildSteps() {
        const steps = [
            {
                intro: 'This is invoice groups settings page. Here you can edit and create <b>invoice number order</b>, <b>display format</b> and <b>next number</b>',
                position: 'bottom'
            }
        ];

        const firstGroupBtn = document.querySelector(firstGroupBtnSelector);
        if (firstGroupBtn) {
            steps.push({
                element: firstGroupBtn,
                intro: 'To change invoice number format and next number, you have to access this menu',
                position: 'bottom',
                offset: { top: 0, left: 0 }
            });
        } else {
            console.warn('Button isnt found. Skipping');
        }

        steps.push({
            intro: 'You can create, delete and edit invoice groups. <b>Please be careful! Normally this setting is done once at the beginning of system usage</b>',
            position: 'bottom'
        });

        return steps;
    }

    tour.setOptions({
        steps: buildSteps(),
        showStepNumbers: true,
        exitOnOverlayClick: true,
        showBullets: false,
        nextLabel: 'Next',
        prevLabel: 'Back',
        skipLabel: 'Skip',
        doneLabel: 'Done'
    });

    // Подсветка и смещение при каждом шаге
    tour.onbeforechange(function(targetElement) {
        if (!targetElement) return;
        // Если есть раскрывающееся меню, его можно раскрыть:
        const settingsMenu = document.getElementById('menu-settings');
        if (settingsMenu) settingsMenu.classList.add('introjs-showMenu');

        // Смещение подсказки
        const tooltip = document.querySelector('.introjs-tooltip');
        if (tooltip) {
            const stepData = tour._introItems[tour._currentStep];
            if (stepData && stepData.offset) {
                tooltip.style.transform = `translate(${stepData.offset.left || 0}px, ${stepData.offset.top || 0}px)`;
            }
        }
    });

    tour.onexit(function() {
        const settingsMenu = document.getElementById('menu-settings');
        if (settingsMenu) settingsMenu.classList.remove('introjs-showMenu');
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
        startTourInvoiceGroupsPage();
    }
});
</script>
