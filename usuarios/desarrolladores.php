<?php
include '../includes/conexion1.php';

$conexion = new Conexion();
$db = $conexion->getConexion();

// Contacto (solo un registro)
$contacto = null;
$consulta3 = "SELECT * FROM contacto LIMIT 1";
$resultado3 = pg_query($db, $consulta3);
if ($resultado3) {
    $contacto = pg_fetch_assoc($resultado3);
}

// Obtener desarrolladores de la base de datos
$desarrolladores = [];
$consultaDesarrolladores = "SELECT * FROM desarrolladores ORDER BY id";
$resultadoDesarrolladores = pg_query($db, $consultaDesarrolladores);
if ($resultadoDesarrolladores) {
    $desarrolladores = pg_fetch_all($resultadoDesarrolladores);
}

$conexion->cerrar();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuestro Equipo de Desarrollo | FISEI</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8B0000;
            --secondary: #D32F2F;
            --accent: #FF5722;
            --light: #F5F5F5;
            --dark: #212121;
            --gray: #757575;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Montserrat', sans-serif;
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

        /* Sección de desarrolladores */
        .developers-section {
            padding: 4rem 0;
            background-color: white;
        }

        .developers-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            color: var(--primary);
            font-size: 2.2rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background-color: var(--accent);
            margin: 1rem auto 0;
        }

        .section-description {
            text-align: center;
            max-width: 800px;
            margin: 0 auto 3rem;
            color: var(--gray);
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .developers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
        }

        .developer-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            transition: var(--transition);
            text-align: center;
            padding-bottom: 1.5rem;
        }

        .developer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .developer-image {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid var(--light);
            margin: -75px auto 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            display: block;
        }

        .developer-header {
            background-color: var(--primary);
            height: 100px;
            position: relative;
            margin-bottom: 75px;
        }

        .developer-name {
            margin: 0 0 0.5rem;
            color: var(--dark);
            font-size: 1.4rem;
        }

        .developer-role {
            color: var(--secondary);
            font-weight: 500;
            margin-bottom: 1.5rem;
            font-size: 1rem;
        }

        .developer-bio {
            color: var(--gray);
            line-height: 1.6;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
        }

        .developer-skills {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
        }

        .skill-tag {
            background-color: var(--light);
            color: var(--dark);
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--light);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            text-decoration: none;
        }

        .social-link:hover {
            background-color: var(--primary);
            color: white;
            transform: translateY(-3px);
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

            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header con logo UTA/FISEI -->
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

    <!-- Barra de navegación -->
    <nav class="nav-bar">
        <div class="nav-container">
            <a href="../index.php" class="nav-link">Inicio</a>
            <a href="eventos.php" class="nav-link">Eventos</a>
            <a href="login.php" class="nav-link">Inscripciones</a>
            <a href="#" class="nav-link active">Contacto</a>
        </div>
    </nav>

    <!-- Sección de desarrolladores -->
    <section class="developers-section">
        <div class="developers-container">
            <h2 class="section-title">Nuestro Equipo de Desarrollo</h2>
            <p class="section-description">
                Conoce al talentoso equipo detrás del sistema de gestión de eventos de la FISEI.
                Un grupo de profesionales comprometidos con la innovación y la excelencia tecnológica.
            </p>

            <div class="developers-grid">
                <?php if (!empty($desarrolladores)): ?>
                    <?php foreach ($desarrolladores as $dev): ?>
                        <div class="developer-card">
                            <div class="developer-header"></div>
                            <img src="<?= htmlspecialchars($dev['imagen_url'] ?? '../imagenes/placeholder.jpg') ?>" alt="Imagen de <?= htmlspecialchars($dev['nombre']) ?>" class="developer-image">
                            <h3 class="developer-name"><?= htmlspecialchars($dev['nombre']) ?></h3>
                            <p class="developer-role"><?= htmlspecialchars($dev['cargo']) ?></p>
                            <p class="developer-bio">
                                <?= nl2br(htmlspecialchars($dev['descripcion'])) ?>
                            </p>
                            <div class="developer-skills">
                                <?php
                                $habilidades = explode(',', $dev['habilidades']);
                                foreach ($habilidades as $habilidad) {
                                    echo '<span class="skill-tag">' . htmlspecialchars(trim($habilidad)) . '</span>';
                                }
                                ?>
                            </div>
                            <div class="social-links">
                                <?php if (!empty($dev['github_url'])): ?>
                                    <a href="<?= htmlspecialchars($dev['github_url']) ?>" class="social-link" target="_blank"><i class="fab fa-github"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($dev['linkedin_url'])): ?>
                                    <a href="<?= htmlspecialchars($dev['linkedin_url']) ?>" class="social-link" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                                <?php endif; ?>
                                <?php if (!empty($dev['email'])): ?>
                                    <a href="mailto:<?= htmlspecialchars($dev['email']) ?>" class="social-link"><i class="fas fa-envelope"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; width: 100%;">No se encontraron desarrolladores en la base de datos.</p>
                <?php endif; ?>
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
                    <li><a href="../index.php">Inicio</a></li>
                    <li><a href="eventos.php">Eventos</a></li>
                    <li><a href="login.php">Inscripciones</a></li>
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
            <p>&copy; 2023 Facultad de Ingeniería en Sistemas, Electrónica e Industrial - Universidad Técnica de Ambato</p>
        </div>
    </footer>
</body>
</html>
