<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inicio Administrador</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <style>
    
    :root {
    --primary: #8B0000;
    --secondary: #C62828;
    --accent:rgb(143, 11, 11);
    --light: #FAFAFA;
    --dark: #1C1C1C;
    --gray: #9E9E9E;
    --card-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
    --radius: 12px;
}

body {
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    background-color: var(--light);
    color: var(--dark);
}

/* HEADER */
.header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: #fff;
    padding: 1rem 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.header-container {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0 2rem;
    flex-wrap: wrap;
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.logo-container img {
    height: 60px;
    max-width: 100%;
    border-radius: var(--radius);
    box-shadow: 0 0 10px rgba(255,255,255,0.2);
}

.logo-text h1 {
    font-size: 1.2rem;
    margin: 0;
    font-weight: 600;
}

.logo-text p {
    font-size: 0.85rem;
    margin: 0;
    color: #f1f1f1;
    opacity: 0.9;
}

/* MAIN CARDS */
.admin-panel {
    max-width: 1200px;
    margin: 2rem auto;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.5rem;
    padding: 0 2rem;
}

.admin-card {
    background-color: #fff;
    padding: 2rem 1rem;
    text-align: center;
    border-radius: var(--radius);
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 200px;
}

.admin-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.15);
}

.admin-card i {
    font-size: 2.5rem;
    color: var(--primary);
    margin-bottom: 1rem;
}

.admin-card a {
    margin-top: 0.5rem;
    text-decoration: none;
    color: var(--dark);
    font-weight: 600;
    transition: color 0.3s ease;
    font-size: 1rem;
}

.admin-card a:hover {
    color: var(--accent);
}

/* FOOTER */
footer {
    background-color: var(--dark);
    color: white;
    padding: 2.5rem 1.5rem 1rem;
    margin-top: 4rem;
}

.footer-content {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    max-width: 1200px;
    margin: 0 auto;
    gap: 2rem;
}

.footer-section {
    flex: 1 1 250px;
}

.footer-section h3 {
    font-size: 1.1rem;
    margin-bottom: 1rem;
    color: var(--accent);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.footer-section p,
.footer-section li {
    font-size: 0.95rem;
    line-height: 1.6;
    color: #ddd;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section li a {
    text-decoration: none;
    color: #ccc;
    transition: color 0.3s;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

.footer-section li a:hover {
    color: var(--accent);
}

.footer-bottom {
    text-align: center;
    padding-top: 1.5rem;
    font-size: 0.9rem;
    color: #aaa;
    border-top: 1px solid #444;
    margin-top: 2rem;
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
      <a href="../admin/indexEvento.html">Crear Evento/Curso</a>
    </div>
    <div class="admin-card">
      <i class="fas fa-list"></i>
      <a href="../admin/verEventos.html">Ver / Editar Eventos</a>
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
      <a href="../admin/notasAsistencia.html">Notas y Asistencias</a>
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
