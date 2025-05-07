const heroWave = document.getElementById('hero-wave');

window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;

    if (scrollTop > 100) {
        // Oculta el SVG al hacer scroll
        heroWave.style.opacity = '0';
        heroWave.style.pointerEvents = 'none'; // Por si flota sobre contenido
    } else {
        // Muestra el SVG si volvés al tope
        heroWave.style.opacity = '1';
        heroWave.style.pointerEvents = 'auto';
    }
});

