document.addEventListener('DOMContentLoaded', function() {
    // Simular datos del dashboard (en un sistema real estos vendrían de la API)
    setTimeout(() => {
        document.getElementById('total-usuarios').textContent = '124';
        document.getElementById('total-eventos').textContent = '8';
        document.getElementById('total-inscripciones').textContent = '76';
    }, 500);

    // Navegación activa
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    const navLinks = document.querySelectorAll('nav ul li a');
    
    navLinks.forEach(link => {
        const linkPage = link.getAttribute('href');
        if (currentPage === linkPage) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
});