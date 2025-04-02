const menuIcon = document.getElementById('menu-icon');
const dashboard = document.getElementById('dashboard');

// Agregar un evento de clic al ícono de menú
menuIcon.addEventListener('click', () => {
    // Alternar la clase "active" en el dashboard
    dashboard.classList.toggle('active');
});