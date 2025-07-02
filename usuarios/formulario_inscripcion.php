<?php
session_start();

// Redirige si no hay sesión
if (empty($_SESSION['usuario']) || empty($_SESSION['cedula'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

$id_evento = isset($_GET['id_evento']) ? (int)$_GET['id_evento'] : 0;
$cedula = $_SESSION['cedula'];

// Obtener datos del evento
$sql = "SELECT * FROM eventos_cursos WHERE id_eve_cur = $1";
$result = pg_query_params($conn, $sql, [$id_evento]);
$evento = pg_fetch_assoc($result);

if (!$evento) {
    echo "Evento no encontrado.";
    exit();
}

// Obtener datos del usuario
$sql_usu = "SELECT nom_pri_usu, ape_pat_usu, correo_usu FROM usuarios WHERE ced_usu = $1";
$result_usu = pg_query_params($conn, $sql_usu, [$cedula]);
$usuario = pg_fetch_assoc($result_usu);

if (!$usuario) {
    echo "Usuario no encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inscripción al Evento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container py-5">
    <div class="card shadow p-4">
      <h3 class="mb-4">Confirmar inscripción</h3>

      <h5>Datos del evento</h5>
      <ul>
        <li><strong>Título:</strong> <?= htmlspecialchars($evento['tit_eve_cur']) ?></li>
        <li><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($evento['fec_ini_eve_cur'])) ?> al <?= date('d/m/Y', strtotime($evento['fec_fin_eve_cur'])) ?></li>
        <li><strong>Modalidad:</strong> <?= htmlspecialchars($evento['mod_eve_cur']) ?></li>
        <li><strong>Costo:</strong> $<?= number_format($evento['cos_eve_cur'], 2) ?></li>
      </ul>

      <h5 class="mt-4">Tus datos</h5>
      <ul>
        <li><strong>Nombre:</strong> <?= htmlspecialchars($usuario['nom_pri_usu']) . ' ' . htmlspecialchars($usuario['ape_pat_usu']) ?></li>
        <li><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></li>
        <li><strong>Correo:</strong> <?= htmlspecialchars($usuario['correo_usu']) ?></li>
      </ul>

      <form method="post" action="procesar_inscripcion.php">
        <input type="hidden" name="id_evento" value="<?= $evento['id_eve_cur'] ?>">
        <input type="hidden" name="cedula" value="<?= htmlspecialchars($cedula) ?>">

        <button type="submit" class="btn btn-primary mt-3">Confirmar inscripción</button>
        <a href="index.php" class="btn btn-secondary mt-3 ms-2">Cancelar</a>
      </form>
    </div>
  </div>
</body>
</html>
