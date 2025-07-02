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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles/css/componente.css">
  <style>
    /* Estilos generales */
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f5f7fa;
      color: #333;
    }
    
    /* Hero section */
    .hero {
      background: linear-gradient(135deg,rgb(169, 89, 89) 0%,rgb(134, 15, 48) 100%);
      color: white;
      padding: 3rem 2rem;
      text-align: center;
      border-radius: 0 0 20px 20px;
      margin-bottom: 2rem;
    }
    .hero h2 {
      font-size: 2rem;
      margin-bottom: 0.8rem;
      font-weight: 700;
    }
    .hero p {
      font-size: 1.1rem;
      max-width: 700px;
      margin: 0 auto 1.5rem;
      opacity: 0.9;
    }
    .btn-primary {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background-color:rgb(152, 18, 34);
      color: white;
      padding: 0.8rem 1.8rem;
      border-radius: 50px;
      font-size: 1rem;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(210, 226, 231, 0.3);
    }
    .btn-primary:hover {
      background-color:rgb(129, 9, 9);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(176, 206, 215, 0.4);
    }
    
    /* Carousel */
    .carousel {
      width: 100%;
      max-height: 400px;
      overflow: hidden;
      position: relative;
      border-radius: 12px;
      margin: 0 auto 3rem;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    .carousel-slide {
      display: none;
      position: relative;
    }
    .carousel-slide.active {
      display: block;
    }
    .carousel img {
      width: 100%;
      height: 400px;
      object-fit: cover;
    }
    .carousel-caption {
      position: absolute;
      bottom: 30px;
      left: 30px;
      color: white;
      background: rgba(0,0,0,0.6);
      padding: 0.8rem 1.5rem;
      border-radius: 10px;
      max-width: 60%;
      font-size: 1.1rem;
      font-weight: 500;
    }
    
    /* Event section */
    .event-section {
      padding: 2rem;
      max-width: 1200px;
      margin: 0 auto;
    }
    .event-section h3 {
      font-size: 1.8rem;
      color: #2b2d42;
      text-align: center;
      margin-bottom: 2rem;
      position: relative;
      font-weight: 700;
    }
    .event-section h3::after {
      content: '';
      display: block;
      width: 80px;
      height: 4px;
      background: #4361ee;
      margin: 0.8rem auto 0;
      border-radius: 2px;
    }
    .event-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 2rem;
      margin-top: 1.5rem;
    }
    .event-card {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      transition: all 0.3s ease;
      position: relative;
      display: flex;
      flex-direction: column;
    }
    .event-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 12px 25px rgba(0,0,0,0.12);
    }
    .event-image {
      height: 180px;
      overflow: hidden;
    }
    .event-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
    }
    .event-card:hover .event-image img {
      transform: scale(1.05);
    }
    .event-content {
      padding: 1.5rem;
      flex-grow: 1;
      display: flex;
      flex-direction: column;
    }
    .event-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2b2d42;
      margin-bottom: 0.5rem;
      line-height: 1.4;
    }
    .event-meta {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }
    .event-date {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.9rem;
      color: #6c757d;
    }
    .event-type {
      display: inline-flex;
      align-items: center;
      padding: 0.25rem 0.8rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    .event-type.gratuito {
      background: rgba(40, 167, 69, 0.1);
      color: #28a745;
    }
    .event-type.pagado {
      background: rgba(220, 53, 69, 0.1);
      color: #dc3545;
    }
    .event-description {
      color: #495057;
      font-size: 0.95rem;
      line-height: 1.5;
      margin-bottom: 1.5rem;
      flex-grow: 1;
    }
    .event-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding-top: 1rem;
      border-top: 1px solid #e9ecef;
    }
    .event-price {
      font-weight: 700;
      font-size: 1.1rem;
    }
    .event-price.gratuito {
      color: #28a745;
    }
    .event-price.pagado {
      color: #dc3545;
    }
    .event-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #4361ee;
      color: white;
      border: none;
      padding: 0.6rem 1.2rem;
      border-radius: 6px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
      text-decoration: none;
      font-size: 0.9rem;
    }
    .event-btn:hover {
      background: #3a56d4;
      transform: translateY(-2px);
    }
    
    /* Modal mejorado */
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.7);
      z-index: 1000;
      justify-content: center;
      align-items: center;
      opacity: 0;
      transition: opacity 0.3s ease;
      overflow-y: auto;
      padding: 2rem 0;
    }
    .modal.active {
      display: flex;
      opacity: 1;
    }
    .modal-content {
      background: white;
      border-radius: 12px;
      width: 90%;
      max-width: 500px;
      max-height: 90vh;
      box-shadow: 0 15px 30px rgba(0,0,0,0.2);
      transform: translateY(-20px);
      transition: transform 0.3s ease;
      position: relative;
      display: flex;
      flex-direction: column;
    }
    .modal-body {
      padding: 1.5rem;
      overflow-y: auto;
      flex-grow: 1;
    }
    .modal.active .modal-content {
      transform: translateY(0);
    }
    .modal-header {
      padding: 1.5rem;
      border-bottom: 1px solid #e9ecef;
      position: relative;
    }
    .modal-title {
      font-size: 1.5rem;
      color: #2b2d42;
      margin: 0;
      font-weight: 600;
    }
    .modal-close {
      position: absolute;
      top: 1.5rem;
      right: 1.5rem;
      font-size: 1.5rem;
      color: #6c757d;
      cursor: pointer;
      transition: color 0.3s ease;
    }
    .modal-close:hover {
      color: #dc3545;
    }
    .modal-field {
      margin-bottom: 1.2rem;
    }
    .modal-label {
      display: block;
      font-weight: 600;
      color: #343a40;
      margin-bottom: 0.5rem;
      font-size: 0.95rem;
    }
    .modal-input {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ced4da;
      border-radius: 6px;
      background: #f8f9fa;
      font-family: 'Poppins', sans-serif;
      font-size: 0.95rem;
    }
    .modal-footer {
      padding: 1.5rem;
      border-top: 1px solid #e9ecef;
      text-align: right;
    }
    .btn-inscribir {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      background: #28a745;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 6px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      font-size: 1rem;
    }
    .btn-inscribir:hover {
      background: #218838;
      transform: translateY(-2px);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
      .hero {
        padding: 2rem 1rem;
      }
      .hero h2 {
        font-size: 1.6rem;
      }
      .carousel {
        margin-bottom: 2rem;
      }
      .carousel-caption {
        max-width: 80%;
        left: 15px;
        bottom: 15px;
        font-size: 1rem;
      }
      .event-grid {
        grid-template-columns: 1fr;
      }
    }

    .search-bar {
      max-width: 400px;
      margin: 0 auto 2rem;
      text-align: center;
    }
    .search-bar input {
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 30px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    /* Animaciones */
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .event-card {
      animation: fadeIn 0.5s ease forwards;
      opacity: 0;
    }

    /* Retrasos para el efecto cascada */
    .event-card:nth-child(1) { animation-delay: 0.1s; }
    .event-card:nth-child(2) { animation-delay: 0.2s; }
    .event-card:nth-child(3) { animation-delay: 0.3s; }
    .event-card:nth-child(4) { animation-delay: 0.4s; }
    .event-card:nth-child(5) { animation-delay: 0.5s; }
    .event-card:nth-child(n+6) { animation-delay: 0.6s; }

    .loading-spinner {
      display: inline-block;
      width: 40px;
      height: 40px;
      border: 4px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      border-top-color: #4361ee;
      animation: spin 1s ease-in-out infinite;
      margin: 2rem auto;
    }

    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    .loading-container {
      text-align: center;
      padding: 2rem;
    }
  </style>
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

 <section class="event-section">
    <h3>Eventos Disponibles</h3>
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Buscar evento por nombre...">
    </div>
    <div class="event-grid" id="eventosContainer"></div>
  </section>

  <div class="modal" id="modalEvento">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitulo"></h3>
        <span class="modal-close" onclick="cerrarModal()">&times;</span>
      </div>
      <form id="formInscribir" method="POST" action="../usuarios/inscripciones/procesar_inscripcion.php" enctype="multipart/form-data" onsubmit="return validarComprobante()">
      <div class="modal-body">
        <div class="modal-field">
          <label class="modal-label">Descripción:</label>
          <input type="text" class="modal-input" id="modalDescripcion" readonly>
        </div>
        <div class="modal-field">
          <label class="modal-label">Fecha Inicio:</label>
          <input type="text" class="modal-input" id="modalFechaInicio" readonly>
        </div>
        <div class="modal-field">
          <label class="modal-label">Fecha Fin:</label>
          <input type="text" class="modal-input" id="modalFechaFin" readonly>
        </div>
        <div class="modal-field">
          <label class="modal-label">Costo:</label>
          <input type="text" class="modal-input" id="modalCosto" readonly>
        </div>
        <div class="modal-field">
          <label class="modal-label">Modalidad:</label>
          <input type="text" class="modal-input" id="modalTipo" readonly>
        </div>
        <div class="modal-field" id="modalComprobanteDiv" style="display: none;">
          <label class="modal-label">Subir Comprobante (JPG/PNG, máx 2MB):</label>
          <input type="file" class="modal-input" name="comprobante" id="modalComprobante" accept="image/png, image/jpeg">
        </div>
      </div>
      <div class="modal-footer">
          <input type="hidden" name="evento" id="inputEventoId">
          <input type="hidden" name="cedula" value="<?= $_SESSION['cedula'] ?>">
          <input type="hidden" name="fecha_inscripcion" value="<?= date('Y-m-d') ?>">
          <input type="hidden" name="estado_pago" id="estadoPagoInput">
          <button type="submit" class="btn-inscribir">
            <i class="fas fa-check-circle"></i> Confirmar Inscripción
          </button>
      </div>
      </form>
    </div>
  </div>
</main>
<?php include "../includes/footer.php"; ?>
<script>
function formatearFecha(fecha) {
  if (!fecha) return 'Sin fecha definida';
  try {
    const fechaObj = new Date(fecha);
    return fechaObj.toLocaleDateString('es-ES', {
      day: '2-digit',
      month: 'short',
      year: 'numeric'
    });
  } catch {
    return fecha;
  }
}

  function mostrarModal(evento) {
    const modal = document.getElementById('modalEvento');
    const modalContent = modal.querySelector('.modal-content');
    
    // Limpiar estilos previos
    modalContent.style.maxHeight = '90vh';
    
    document.getElementById('modalTitulo').textContent = evento.titulo;
    document.getElementById('modalDescripcion').value = evento.descripcion || 'No disponible';
    document.getElementById('modalFechaInicio').value = formatearFecha(evento.fechaInicio);
    document.getElementById('modalFechaFin').value = formatearFecha(evento.fechaFin);
    
    const esGratuito = evento.tipo_evento.toLowerCase() === 'gratuito';
    const costoTexto = esGratuito ? 'Gratuito' : `$${evento.costo || '0'}`;
    
    document.getElementById('modalCosto').value = costoTexto;
    document.getElementById('modalTipo').value = evento.tipo_evento;
    document.getElementById('inputEventoId').value = evento.codigo;
    document.getElementById('estadoPagoInput').value = esGratuito ? 'Pagado' : 'Pendiente';
    
    // Mostrar/ocultar campo de comprobante
    const comprobanteDiv = document.getElementById('modalComprobanteDiv');
    comprobanteDiv.style.display = esGratuito ? 'none' : 'block';
    if (!esGratuito) {
      comprobanteDiv.querySelector('input').required = true;
    }
    
    modal.classList.add('active');
    
    // Ajustar altura después de cargar el contenido
    setTimeout(() => {
      const contentHeight = modalContent.scrollHeight;
      const windowHeight = window.innerHeight;
      
      if (contentHeight > windowHeight * 0.9) {
        modalContent.style.maxHeight = '80vh';
      }
    }, 10);
  }
function cerrarModal() {
  document.getElementById('modalEvento').classList.remove('active');
}

function mostrarCarga() {
  const container = document.getElementById('eventosContainer');
  container.innerHTML = `
    <div class="loading-container">
      <div class="loading-spinner"></div>
      <p>Cargando eventos...</p>
    </div>
  `;
}

function validarComprobante() {
  const tipo = document.getElementById('modalTipo').value.toLowerCase();
  if (tipo === 'gratuito') return true;
  const archivo = document.getElementById('modalComprobante');
  if (!archivo.files.length) {
    alert('Debe subir el comprobante de pago.');
    return false;
  }
  const file = archivo.files[0];
  const sizeMB = file.size / (1024 * 1024);
  if (!['image/jpeg', 'image/png'].includes(file.type)) {
    alert('El archivo debe ser JPG o PNG.');
    return false;
  }
  if (sizeMB > 2) {
    alert('El archivo no debe superar los 2MB.');
    return false;
  }
  return true;
}

let eventos = [];

function renderizarEventos(filtrados) {
  const container = document.getElementById('eventosContainer');
  container.innerHTML = '';
  if (!filtrados.length) {
    container.innerHTML = '<p style="text-align:center;color:#777">No se encontraron eventos</p>';
    return;
  }
  filtrados.forEach(ev => {
    const esGratuito = ev.tipo_evento.toLowerCase() === 'gratuito';
    const tipoClase = esGratuito ? 'gratuito' : 'pagado';
    const precioTexto = esGratuito ? 'Gratuito' : `$${ev.costo || '0'}`;
    const imagenEvento = ev.imagen || 'https://images.unsplash.com/photo-1505373877841-8d25f7d46678?q=80&w=1000';
    const card = document.createElement('div');
    card.className = 'event-card';
    card.innerHTML = `
      <div class="event-image">
        <img src="${imagenEvento}" alt="${ev.titulo}">
      </div>
      <div class="event-content">
        <h3 class="event-title">${ev.titulo}</h3>
        <div class="event-meta">
          <div class="event-date">
            <i class="far fa-calendar-alt"></i>
            ${formatearFecha(ev.fechaInicio)}
          </div>
          <span class="event-type ${tipoClase}">
            ${ev.tipo_evento}
          </span>
        </div>
        <p class="event-description">${ev.descripcion || 'Descripción no disponible'}</p>
        <div class="event-footer">
          <div class="event-price ${tipoClase}">${precioTexto}</div>
          <button class="event-btn" onclick='mostrarModal(${JSON.stringify(ev)})'>
            <i class="fas fa-ticket-alt"></i> Inscribirse
          </button>
        </div>
      </div>
    `;
    container.appendChild(card);
  });
}

document.addEventListener('DOMContentLoaded', function() {
  mostrarCarga();
  
  fetch('../usuarios/inscripciones/get_eventos_disponibles.php')
    .then(res => {
      if (!res.ok) throw new Error('Error al cargar eventos');
      return res.json();
    })
    .then(data => {
      eventos = data;
      if (eventos.length === 0) {
        renderizarEventos([]);
        document.getElementById('eventosContainer').innerHTML = `
          <p style="text-align:center; color:#666; padding:2rem;">
            No hay eventos disponibles en este momento.
          </p>
        `;
      } else {
        renderizarEventos(eventos);
      }
    })
    .catch(err => {
      console.error('Error cargando eventos:', err);
      document.getElementById('eventosContainer').innerHTML = `
        <p style="text-align:center; color:#dc3545; padding:2rem;">
          Error al cargar los eventos. Por favor intenta nuevamente.
        </p>
      `;
    });

  document.getElementById('searchInput').addEventListener('input', function() {
    const texto = this.value.toLowerCase();
    const filtrados = eventos.filter(ev => ev.titulo.toLowerCase().includes(texto));
    renderizarEventos(filtrados);
  });

  let currentSlide = 0;
  const slides = document.querySelectorAll('.carousel-slide');
  function showSlide(index) {
    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === index);
    });
  }
  setInterval(() => {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }, 5000);
});
</script>
</body>
</html>
