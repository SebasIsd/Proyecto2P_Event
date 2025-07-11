<?php
session_start();
require_once('../includes/conexion1.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ver Inscripciones</title>
  <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="stylesheet" href="../styles/css/estilosAdmin.css">
  <link rel="stylesheet" href="../styles/css/estiloInscripciones.css">
  <link rel="stylesheet" href="../styles/css/componente.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<?php include '../includes/headeradmin.php'?>

  <main>
    <section class="admin-panel full-width">
      <h2>Lista de Inscripciones</h2>
      <div class="filtro-inscripciones">
          <input type="text" id="filtroNombre" placeholder="Filtrar por nombre..." onkeyup="filtrarInscripciones()">
      </div>
      <div class="tabla-inscripciones">
        <table>
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre Completo</th>
              <th>Evento</th>
              <th>Fecha Inicio</th>
              <th>Fecha Cierre</th>
              <th>Estado de Pago</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody id="contenedor-inscripciones">
            <!-- Registros dinámicos -->
          </tbody>
        </table>
      </div>
    </section>
  </main>

<?php include '../includes/footeradmin.php'?>

  <script src="../styles/verInscripciones.js"></script>
</body>
</html>

