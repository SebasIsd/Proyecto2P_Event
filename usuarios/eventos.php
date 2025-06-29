<?php
require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

$sql = "SELECT * FROM eventos_cursos ORDER BY fec_ini_eve_cur ASC";
$result = pg_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cursos </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary: #6c1313;
      --hover: #5a0f0f;
    }

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
    }

    .course-box:hover {
      transform: scale(1.03);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .course-details {
      padding: 20px;
    }

    .course-details h5 {
      color: var(--primary);
      font-weight: 700;
    }

    .btn-inscribirse {
      background-color: var(--primary);
      border: none;
      color: white;
      padding: 6px 16px;
      border-radius: 20px;
      text-transform: uppercase;
      font-size: 0.9rem;
    }

    .btn-inscribirse:hover {
      background-color: var(--hover);
    }
  </style>
</head>
<body>

  <div class="container py-5">
    <div class="row g-4">

      <?php while ($row = pg_fetch_assoc($result)): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
          <div class="course-box h-100">
            <div class="course-details">
              <h5><?= htmlspecialchars($row['tit_eve_cur']) ?></h5>
              <p><?= htmlspecialchars($row['des_eve_cur']) ?></p>
              <p><strong>Inicio:</strong> <?= date('d/m/Y', strtotime($row['fec_ini_eve_cur'])) ?></p>
              <p><strong>Fin:</strong> <?= date('d/m/Y', strtotime($row['fec_fin_eve_cur'])) ?></p>
              <p><strong>Modalidad:</strong> <?= htmlspecialchars($row['mod_eve_cur']) ?></p>
              <p><strong>Costo:</strong> $<?= number_format($row['cos_eve_cur'], 2) ?></p>
              <a href="#" class="btn btn-inscribirse mt-2">Inscribirse</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
