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
  <div class="header-container">
    <div class="logo-container">
      <img src="../images/logo.jpg" alt="Logo FISEI">
      <div class="logo-text">
        <h1>FACULTAD DE INGENIERÍA EN SISTEMAS, ELECTRÓNICA E INDUSTRIAL</h1>
        <p>UNIVERSIDAD TÉCNICA DE AMBATO</p>
      </div>
    </div>
    <div class="user-greeting">
      Bienvenido, <?= htmlspecialchars($nombre_usuario) ?> 👋
    </div>
  </div>

</header>
