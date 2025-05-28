

<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: usuarios/login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

$cedula = $_SESSION['cedula']; 

// Obtener más datos del usuario para el perfil completo
$sql = "SELECT nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, car_usu, 
               cor_usu, tel_usu, dir_usu, fec_nac_usu
        FROM usuarios 
        WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, array($cedula));

if ($usuario = pg_fetch_assoc($result)) {
    $nombreCompleto = $usuario['nom_pri_usu'] . ' ' . $usuario['nom_seg_usu'] . ' ' . $usuario['ape_pri_usu'] . ' ' . $usuario['ape_seg_usu'];
    $carrera = $usuario['car_usu'];
    $fechaNacimiento = !empty($usuario['fec_nac_usu']) ? date('d/m/Y', strtotime($usuario['fec_nac_usu'])) : 'No especificada';
} else {
    $nombreCompleto = "Usuario desconocido";
    $carrera = "Carrera no disponible";
    $fechaNacimiento = 'No disponible';
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mi Perfil - Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/perfil.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <h1>Sistema de Inscripciones</h1>
            </div>
            <nav>
                <ul>
                    <li><a href="inicio.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="#"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
                    <li><a href="/usuarios/inscripciones/inscripciones.html"><i class="fas fa-edit"></i> Inscripciones</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> Usuarios</a></li>
                    <li><a href="/usuarios/SolicitudesCambios/solicitudCambios.html"><i class="fas fa-chart-bar"></i> Solicitudes</a></li>
                    <li class="profile-link">
                        <a href="perfil.php" class="active"><i class="fas fa-user-circle"></i> Perfil</a>
                    </li>
                    <li><a href="/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
     <section class="profile-section">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-info">
                <h1 class="profile-name"><?= htmlspecialchars($nombreCompleto) ?></h1>
                <p class="profile-title"><?= htmlspecialchars($carrera) ?></p>
            </div>
            <a href="editar_perfil.php" class="profile-edit-btn">
                <i class="fas fa-user-edit"></i> Editar Perfil
            </a>
        </div>
        
        <div class="profile-details">
            <div class="profile-detail-section">
                <h3><i class="fas fa-id-card"></i> Información Personal</h3>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($fechaNacimiento) ?></p>
            </div>
            
            <div class="profile-detail-section">
                <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
                <p><strong>Carrera:</strong> <?= htmlspecialchars($carrera) ?></p>
            </div>
            
            <div class="profile-detail-section">
                <h3><i class="fas fa-address-book"></i> Contacto</h3>
                <p><strong>Email:</strong> <?= !empty($usuario['cor_usu']) ? htmlspecialchars($usuario['cor_usu']) : 'No especificado' ?></p>
                <p><strong>Teléfono:</strong> <?= !empty($usuario['tel_usu']) ? htmlspecialchars($usuario['tel_usu']) : 'No especificado' ?></p>
                <p><strong>Dirección:</strong> <?= !empty($usuario['dir_usu']) ? htmlspecialchars($usuario['dir_usu']) : 'No especificada' ?></p>
            </div>
        </div>
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
                        <li><a href="inicio.php">Inicio</a></li>
                        <li><a href="perfil.php">Mi Perfil</a></li>
                        <li><a href="#">Políticas</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Sistema de Inscripciones. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script src="/styles/script.js"></script>
</body>
</html>