document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const container = document.querySelector(".contenedor-principal");
    const navbarToggler = document.querySelector(".navbar-toggler");
    const mainNav = document.querySelector("#mainNav");

    function handleResponsiveLayout() {
        const windowWidth = window.innerWidth;

        if (windowWidth < 768) {
            sidebar.classList.remove("active");

            document.addEventListener('click', handleOutsideClick);
        } else {
            document.removeEventListener('click', handleOutsideClick);

            const savedState = localStorage.getItem('sidebarState');
            if (savedState === 'open') {
                sidebar.classList.remove("active");
                container.classList.remove("shifted");
            } else if (savedState === 'closed') {
                sidebar.classList.add("active");
                container.classList.add("shifted");
            }
        }
    }

    function handleOutsideClick(event) {
        if (window.innerWidth < 768) {
            const isClickInsideSidebar = sidebar.contains(event.target);
            const isClickOnToggleBtn = closeBtn.contains(event.target);

            if (!isClickInsideSidebar && !isClickOnToggleBtn && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                localStorage.setItem('sidebarState', 'closed');
            }
        }
    }

    closeBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        container.classList.toggle("shifted");

        const sidebarState = sidebar.classList.contains("active") ? 'closed' : 'open';
        localStorage.setItem('sidebarState', sidebarState);

        if (window.innerWidth < 768 && mainNav && !mainNav.classList.contains("collapse")) {
            navbarToggler.click();
        }
    });


    if (document.querySelectorAll('.tab-btn').length > 0) {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                document.querySelectorAll('.tab-content').forEach(c => c.classList.add('d-none'));
                const targetContent = document.querySelector(btn.dataset.target);
                if (targetContent) {
                    targetContent.classList.remove('d-none');
                }

                if (window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                    localStorage.setItem('sidebarState', 'closed');
                }
            });
        });
    }

    handleResponsiveLayout();

    window.addEventListener('resize', handleResponsiveLayout);

    setTimeout(() => {
        sidebar.classList.add('transitions-enabled');
        container.classList.add('transitions-enabled');
    }, 100);
});