document.addEventListener('DOMContentLoaded', function () {
    // Función para formatear fechas
    function formatearFecha(fecha) {
        if (!fecha) return 'Sin fecha';
        
        try {
            const fechaObj = new Date(fecha);
            if (isNaN(fechaObj.getTime())) {
                return fecha; // Devolver la fecha original si no se puede parsear
            }
            
            return fechaObj.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (error) {
            return fecha; // Devolver la fecha original si hay error
        }
    }

    // Función para obtener datos del dashboard
    async function fetchDashboardData() {
        try {
            const response = await fetch('./conexion/dashboard.php');
            if (!response.ok) {
                throw new Error('Error al obtener datos');
            }
            const data = await response.json();

            // Actualizar contadores
            document.getElementById('total-usuarios').textContent = data.totalUsuarios || 0;
            document.getElementById('total-eventos').textContent = data.totalEventos || 0;
            document.getElementById('total-inscripciones').textContent = data.totalInscripciones || 0;

            // Mostrar eventos si existen
            const eventosContainer = document.getElementById('proximos-eventos-container');
            eventosContainer.innerHTML = ''; // Limpiar contenido previo

            if (data.proximosEventos && data.proximosEventos.length > 0) {
                // Mostrar solo los primeros 2 eventos
                const eventosVisibles = data.proximosEventos.slice(0, 2);
                eventosVisibles.forEach(evento => {
                    const eventoCard = document.createElement('div');
                    eventoCard.classList.add('evento-moderno');
                    
                    const fechaInicio = formatearFecha(evento.fechainicio);
                    const fechaFin = evento.fechafin ? formatearFecha(evento.fechafin) : null;
                    const fechaTexto = fechaFin && fechaFin !== fechaInicio ? 
                        `${fechaInicio} - ${fechaFin}` : fechaInicio;
                    
                    const descripcionCorta = evento.descripcion && evento.descripcion.length > 80 ? 
                        evento.descripcion.substring(0, 10) + '...' : 
                        (evento.descripcion || 'Sin descripción');
                    
                    // En script.js, modifica la parte donde creas las tarjetas de eventos
                    eventoCard.innerHTML = `
                        <div class="evento-header">
                            <h4><i class="fas fa-calendar-alt"></i> ${evento.titulo || 'Sin título'}</h4>
                        </div>
                        <div class="evento-body">
                            <p class="evento-descripcion"><i class="fas fa-align-left"></i> ${descripcionCorta}</p>
                            <p><i class="fas fa-calendar-day"></i> ${fechaTexto}</p>
                            <p><i class="fas fa-dollar-sign"></i> $${evento.costo || '0.00'}</p>
                            ${evento.tipo ? `<p><i class="fas fa-tag"></i> ${evento.tipo}</p>` : ''}
                            <p><i class="fas fa-laptop-house"></i> ${evento.modalidad === 'Pagado' ? 'Gratis' : evento.modalidad || 'Sin modalidad'}</p>
                        </div>
                        <!-- Cambiar esta parte en el evento-footer -->
                        <div class="evento-footer">
                            <a href="usuarios/login.php?evento=${evento.id_evento || evento.codigo}" class="btn btn-primary">Inscribirse</a>
                        </div>

                    `;
                    eventosContainer.appendChild(eventoCard);
                });
            } else {
                eventosContainer.innerHTML = `
                    <div class="evento-moderno" style="text-align: center; justify-content: center;">
                        <p><i class="fas fa-calendar-times"></i> No hay eventos disponibles en este momento.</p>
                    </div>
                `;
            }


            animateCards();

        } catch (error) {
            console.error('Error al cargar datos:', error);
            
            // Mostrar valores por defecto en caso de error
            document.getElementById('total-usuarios').textContent = 'N/A';
            document.getElementById('total-eventos').textContent = 'N/A';
            document.getElementById('total-inscripciones').textContent = 'N/A';
            
            // Mostrar mensaje de error en eventos
            const eventosContainer = document.getElementById('proximos-eventos-container');
            eventosContainer.innerHTML = `
                <div class="evento-moderno" style="text-align: center; justify-content: center; color: #8B0000;">
                    <p><i class="fas fa-exclamation-triangle"></i> Error al cargar los eventos.</p>
                    <small>Verifique la conexión a la base de datos.</small>
                </div>
            `;
        }
    }

    // Función para animar las tarjetas
    function animateCards() {
        const cards = document.querySelectorAll('.card, .evento-moderno');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100 * index);
        });
    }

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

    // Iniciar carga
    fetchDashboardData();
    
    // Opcional: Recargar datos cada 5 minutos
    setInterval(fetchDashboardData, 300000);
});