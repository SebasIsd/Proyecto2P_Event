<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

//se usa la cedula 
$cedula = $_SESSION['cedula']; 

$sql = "SELECT nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, car_usu 
        FROM usuarios 
        WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, array($cedula));

if ($usuario = pg_fetch_assoc($result)) {
    $nombreCompleto = $usuario['nom_pri_usu'] . ' ' . $usuario['ape_pri_usu'];
    $carrera = $usuario['car_usu'];
} else {
    $nombreCompleto = "Usuario desconocido";
    $carrera = "Carrera no disponible";
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
</head>
<body>
        <header>
            <div class="container">
                <div class="logo">
                    <h1>Bienvenido, <span><?= htmlspecialchars($nombreCompleto) ?></span></h1>
                    <p style="font-size: 14px; color: #ccc;">
                        Carrera: <?= htmlspecialchars($carrera) ?>
                    </p>
                </div>
                <nav>
        <ul>
            <li><a href="inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
            <li><a href="/usuarios/inscripciones/inscripciones.html"><i class="fas fa-edit"></i> Inscripciones</a></li>
            <li><a href="#"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="/usuarios/SolicitudesCambios/solicitudCambios.html"><i class="fas fa-chart-bar"></i> Solicitudes de Cambios</a></li>
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
            <a href="inscripciones.html" class="btn-primary">Nueva Inscripción</a>
        </section>

        <section class="dashboard">
            <div class="card">
                <i class="fas fa-users"></i>
                <h3>Usuarios Registrados</h3>
                <p id="total-usuarios">0</p>
            </div>
            <div class="card">
                <i class="fas fa-calendar-alt"></i>
                <h3>Eventos Activos</h3>
                <p id="total-eventos">0</p>
            </div>
            <div class="card">
                <i class="fas fa-file-alt"></i>
                <h3>Inscripciones</h3>
                <p id="total-inscripciones">0</p>
            </div>
        </section>
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

    <script src="styles/script.js"></script>
</body>
</html>
