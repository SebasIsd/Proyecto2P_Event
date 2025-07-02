<?php
session_start();

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

$nombre_usuario = 'Usuario';

$sql = "SELECT nom_pri_usu FROM usuarios WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, [$_SESSION['cedula']]);

if ($datos = pg_fetch_assoc($result)) {
    $nombre_usuario = $datos['nom_pri_usu'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard | Sistema de Inscripciones</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../styles/css/componente.css">


  <!-- Estilos personalizados para tarjetas de eventos -->
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Segoe UI', sans-serif;
    }
    .course-box {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
      padding: 1rem;
      height: 100%;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .course-box:hover {
      transform: scale(1.03);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .course-details h5 {
      color: #6c1313;
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .btn-inscribirse {
      background-color: #6c1313;
      border: none;
      color: white;
      padding: 0.5rem 1.25rem;
      border-radius: 20px;
      text-transform: uppercase;
      font-size: 0.9rem;
      cursor: pointer;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      margin-top: 1rem;
      transition: background-color 0.3s ease;
    }
    .btn-inscribirse:hover {
      background-color: #5a0f0f;
      color: white;
      text-decoration: none;
    }
  </style>
</head>
<body>
<?php include "../includes/header.php"; ?>

<main class="container py-4">



<section>
  <h3 class="text-center mb-4">Eventos destacados</h3>

  <form method="get" class="mb-4 d-flex justify-content-center">
    <input type="text" name="buscar" class="form-control w-50 me-2" placeholder="Buscar eventos por nombre..." value="<?= isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : '' ?>">
    <button type="submit" class="btn btn-outline-dark">Buscar</button>
  </form>

  <?php include "eventos.php"; ?>
</section>


</main>

<?php include "../includes/footer.php"; ?>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
