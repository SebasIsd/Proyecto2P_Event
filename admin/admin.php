<?php
session_start();
require_once('../includes/conexion1.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio Administrador</title>
  <link rel="stylesheet" href="../styles/css/estilosAdmin.css">
  <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="stylesheet" href="../styles/css/componente.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
</head>
<body>
   <?php include '../includes/headeradmin.php'?>


  <main class="admin-panel">

     <div class="admin-card">
      <i class="fas fa-calendar-plus"></i>
      <a href="../admin/ingresoEventos.php">Crear Evento/Curso</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-list"></i>
      <a href="../admin/eventos.php">Ver / Editar Eventos</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-user-check"></i>
      <a href="../admin/inscripciones.php">Ver Inscripciones</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-money-check-alt"></i>
      <a href="../admin/ValidarPago.html">Gestión de Pagos</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-graduation-cap"></i>
      <a href="../admin/notas.php">Notas y Asistencias</a>
      </div>  
      <div class="admin-card">
      <i class="fas fa-check-circle"></i>
      <a href="../admin/verificacionRequisitos.php">Validar requisitos adicionales</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-certificate"></i>
      <a href="../admin/generarCertificado.php">Certificados</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-users-cog"></i>
      <a href="../admin/AdminUsuario.php">Gestión de Usuarios</a>
    </div>
    <div class="admin-card">
      <i class="fa-solid fa-id-card"></i>
      <a href="../admin/administrarInicio.php">Actualizar Inicio</a>
    </div>
    <!--<div class="admin-card">
      <i class="fas fa-chart-bar"></i>
      <a href="https://sdsnt2003.atlassian.net/servicedesk/customer/portal/36">Solicitud de Cambios</a>
    </div>-->
  </main>

  <?php include '../includes/footeradmin.php'?>

</body>

</html>

