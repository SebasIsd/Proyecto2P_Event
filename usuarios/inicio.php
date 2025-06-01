<?php
session_start();

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

$nombre_usuario = 'Usuario'; // Valor por defecto

$sql = "SELECT nom_pri_usu FROM usuarios WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, [$_SESSION['cedula']]);

if ($datos = pg_fetch_assoc($result)) {
    $nombre_usuario = $datos['nom_pri_usu'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header>
            <div class="container">
                <div class="logo">
                <h1>Bienvenido, <span><?= htmlspecialchars($nombre_usuario) ?></span></h1>

                </div>
                <nav>
        <ul>
            <li><a href="inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="mis_eventos.php"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
            <li><a href="./inscripciones/inscripciones.php"><i class="fas fa-edit"></i> Inscripciones</a></li>
            <li><a href="../usuarios/SolicitudesCambios/solicitudCambios.html"><i class="fas fa-chart-bar"></i> Solicitudes de Cambios</a></li>
            <li class="profile-link">
                <a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
            </li>
            <li><a href="./logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </nav>
            </div>
        </header>

    <main class="container">
        <section class="hero">
            <h2>Bienvenido al Sistema de Inscripciones</h2>
            <p>Gestiona eventos, cursos y participantes de manera eficiente</p>
            <a href="../usuarios/inscripciones/inscripciones.php" class="btn-primary">Nueva Inscripción</a>
        </section>  
        <section class="carousel-section">
            <div class="carousel-container">
                <div class="carousel-slide">
                    <img src="https://onsitevents.com/wp-content/uploads/2023/12/webinar-para-empresas.jpeg" alt="Evento 1">
                    <div class="carousel-caption">Participa en nuestros eventos académicos</div>
                </div>
                <div class="carousel-slide">
                    <img src="https://www.ucr.ac.cr/medios/fotos/2024/rs179490_af7d0091-66f70d1a838e8.jpg" alt="Evento 2">
                    <div class="carousel-caption">Cursos disponibles para tu carrera</div>
                </div>
                <div class="carousel-slide">
                    <img src="https://www.ibero.edu.co/sites/default/files/inline-images/habilidades-academicas-para-la-u.jpg" alt="Evento 3">
                    <div class="carousel-caption">Mejora tus habilidades con nosotros</div>
                </div>
                
                <button class="carousel-btn prev-btn" onclick="moveSlide(-1)">&#10094;</button>
                <button class="carousel-btn next-btn" onclick="moveSlide(1)">&#10095;</button>
            </div>
            
            <div class="carousel-dots">
                <span class="dot active" onclick="currentSlide(1)"></span>
                <span class="dot" onclick="currentSlide(2)"></span>
                <span class="dot" onclick="currentSlide(3)"></span>
            </div>
        </section>

        <style>
            .carousel-section {
                margin: 2rem 0;
                position: relative;
            }
            
            .carousel-container {
                max-width: 800px;
                margin: 0 auto;
                position: relative;
                overflow: hidden;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            }
            
            .carousel-slide {
                display: none;
                width: 100%;
                position: relative;
            }
            
            .carousel-slide img {
                width: 100%;
                height: auto;
                display: block;
            }
            
            .carousel-slide.active {
                display: block;
                animation: fadeIn 0.5s;
            }
            
            .carousel-caption {
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(108, 19, 19, 0.7);
                color: white;
                padding: 1rem;
                text-align: center;
                font-weight: 600;
            }
            
            .carousel-btn {
                position: absolute;
                top: 50%;
                transform: translateY(-50%);
                background: rgba(108, 19, 19, 0.5);
                color: white;
                border: none;
                padding: 1rem;
                cursor: pointer;
                font-size: 1.5rem;
                transition: background 0.3s;
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .carousel-btn:hover {
                background: rgba(108, 19, 19, 0.8);
            }
            
            .prev-btn {
                left: 20px;
            }
            
            .next-btn {
                right: 20px;
            }
            
            .carousel-dots {
                text-align: center;
                margin-top: 1rem;
            }
            
            .dot {
                display: inline-block;
                width: 12px;
                height: 12px;
                margin: 0 5px;
                background-color: #bbb;
                border-radius: 50%;
                cursor: pointer;
                transition: background 0.3s;
            }
            
            .dot.active {
                background-color: var(--primary-color);
            }
            
            @keyframes fadeIn {
                from {opacity: 0.4}
                to {opacity: 1}
            }
        </style>

        <script>
            let slideIndex = 1;
            showSlides(slideIndex);
            
            function moveSlide(n) {
                showSlides(slideIndex += n);
            }
            
            function currentSlide(n) {
                showSlides(slideIndex = n);
            }
            
            function showSlides(n) {
                let i;
                let slides = document.getElementsByClassName("carousel-slide");
                let dots = document.getElementsByClassName("dot");
                
                if (n > slides.length) {slideIndex = 1}
                if (n < 1) {slideIndex = slides.length}
                
                for (i = 0; i < slides.length; i++) {
                    slides[i].classList.remove("active");
                }
                
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }
                
                slides[slideIndex-1].classList.add("active");
                dots[slideIndex-1].className += " active";
            }
            
            // Auto slide change every 5 seconds
            setInterval(() => {
                moveSlide(1);
            }, 4000);
        </script>


    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre el Sistema</h3>
                    <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p>
                    <p><i class="fas fa-phone"></i> +123 456 7890</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="#">Inicio</a></li>
                        <li><a href="#">Eventos</a></li>
                        <li><a href="#">Políticas</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Sistema de Inscripciones. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>
</body>
</html>
