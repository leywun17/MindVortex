/**
 * MindVortex - Script para manejar el sidebar responsivo
 * Controla el comportamiento de la barra lateral en diferentes dispositivos
 */

document.addEventListener('DOMContentLoaded', function () {
    // Elementos principales
    const sidebar = document.querySelector(".sidebar");
    const closeBtn = document.querySelector("#btn");
    const container = document.querySelector(".contenedor-principal");
    const navbarToggler = document.querySelector(".navbar-toggler");
    const mainNav = document.querySelector("#mainNav");

    /**
     * Maneja el comportamiento de la barra lateral según el tamaño de pantalla
     */
    function handleResponsiveLayout() {
        const windowWidth = window.innerWidth;
        
        if (windowWidth < 768) {
            // En dispositivos móviles, asegurar que el sidebar está cerrado por defecto
            sidebar.classList.remove("active");
            
            // Configurar efecto overlay para móviles
            document.addEventListener('click', handleOutsideClick);
        } else {
            // En dispositivos grandes
            document.removeEventListener('click', handleOutsideClick);
            
            // Restaurar estado guardado si existe
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

    /**
     * Maneja clics fuera del sidebar para cerrarlo en móviles
     */
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

    /**
     * Toggle del sidebar al hacer clic en el botón
     */
    closeBtn.addEventListener("click", function () {
        sidebar.classList.toggle("active");
        container.classList.toggle("shifted");
        
        // Guardar preferencia del usuario
        const sidebarState = sidebar.classList.contains("active") ? 'closed' : 'open';
        localStorage.setItem('sidebarState', sidebarState);
        
        // En móviles, cerrar el navbar si está abierto
        if (window.innerWidth < 768 && mainNav && !mainNav.classList.contains("collapse")) {
            navbarToggler.click();
        }
    });

    /**
     * Configurar tabs si existen
     */
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
                
                // En móviles, cerrar el sidebar después de seleccionar una tab
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('active');
                    localStorage.setItem('sidebarState', 'closed');
                }
            });
        });
    }

    // Inicializar layout responsivo
    handleResponsiveLayout();
    
    // Actualizar layout cuando cambia el tamaño de ventana
    window.addEventListener('resize', handleResponsiveLayout);
    
    // Activar transiciones después de carga inicial para evitar animaciones no deseadas
    setTimeout(() => {
        sidebar.classList.add('transitions-enabled');
        container.classList.add('transitions-enabled');
    }, 100);
});