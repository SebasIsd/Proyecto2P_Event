<?php
include './includes/conexion1.php';

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FISEI | Gestión de Eventos</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            /*background: url('https://fisei.uta.edu.ec/wp-content/uploads/2021/05/fachada-fisei.jpg') center/cover;*/
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
        
        /* Sección de estadísticas */
        .stats-section {
            background-color: white;
            padding: 3rem 0;
        }
        
        .stats-container {
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
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            padding: 2rem;
            text-align: center;
            transition: var(--transition);
            border-top: 4px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 0.5rem 0;
        }
        
        .stat-label {
            color: var(--gray);
            font-size: 1.1rem;
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
        
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        /* Añade esto a tu CSS */
        .evento-moderno {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }

        .evento-moderno:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .evento-header {
            background-color: var(--primary);
            color: white;
            padding: 1rem;
        }

        .evento-header h4 {
            margin: 0;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .evento-body {
            padding: 1.5rem;
        }

        .evento-body p {
            margin: 0.5rem 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .evento-descripcion {
            color: var(--dark);
            line-height: 1.5;
            margin-bottom: 1rem !important;
        }

        .evento-footer {
            text-align: right;
            padding: 1rem;
            opacity: 0;
            transition: all 0.3s ease;
            transform: translateY(10px);
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
            
            .banner {
                height: 300px;
            }
            
            .banner h2 {
                font-size: 2rem;
            }
            
            .banner p {
                font-size: 1rem;
            }
        }
        /* Estilos para el carrusel */
        .banner-carousel {
            width: 100%;
            height: 400px;
            position: relative;
        }
        
        .slide {
            height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .slide::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
        }
        
        .slide-content {
            position: relative;
            z-index: 1;
            color: white;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            width: 100%;
        }
        
        /* Estilos para las autoridades */
        .authorities-section {
            padding: 3rem 0;
            background-color: white;
        }
        
        .authorities-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .authorities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .authority-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .authority-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        
        .authority-image {
            height: 250px;
            background-size: cover;
            background-position: center;
        }
        
        .authority-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .authority-info h3 {
            margin: 0 0 0.5rem;
            color: var(--primary);
        }
        
        .authority-info p.position {
            color: var(--secondary);
            font-weight: 500;
            margin-bottom: 1rem;
        }
        
        .authority-info p.description {
            color: var(--dark);
            line-height: 1.6;
        }

        .evento-footer {
            text-align: right;
            padding: 1rem;
            opacity: 0;
            transition: all 0.3s ease;
            transform: translateY(10px);
        }

        .evento-moderno:hover .evento-footer {
            opacity: 1;
            transform: translateY(0);
        }

        .btn-inscribirse {
            background-color: var(--accent);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .btn-inscribirse:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
    <!-- Header con logo UTA/FISEI -->
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <img src="imagenes/evento1.png" alt="Logo FISEI">
                <div class="logo-text">
                    <h1>FACULTAD DE INGENIERÍA EN SISTEMAS, ELECTRÓNICA E INDUSTRIAL</h1>
                    <p>UNIVERSIDAD TÉCNICA DE AMBATO</p>
                </div>
            </div>
        </div>
    </header>

    
    <!-- Barra de navegación -->
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="#" class="nav-link active">Inicio</a>
            <a href="usuarios/eventos.php" class="nav-link">Eventos</a>
            <a href="usuarios/login.php" class="nav-link">Inscripciones</a>
            <a href="usuarios/desarrolladores.php" class="nav-link">Contacto</a>
        </div>
    </nav>
        <!-- Carrusel dinámico -->
        <div class="banner-carousel main-carousel">
            <?php foreach($slides as $slide): ?>
            <div class="slide" style="background-image: url('<?= htmlspecialchars($slide['link_url']) ?>')">
                <div class="slide-content">
                    <h2><?= htmlspecialchars($slide['titulo']) ?></h2>
                    <p><?= htmlspecialchars($slide['descripcion']) ?></p>
                    <a href="<?= htmlspecialchars($slide['imagen_url']) ?>" class="btn">Ver mas</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($sobreNosotros): ?>
        <section class="authorities-section" style="background-color: #fdfdfd;">
            <div class="authorities-container">
                <h2 class="section-title"><?= htmlspecialchars($sobreNosotros['titulo']) ?></h2>
                <div class="authorities-grid">
                    <div class="authority-card">
                        <div class="authority-image" style="background-image: url('<?= htmlspecialchars($sobreNosotros['imagen_url']) ?>'); height: 250px;"></div>
                        <div class="authority-info">
                            <p class="description" style="text-align: justify;"><?= nl2br(htmlspecialchars($sobreNosotros['descripcion'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>

    <!-- Sección de eventos -->
    <section class="events-section" id="eventos">
        <div class="events-container">
            <h2 class="section-title">Próximos Eventos</h2>
            <div class="events-grid" id="proximos-eventos-container">
                <!-- Los eventos se cargarán dinámicamente aquí -->
                <div class="evento-moderno">
                    <div class="event-header">
                        <h3>Cargando eventos...</h3>
                    </div>
                    <div class="event-body">
                        <p>Por favor espera mientras cargamos los próximos eventos.</p>
                    </div>
                    <?php if (!isset($_SESSION['usuario'])): ?>
                    <!-- Modificado para pasar el ID del evento al login si no está logueado -->
                    <a href="usuarios/login.php?redirect=inscripciones.php&evento_id=<?= $evento['codigo'] ?>" class="btn btn-primary">Inscribirse</a>
                    <?php else: ?>
                    <!-- Modificado para pasar el ID del evento directamente a inscripciones.php si está logueado -->
                    <a href="usuarios/inscripciones/inscripciones.php?evento_id=<?= $evento['codigo'] ?>" class="btn btn-primary">Inscribirse</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
<section class="authorities-section">
    <div class="authorities-container">
        <h2 class="section-title">Nuestras Autoridades - Princiales</h2>
        <div class="authorities-grid">
            <?php if (!empty($autoridades)): ?>
                <?php foreach($autoridades as $auth): ?>
                    <div class="authority-card">
                        <div class="authority-image" style="background-image: url('<?= htmlspecialchars($auth['imagen_url']) ?>')"></div>
                        <div class="authority-info">
                            <h3><?= htmlspecialchars($auth['nombre']) ?></h3>
                            <p class="position"><?= htmlspecialchars($auth['cargo']) ?></p>
                            <p class="position"><?= htmlspecialchars($auth['dependencia']) ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay autoridades registradas.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

    
    <!-- Sección de estadísticas -->
    <section class="stats-section">
        <div class="stats-container">
            <h2 class="section-title">Nuestros Números</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value" id="total-usuarios">0</div>
                    <div class="stat-label">Usuarios Registrados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                    <div class="stat-value" id="total-eventos">0</div>
                    <div class="stat-label">Eventos Activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-file-signature"></i></div>
                    <div class="stat-value" id="total-inscripciones">0</div>
                    <div class="stat-label">Inscripciones Realizadas</div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="https://play-lh.googleusercontent.com/EqL3NouatH9jKPfrOdoBrhbL7w0jGSB1czNYxRc5f3oRN8eja0WvsrsYtAmHypGlu4w" alt="Logo FISEI">
                </div>
            <div class="footer-links">
                <h4>Enlaces Rápidos</h4>
                <ul>
                    <li><a href="#">Inicio</a></li>
                    <li><a href="usuarios/eventos.php">Eventos</a></li>
                    <li><a href="usuarios/login.php">Inscripciones</a></li>
                    <li><a href="https://sdsnt2003.atlassian.net/servicedesk/customer/portal/36" target="_blank">Solicitar un Cambio</a></li>
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
            <p>&copy; 2023 Facultad de Ingeniería en Sistemas, Electrónica e Industrial - Universidad Técnica de Ambato</p>
        </div>
    </footer>
    
   <script src="./styles/script.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script>
        // Inicializar carrusel
        $(document).ready(function(){
            $('.main-carousel').slick({
                dots: true,
                infinite: true,
                speed: 400,
                fade: true,
                cssEase: 'linear',
                autoplay: true,
                autoplaySpeed: 5000
            });
        });

       /* // Función para cargar eventos en la sección "Próximos Eventos"
        document.addEventListener('DOMContentLoaded', function() {
            fetch('usuarios/inscripciones/get_eventos_disponibles.php') // Nueva API para eventos públicos
                .then(response => response.json())
                .then(data => {
                    const eventosContainer = document.getElementById('proximos-eventos-container');
                    eventosContainer.innerHTML = ''; // Limpiar el contenido de "Cargando eventos..."

                    if (data.length > 0) {
                        data.forEach(evento => {
                            const eventoCard = `
                                <div class="evento-moderno">
                                    <div class="evento-header">
                                        <h4><i class="fas fa-calendar-alt"></i> ${evento.titulo}</h4>
                                    </div>
                                    <div class="evento-body">
                                        <p class="evento-descripcion">${evento.descripcion}</p>
                                        <p><i class="fas fa-calendar-day"></i> Fecha Inicio: ${new Date(evento.fechaInicio).toLocaleDateString('es-ES')}</p>
                                        <p><i class="fas fa-calendar-times"></i> Fecha Fin: ${new Date(evento.fechaFin).toLocaleDateString('es-ES')}</p>
                                        <p><i class="fas fa-dollar-sign"></i> Costo: ${evento.costo === '0.00' ? 'Gratuito' : '$' + evento.costo}</p>
                                        <p><i class="fas fa-tag"></i> Tipo: ${evento.tipo_evento}</p>
                                    </div>
                                    <div class="evento-footer">
                                        <?php if (!isset($_SESSION['usuario'])): ?>
                                            <a href="usuarios/login.php?redirect=inscripciones.php&evento_id=${evento.codigo}" class="btn btn-inscribirse">Inscribirse</a>
                                        <?php else: ?>
                                            <a href="usuarios/inscripciones/inscripciones.php?evento_id=${evento.codigo}" class="btn btn-inscribirse">Inscribirse</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            `;
                            eventosContainer.innerHTML += eventoCard;
                        });
                    } else {
                        eventosContainer.innerHTML = '<p style="text-align: center; width: 100%;">No hay próximos eventos disponibles.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error al cargar los eventos:', error);
                    document.getElementById('proximos-eventos-container').innerHTML = '<p style="text-align: center; width: 100%; color: red;">Error al cargar los eventos. Intente de nuevo más tarde.</p>';
                });
        });*/
    </script>
</body>
</html>
