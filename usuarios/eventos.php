<?php
session_start(); // Add this line at the very top
include '../includes/conexion1.php';

$conexion = new Conexion();
$db = $conexion->getConexion();

// Carrusel
$slides = [];
$consulta = "SELECT * FROM carrusel ORDER BY id";
$resultado = pg_query($db, $consulta);
if ($resultado) {
    while ($fila = pg_fetch_assoc($resultado)) {
        $slides[] = $fila;
    }
}

// Autoridades
$autoridades = [];
$consulta2 = "SELECT * FROM autoridades ORDER BY id";
$resultado2 = pg_query($db, $consulta2);
if ($resultado2) {
    while ($fila2 = pg_fetch_assoc($resultado2)) {
        $autoridades[] = $fila2;
    }
}

// Contacto (solo un registro)
$contacto = null;
$consulta3 = "SELECT * FROM contacto LIMIT 1";
$resultado3 = pg_query($db, $consulta3);
if ($resultado3) {
    $contacto = pg_fetch_assoc($resultado3);
}
// Sobre Nosotros
$sobreNosotros = null;
$resultadoSobre = pg_query($db, "SELECT * FROM sobre_nosotros ORDER BY id DESC LIMIT 1");
if ($resultadoSobre) {
    $sobreNosotros = pg_fetch_assoc($resultadoSobre);
}

