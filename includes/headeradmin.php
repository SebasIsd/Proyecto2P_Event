<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/conexion1.php';

$conexion = new Conexion();
$conn = $conexion->getConexion();

$nombre_usuario = 'Usuario';

$sql = "SELECT nom_pri_usu FROM usuarios WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, [$_SESSION['cedula']]);

if ($datos = pg_fetch_assoc($result)) {
    $nombre_usuario = $datos['nom_pri_usu'];
}
?>

<header class="main-header">
  <div class="logo-nombre">
    <h1>Bienvenido, Administrador 👋</h1>
  </div>
  <nav>
    <ul>
      <li><a href="admin.php"><i class="fas fa-home"></i> Inicio</a></li>
      <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
    </ul>
  </nav>
</header>