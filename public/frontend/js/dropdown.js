// Resources Dropdown — Desktop: hover | Mobile: click


document.addEventListener('DOMContentLoaded', function () {
    const li     = document.querySelector('.td-resources');
    const menu   = li ? li.querySelector('.td-dropdown') : null;
    const toggle = li ? li.querySelector('.td-resources-toggle') : null;

    if (!li || !menu || !toggle) return;

    function isDesktop() { return window.innerWidth >= 992; }

    function openMenu() {
        menu.classList.add('open');
        li.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    }

    function closeMenu() {
        menu.classList.remove('open');
        li.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    }

    // Desktop — hover
    li.addEventListener('mouseenter', function () {
        if (isDesktop()) openMenu();
    });
    li.addEventListener('mouseleave', function () {
        if (isDesktop()) closeMenu();
    });

    // Mobile — click
    toggle.addEventListener('click', function (e) {
        e.preventDefault();
        if (!isDesktop()) {
            menu.classList.contains('open') ? closeMenu() : openMenu();
        }
    });

    
    document.addEventListener('click', function (e) {
        if (!li.contains(e.target)) closeMenu();
    });
});