$conexion->cerrar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Lista de Eventos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>

    <style>
        :root {
            --primary: #8B0000; /* Rojo FISEI más oscuro */
            --secondary: #D32F2F; /* Rojo más claro */
            --accent: #FF5722; /* Naranja para acentos */
            --light: #F5F5F5;
            --dark: #212121;
            --gray: #757575;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
        }
        
        /* Header estilo UTA */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-container img {
            height: 60px;
        }
        
        .logo-text h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        
        .logo-text p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Barra de navegación */
        .nav-bar {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }
        
        .nav-link {
            padding: 1rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border-bottom: 3px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--accent);
        }
        
        /* Banner principal */
        .banner {
            height: 400px;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
        }
        
        .banner-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 1;
            color: white;
        }
        
        .banner h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            font-weight: 700;
        }
        
        .banner p {
            font-size: 1.2rem;
            max-width: 600px;
            margin-bottom: 2rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            background-color: var(--accent);
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }
        
        .btn:hover {
            background-color: #E64A19;
            transform: translateY(-2px);
        }
        
        /* Sección de búsqueda y filtros */
        .search-section {
            background-color: white;
            padding: 2rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .search-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            justify-content: space-between;
        }
        
        .search-box {
            flex-grow: 1;
            position: relative;
            max-width: 500px;
        }
        
        .search-box input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(139, 0, 0, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }
        
        .filter-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .filter-btn {
            padding: 0.8rem 1.5rem;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
        }
        
        .filter-btn:hover {
            background-color: var(--light);
        }
        
        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        /* Sección de eventos */
        .events-section {
            padding: 3rem 0;
            background-color: var(--light);
        }
        
        .events-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
            font-size: 2rem;
            font-weight: 500;
        }
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .event-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .event-header {
            background-color: var(--primary);
            color: white;
            padding: 1rem;
            position: relative;
        }
        
        .event-header h3 {
            margin: 0;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .event-type {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background-color: var(--accent);
            color: white;
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .event-body {
            padding: 1.5rem;
            flex-grow: 1;
        }
        
        .event-description {
            color: var(--dark);
            line-height: 1.6;
            margin-bottom: 1.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .event-details {
            margin-top: 1rem;
        }
        
        .event-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--gray);
        }
        
        .event-price {
            font-weight: 700;
            color: var(--primary);
        }
        
        .event-footer {
            padding: 1rem;
            text-align: right;
            border-top: 1px solid #eee;
            opacity: 0;
            transform: translateY(10px);
            transition: var(--transition);
        }
        
        .event-card:hover .event-footer {
            opacity: 1;
            transform: translateY(0);
        }
        
        .btn-inscribirse {
            background-color: var(--accent);
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
            display: inline-block;
        }
        
        .btn-inscribirse:hover {
            background-color: #E64A19;
        }
        
        .no-events {
            grid-column: 1 / -1;
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }
        
        /* Footer */
        .footer {
            background-color: var(--dark);
            color: white;
            padding: 3rem 0 1rem;
        }
        
        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .footer-logo img {
            height: 80px;
            margin-bottom: 1rem;
        }
        
        .footer-links h4 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-weight: 500;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-links li {
            margin-bottom: 0.5rem;
        }
        
        .footer-links a {
            color: #BDBDBD;
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-links a:hover {
            color: white;
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid #424242;
            color: #BDBDBD;
            font-size: 0.9rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
            
            .nav-container {
                flex-wrap: wrap;
            }
            
            .search-container {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-box {
                max-width: 100%;
            }
            
            .filter-buttons {
                width: 100%;
                justify-content: space-between;
            }
            
            .filter-btn {
                flex-grow: 1;
                text-align: center;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <img src="../imagenes/evento1.png" alt="Logo FISEI">
                <div class="logo-text">
                    <h1>FACULTAD DE INGENIERÍA EN SISTEMAS, ELECTRÓNICA E INDUSTRIAL</h1>
                    <p>UNIVERSIDAD TÉCNICA DE AMBATO</p>
                </div>
            </div>
        </div>
    </header>

    <nav class="nav-bar">
        <div class="nav-container">
            <a href="../index.php" class="nav-link">Inicio</a>
            <a href="#" class="nav-link active">Eventos</a>
            <a href="usuarios/login.php" class="nav-link">Inscripciones</a>
            <a href="desarrolladores.php" class="nav-link">Contacto</a>
        </div>
    </nav>

    <section class="search-section">
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="search-input" placeholder="Buscar eventos...">
            </div>
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">Todos</button>
                <button class="filter-btn" data-filter="free">Gratis</button>
                <button class="filter-btn" data-filter="paid">Pagados</button>
            </div>
        </div>
    </section>

    <main class="events-section">
        <div class="events-container">
            <h2 class="section-title"><i class="fas fa-calendar-week"></i> Todos los Eventos</h2>
            <div class="events-grid" id="proximos-eventos-container">
                <p>Cargando eventos...</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="https://play-lh.googleusercontent.com/EqL3NouatH9jKPfrOdoBrhbL7w0jGSB1czNYxRc5f3oRN8eja0WvsrsYtAmHypGlu4w" alt="Logo FISEI">
            </div>
            <div class="footer-links">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="#">Eventos</a></li>
                    <li><a href="usuarios/login.php">Inscripciones</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Contacto</h4>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($contacto['direccion'] ?? 'No disponible') ?></li>
                    <li><i class="fas fa-phone"></i> <?= htmlspecialchars($contacto['telefono'] ?? 'No disponible') ?></li>
                    <li><i class="fas fa-envelope"></i> <?= htmlspecialchars($contacto['correo'] ?? 'No disponible') ?></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>© 2023 Facultad de Ingeniería en Sistemas, Electrónica e Industrial - Universidad Técnica de Ambato</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            // Formatear fecha
            function formatearFecha(fecha) {
                if (!fecha) return 'Sin fecha';
                try {
                    const fechaObj = new Date(fecha);
                    return fechaObj.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch {
                    return fecha;
                }
            }

            // Cargar eventos
            let eventos = [];
            
            try {
                const res = await fetch('../conexion/dashboard_eventos.php');
                const data = await res.json();
                eventos = data.proximosEventos || [];
                mostrarEventos(eventos);
            } catch (err) {
                console.error(err);
                document.getElementById('proximos-eventos-container').innerHTML = `
                    <div class="no-events">
                        <p>Error al cargar los eventos. Por favor intenta nuevamente.</p>
                    </div>
                `;
            }

            // Mostrar eventos en el grid
            function mostrarEventos(eventosMostrar) {
                const container = document.getElementById('proximos-eventos-container');
                container.innerHTML = '';

                if (eventosMostrar && eventosMostrar.length > 0) {
                    eventosMostrar.forEach(evento => {
                        const fechaInicio = formatearFecha(evento.fechainicio);
                        const fechaFin = evento.fechafin ? formatearFecha(evento.fechafin) : null;
                        const fechaTexto = (fechaFin && fechaFin !== fechaInicio) ? `${fechaInicio} - ${fechaFin}` : fechaInicio;
                        const esGratis = parseFloat(evento.costo || 0) <= 0;
                        const tipoEvento = esGratis ? 'Gratis' : 'Pagado';
                        
                        const card = document.createElement('div');
                        card.className = 'event-card';
                        card.dataset.tipo = esGratis ? 'free' : 'paid';
                        card.dataset.nombre = evento.titulo ? evento.titulo.toLowerCase() : '';
                        
                        card.innerHTML = `
                            <div class="event-header">
                                <h3><i class="fas fa-calendar-alt"></i> ${evento.titulo || 'Sin título'}</h3>
                                <span class="event-type">${tipoEvento}</span>
                            </div>
                            <div class="event-body">
                                <p class="event-description">${evento.descripcion || 'Sin descripción'}</p>
                                <div class="event-details">
                                    <p class="event-detail"><i class="fas fa-calendar-day"></i> ${fechaTexto}</p>
                                    <p class="event-detail"><i class="fas fa-dollar-sign"></i> Costo: <span class="event-price">$${evento.costo || '0.00'}</span></p>
                                    <p class="event-detail"><i class="fas fa-tag"></i> ${evento.tipo || 'Sin tipo'}</p>
                                    <p class="event-detail"><i class="fas fa-laptop-house"></i> ${evento.modalidad || 'Sin modalidad'}</p>
                                </div>
                            </div>
                            <div class="event-footer">
                                <?php if (!isset($_SESSION['usuario'])): ?>
                                    <a href="login.php?redirect=login.php&evento=${evento.codigo}" class="btn-inscribirse">Inscribirse</a>
                                <?php else: ?>
                                    <a href="login.php?evento=${evento.codigo}" class="btn-inscribirse">Inscribirse</a>
                                <?php endif; ?>
                            </div>
                        `;
                        container.appendChild(card);
                    });
                    
                    animateCards();
                } else {
                    container.innerHTML = `
                        <div class="no-events">
                            <p>No hay eventos disponibles.</p>
                        </div>
                    `;
                }
            }

            // Animación de las tarjetas
            function animateCards() {
                const cards = document.querySelectorAll('.event-card');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        card.style.transition = 'all 0.4s ease';
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, 100 * index);
                });
            }

            // Filtrado de eventos
            const searchInput = document.getElementById('search-input');
            const filterButtons = document.querySelectorAll('.filter-btn');
            
            // Manejar filtros
            filterButtons.forEach(button => {
                button.addEventListener('click', () => {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');
                    filtrarEventos();
                });
            });
            
            // Manejar búsqueda
            searchInput.addEventListener('input', filtrarEventos);
            
            // Función para filtrar eventos
            function filtrarEventos() {
                const searchTerm = searchInput.value.toLowerCase();
                const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;
                
                const eventosFiltrados = eventos.filter(evento => {
                    const titulo = evento.titulo ? evento.titulo.toLowerCase() : '';
                    const esGratis = parseFloat(evento.costo || 0) <= 0;
                    const tipo = esGratis ? 'free' : 'paid';
                    
                    // Aplicar filtro de tipo
                    if (activeFilter !== 'all' && tipo !== activeFilter) {
                        return false;
                    }
                    
                    // Aplicar filtro de búsqueda
                    if (searchTerm && !titulo.includes(searchTerm)) {
                        return false;
                    }
                    
                    return true;
                });
                
                mostrarEventos(eventosFiltrados);
            }
        });
    </script>
</body>
</html>