const heroWave = document.getElementById('hero-wave');

window.addEventListener('scroll', () => {
    const scrollTop = window.scrollY;

    if (scrollTop > 100) {
        heroWave.style.opacity = '0';
        heroWave.style.pointerEvents = 'none';
    } else {
        heroWave.style.opacity = '1';
        heroWave.style.pointerEvents = 'auto';
    }
});

