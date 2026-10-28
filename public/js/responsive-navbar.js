document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const menuButton = document.getElementById('mobileMenuButton');
    const overlay = document.getElementById('sidebarOverlay');
    const mobileQuery = window.matchMedia('(max-width: 720px)');

    if (!sidebar || !menuButton || !overlay) {
        return;
    }

    function setMenuState(isOpen, returnFocus = false) {
        const canOpen = mobileQuery.matches;
        const nextState = Boolean(isOpen && canOpen);

        sidebar.classList.toggle('is-open', nextState);
        overlay.classList.toggle('is-open', nextState);
        document.body.classList.toggle('mobile-menu-open', nextState);

        menuButton.setAttribute('aria-expanded', String(nextState));
        menuButton.setAttribute(
            'aria-label',
            nextState ? 'Close navigation' : 'Open navigation'
        );
        overlay.setAttribute('aria-hidden', String(!nextState));
        overlay.tabIndex = nextState ? 0 : -1;

        if (!nextState && returnFocus && mobileQuery.matches) {
            menuButton.focus();
        }
    }

    menuButton.addEventListener('click', function () {
        setMenuState(!sidebar.classList.contains('is-open'));
    });

    overlay.addEventListener('click', function () {
        setMenuState(false, true);
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && sidebar.classList.contains('is-open')) {
            setMenuState(false, true);
        }
    });

    sidebar.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (mobileQuery.matches) {
                setMenuState(false);
            }
        });
    });

    function handleViewportChange(event) {
        if (!event.matches) {
            setMenuState(false);
        }
    }

    if (typeof mobileQuery.addEventListener === 'function') {
        mobileQuery.addEventListener('change', handleViewportChange);
    } else {
        mobileQuery.addListener(handleViewportChange);
    }
});
