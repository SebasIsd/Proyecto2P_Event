<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio Administrador</title>
  <link rel="stylesheet" href="../styles/css/estilosAdmin.css">
  <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
            :root {
            --primary: #8B0000; /* Rojo FISEI más oscuro */
            --secondary: #D32F2F; /* Rojo más claro */
            --accent: #FF5722; /* Naranja para acentos */
            --light: #F5F5F5;
            --dark: #212121;
            --gray: #757575;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--light);
            color: var(--dark);
        }
    /* Header estilo UTA */
        .header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .logo-container img {
            height: 60px;
        }
        
        .logo-text h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 500;
        }
        
        .logo-text p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        /* Barra de navegación */
        .nav-bar {
            background-color: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }
        
        .nav-link {
            padding: 1rem 1.5rem;
            color: var(--dark);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            border-bottom: 3px solid transparent;
        }
        
        .nav-link:hover, .nav-link.active {
            color: var(--primary);
            border-bottom: 3px solid var(--accent);
        }
  </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <div class="logo-container">
                <img src="https://images.seeklogo.com/logo-png/27/2/uta-logo-png_seeklogo-272349.png" alt="Logo FISEI">
                <div class="logo-text">
                    <h1>FACULTAD DE INGENIERÍA EN SISTEMAS, ELECTRÓNICA E INDUSTRIAL</h1>
                    <p>UNIVERSIDAD TÉCNICA DE AMBATO</p>
                </div>
            </div>
        </div>
    </header>

  <main class="admin-panel">
     <div class="admin-card">
      <i class="fas fa-calendar-plus"></i>
      <a href="../admin/ingresoEventos.php">Crear Evento/Curso</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-list"></i>
      <a href="../admin/eventos.html">Ver / Editar Eventos</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-user-check"></i>
      <a href="../admin/verInscripciones.html">Ver Inscripciones</a>
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
      <a href="../admin/certificados.html">Certificados</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-users-cog"></i>
      <a href="../admin/AdminUsuario.html">Gestión de Usuarios</a>
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

  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3><i class="fas fa-info-circle"></i> Sobre el Sistema</h3>
          <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
        </div>
        <div class="footer-section">
          <h3><i class="fas fa-envelope"></i> Contacto</h3>
          <p><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</p>
          <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p>
          <p><i class="fas fa-phone"></i> +123 456 7890</p>
        </div>
        <div class="footer-section">
          <h3><i class="fas fa-link"></i> Enlaces Rápidos</h3>
          <ul>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Inicio</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Eventos</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Políticas</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2023 Sistema de Inscripciones. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</body>
</html>