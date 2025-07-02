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
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | Sistema de Inscripciones</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles/css/componente.css">
</head>
<body>
<?php include "../includes/header.php"; ?>
<main>
  <section class="hero">
    <h2>Gestiona tus cursos y participa en eventos</h2>
    <p>Haz clic para inscribirte fácilmente en nuevos cursos y actividades.</p>
    <a href="../usuarios/inscripciones/inscripciones.php" class="btn-primary">
      <i class="fas fa-plus-circle"></i> Nueva Inscripción
    </a>
  </section>
  <section class="carousel">
    <div class="carousel-slide active">
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
  </section>
</main>
<?php include "../includes/footer.php"; ?>
<script>
  let current = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }
  setInterval(() => {
    current = (current + 1) % slides.length;
    showSlide(current);
  }, 4000);
</script>
</body>
</html>