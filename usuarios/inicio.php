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
$cedula_usuario = $_SESSION['cedula'];
$id_carrera_usuario = null;
$preselected_event_data = null;
$preselected_event_unavailable = false;

// Obtener el primer nombre y la carrera del usuario
$sql_user = "SELECT nom_pri_usu, car_usu FROM usuarios WHERE ced_usu = $1";
$result_user = pg_query_params($conn, $sql_user, [$cedula_usuario]);

if ($datos_user = pg_fetch_assoc($result_user)) {
    $nombre_usuario = $datos_user['nom_pri_usu'];
    $id_carrera_usuario = $datos_user['car_usu'];
}

$evento_id_param = $_GET['evento_id'] ?? $_GET['evento'] ?? null;

if ($evento_id_param) {
    $evento_id = pg_escape_string($conn, $evento_id_param);

    $cedula = $_SESSION['cedula'] ?? null;
    $idEvento = $evento_id; 

    if (!$cedula || !$idEvento) {
        echo "Datos incompletos";
        exit;
    }

    // Paso 1: Obtener la carrera del usuario
    $sqlCarrera = "SELECT CAR_USU FROM USUARIOS WHERE CED_USU = $1";
    $resCarrera = pg_query_params($conn, $sqlCarrera, [$cedula]);

    if (!$resCarrera || pg_num_rows($resCarrera) === 0) {
        echo "No se encontró la carrera del usuario.";
        exit;
    }

    $carreraNombre = pg_fetch_result($resCarrera, 0, 0);

    // Paso 2: Obtener el ID de la carrera según su nombre
    $sqlIdCarrera = "SELECT ID_CAR FROM CARRERAS WHERE NOM_CAR = $1";
    $resIdCarrera = pg_query_params($conn, $sqlIdCarrera, [$carreraNombre]);

    if (!$resIdCarrera || pg_num_rows($resIdCarrera) === 0) {
        echo "No se encontró ID de la carrera.";
        exit;
    }

    $idCarrera = pg_fetch_result($resIdCarrera, 0, 0);

    // Paso 3: Verificar si el evento está dirigido a la carrera
    $sqlVerificar = "SELECT COUNT(*) FROM EVENTOS_CARRERAS WHERE ID_EVE_CUR = $1 AND ID_CAR = $2";
    $resVerificar = pg_query_params($conn, $sqlVerificar, [$idEvento, $idCarrera]);

    if (!$resVerificar) {
        echo "Error al verificar carrera del evento.";
        exit;
    }

    $cantidad = pg_fetch_result($resVerificar, 0, 0);

    // Paso 4: Redirigir según corresponda
    if ($cantidad > 0) {
        // En lugar de redirigir a eventoDetalle.php, ahora el modal se abrirá aquí
        // Obtener los detalles del evento para mostrar en el modal
        $sql_event_details = "SELECT
                                ec.ID_EVE_CUR as codigo,
                                ec.TIT_EVE_CUR as titulo,
                                ec.DES_EVE_CUR as descripcion,
                                ec.FEC_INI_EVE_CUR as fechaInicio,
                                ec.FEC_FIN_EVE_CUR as fechaFin,
                                ec.COS_EVE_CUR as costo,
                                ec.MOD_EVE_CUR as tipo_evento,
                                json_agg(json_build_object('id_req', r.id_req, 'nom_req', r.nom_req, 'valor_req', er.valor_req)) FILTER (WHERE r.id_req IS NOT NULL) as requisitos
                              FROM EVENTOS_CURSOS ec
                              LEFT JOIN eventos_requisitos er ON ec.ID_EVE_CUR = er.id_eve_cur
                              LEFT JOIN requisitos r ON er.id_req = r.id_req
                              WHERE ec.ID_EVE_CUR = $1
                              GROUP BY ec.ID_EVE_CUR, ec.TIT_EVE_CUR, ec.DES_EVE_CUR, ec.FEC_INI_EVE_CUR, ec.FEC_FIN_EVE_CUR, ec.COS_EVE_CUR, ec.MOD_EVE_CUR";

        $result_event_details = pg_query_params($conn, $sql_event_details, [$evento_id]);

        if ($event_data = pg_fetch_assoc($result_event_details)) {
            $preselected_event_data = $event_data;
        } else {
            $preselected_event_unavailable = true; // Evento no encontrado o error
        }
    } else {
        $preselected_event_unavailable = true; // No pertenece a la carrera
    }
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
  backdrop-filter: blur(5px);
}

.modal.active {
  display: flex;
  opacity: 1;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 90%;
  max-width: 600px; /* Aumentado para mejor espacio */
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
  transform: translateY(-20px);
  transition: transform 0.3s ease, opacity 0.3s ease;
  position: relative;
  display: flex;
  flex-direction: column;
  opacity: 0;
  max-height: 100vh; /* Permite scroll si el contenido es muy largo */
}

.modal.active .modal-content {
  transform: translateY(0);
  opacity: 1;
}

.modal-header {
  padding: 1.5rem;
  border-bottom: 1px solid #e9ecef;
  position: relative;
  background: #f8f9fa;
  border-radius: 12px 12px 0 0;
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
  background: none;
  border: none;
  padding: 0;
}

.modal-close:hover {
  color: #dc3545;
}

.modal-body {
  padding: 1.5rem;
  overflow-y: auto; /* Permite scroll dentro del cuerpo del modal */
  flex-grow: 1;
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
  transition: border-color 0.3s ease;
}

.modal-input:focus {
  outline: none;
  border-color: #4361ee;
}

.modal-footer {
  padding: 1.5rem;
  border-top: 1px solid #e9ecef;
  text-align: right;
  background: #f8f9fa;
  border-radius: 0 0 12px 12px;
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
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Estilo para campos de solo lectura */
.modal-input[readonly] {
  background-color: #e9ecef;
  cursor: not-allowed;
}

/* Mejoras para el modal de evento no disponible */
#modalEventoNoDisponible .modal-content {
  text-align: center;
}

#modalEventoNoDisponible .modal-body {
  padding: 2rem;
}

#modalEventoNoDisponible .modal-body p {
  margin-bottom: 1rem;
}

#modalEventoNoDisponible .modal-footer {
  text-align: center;
  justify-content: center;
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
<form id="formInscribir" method="POST" action="../usuarios/inscripciones/procesar_inscripcion.php" enctype="multipart/form-data" onsubmit="enviarInscripcion(event)">
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
        <div class="modal-field" id="modalNotaMinimaDiv" style="display: none;">
          <label class="modal-label">Nota Mínima Requerida:</label>
          <input type="text" class="modal-input" id="modalNotaMinima" readonly>
        </div>
        <div class="modal-field" id="modalAsistenciaRequeridaDiv" style="display: none;">
          <label class="modal-label">Asistencia Requerida (%):</label>
          <input type="text" class="modal-input" id="modalAsistenciaRequerida" readonly>
        </div>
        <div id="requisitosDinamicosContainer">
      <!-- Aquí se insertarán dinámicamente los campos de requisitos -->
    </div>

    <div class="modal-field" id="modalComprobanteDiv" style="display: none;">
      <label class="modal-label">Subir Comprobante (JPG/PNG, máx 2MB):</label>
      <input type="file" class="modal-input" name="comprobante" id="modalComprobante" accept="image/png, image/jpeg">
    </div>
  </div>
  <div class="modal-footer">
    <input type="hidden" name="evento" id="inputEventoId">
    <input type="hidden" name="cedula" value="<?= htmlspecialchars($cedula_usuario) ?>">
    <input type="hidden" name="fecha_inscripcion" value="<?= date('Y-m-d') ?>">
    <input type="hidden" name="estado_pago" id="estadoPagoInput">
    <button type="submit" class="btn-inscribir">
      <i class="fas fa-check-circle"></i> Confirmar Inscripción
    </button>
  </div>
</form>
    </div>
  </div>

  <div class="modal" id="modalEventoNoDisponible">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title">Evento No Disponible</h3>
        <span class="modal-close" onclick="cerrarModalNoDisponible()">&times;</span>
      </div>
      <div class="modal-body" style="text-align: center;">
        <p style="font-size: 1.1rem; color: #555;">Lo sentimos, este evento no está disponible para tu carrera.</p>
        <p style="font-size: 0.9rem; color: #777;">Por favor, explora otros eventos disponibles.</p>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <button type="button" class="btn-primary" onclick="cerrarModalNoDisponible()">Entendido</button>
      </div>
    </div>
  </div>
</main>
<?php include "../includes/footer.php"; ?>
<script>
const imagenesEventos = [
  'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?q=80&w=1000', // Conferencia
  'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1000', // Estudio/Curso
  'https://images.unsplash.com/photo-1540575467063-178a50c2df87?q=80&w=1000', // Presentación
  'https://images.unsplash.com/photo-1515187029135-18ee286d815b?q=80&w=1000', // Trabajo en equipo
  'https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000', // Reunión
  'https://images.unsplash.com/photo-1531482615713-2afd69097998?q=80&w=1000', // Capacitación
  'https://images.unsplash.com/photo-1553028826-f4804a6dba3b?q=80&w=1000', // Seminario
  'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1000', // Workshop
  'https://images.unsplash.com/photo-1560439514-4e9645039924?q=80&w=1000', // Educación
  'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?q=80&w=1000'  // Networking
];

function obtenerImagenAleatoria() {
  const indice = Math.floor(Math.random() * imagenesEventos.length);
  return imagenesEventos[indice];
}

// Función robusta para formatear fechas - Solo día, mes y año
function formatearFecha(fechaStr) {
  if (!fechaStr || fechaStr === 'null' || fechaStr.trim() === '' || fechaStr === undefined) {
    return 'Sin fecha definida';
  }
  
  try {
    let fechaObj;
    
    // Convertir string a fecha manejando diferentes formatos
    if (typeof fechaStr === 'string') {
      // Si es formato YYYY-MM-DD (común en PostgreSQL)
      if (/^\d{4}-\d{2}-\d{2}$/.test(fechaStr.trim())) {
        const partes = fechaStr.trim().split('-');
        fechaObj = new Date(parseInt(partes[0]), parseInt(partes[1]) - 1, parseInt(partes[2]));
      }
      // Si es formato DD/MM/YYYY
      else if (/^\d{2}\/\d{2}\/\d{4}$/.test(fechaStr.trim())) {
        const partes = fechaStr.trim().split('/');
        fechaObj = new Date(parseInt(partes[2]), parseInt(partes[1]) - 1, parseInt(partes[0]));
      }
      // Si ya incluye hora (formato ISO)
      else if (fechaStr.includes('T') || fechaStr.includes(' ')) {
        fechaObj = new Date(fechaStr);
      }
      // Último intento con el constructor Date
      else {
        fechaObj = new Date(fechaStr);
      }
    } else {
      fechaObj = new Date(fechaStr);
    }

    if (isNaN(fechaObj.getTime())) {
      return 'Sin fecha definida';
    }

    // Array de meses en español
    const meses = [
      'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
      'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
    ];

    const dia = fechaObj.getDate();
    const mes = meses[fechaObj.getMonth()];
    const año = fechaObj.getFullYear();

    return `${dia} de ${mes} de ${año}`;
    
  } catch (error) {
    console.error('Error al formatear fecha:', error);
    return 'Sin fecha definida';
  }
}

function mostrarModal(evento) {
  const modal = document.getElementById('modalEvento');
  
  document.getElementById('modalTitulo').textContent = evento.titulo;
  document.getElementById('modalDescripcion').value = evento.descripcion || 'No disponible';
  
  // Usar las claves correctas (fechaInicio y fechaFin)
  document.getElementById('modalFechaInicio').value = formatearFecha(evento.fechaInicio);
  document.getElementById('modalFechaFin').value = formatearFecha(evento.fechaFin);
  
  const esGratuito = parseFloat(evento.costo) === 0;
  const costoTexto = esGratuito ? 'Gratuito' : `$${parseFloat(evento.costo || '0').toFixed(2)}`;
  
  document.getElementById('modalCosto').value = costoTexto;
  document.getElementById('modalTipo').value = evento.tipo_evento || 'No especificado';
  document.getElementById('inputEventoId').value = evento.codigo;
  document.getElementById('estadoPagoInput').value = esGratuito ? 'Pagado' : 'Pendiente';

  // Ocultar/limpiar campos de requisitos estáticos y comprobante
  document.getElementById('modalNotaMinimaDiv').style.display = 'none';
  document.getElementById('modalAsistenciaRequeridaDiv').style.display = 'none';

  // Limpiar cualquier requisito dinámico previo para evitar duplicados al abrir el modal varias veces
  const requisitosDinamicosContainer = document.getElementById('requisitosDinamicosContainer');
  if (requisitosDinamicosContainer) {
    requisitosDinamicosContainer.innerHTML = ''; // Limpiar contenido anterior
  }

  // **** CAMBIO CLAVE AQUÍ: Generar campos dinámicamente para los requisitos 'actual' ****
  if (evento.requisitos && Array.isArray(evento.requisitos)) {
    // Convertir la cadena JSON de requisitos a un objeto JavaScript si es necesario
    let requisitosParsed;
    try {
        requisitosParsed = (typeof evento.requisitos === 'string') ? JSON.parse(evento.requisitos) : evento.requisitos;
    } catch (e) {
        console.error("Error al parsear requisitos JSON:", e);
        requisitosParsed = [];
    }

    requisitosParsed.forEach(req => {
      // Requisitos de Nota y Asistencia (se mantienen estáticos pero su visibilidad se controla)
      if (req.nom_req === 'Nota mínima' && !isNaN(parseFloat(req.valor_req)) && parseFloat(req.valor_req) > 0) {
        document.getElementById('modalNotaMinima').value = parseFloat(req.valor_req);
        document.getElementById('modalNotaMinimaDiv').style.display = 'block';
      } else if (req.nom_req === 'Asistencia' && !isNaN(parseFloat(req.valor_req)) && parseFloat(req.valor_req) > 0) {
        document.getElementById('modalAsistenciaRequerida').value = parseFloat(req.valor_req) + '%';
        document.getElementById('modalAsistenciaRequeridaDiv').style.display = 'block';
      } 
      // Requisitos 'actual' que requieren archivo
      else if (req.valor_req === 'actual') {
        const reqDiv = document.createElement('div');
        reqDiv.className = 'modal-field';
        reqDiv.innerHTML = `
          <label class="modal-label">${req.nom_req} (PDF requerido):</label>
          <input type="file" class="modal-input" name="requisito_file_${req.id_req}" accept="application/pdf" required>
        `;
        if (requisitosDinamicosContainer) {
            requisitosDinamicosContainer.appendChild(reqDiv);
        } else {
            console.error("El contenedor 'requisitosDinamicosContainer' no se encontró en el DOM.");
        }
      }
    });
  }

  // Si es pagado, mostrar el campo de comprobante
  const comprobanteDiv = document.getElementById('modalComprobanteDiv');
  const comprobanteInput = document.getElementById('modalComprobante');
  
  if (!esGratuito) {
    comprobanteDiv.style.display = 'block';
    comprobanteInput.required = true;
  } else {
    comprobanteDiv.style.display = 'none';
    comprobanteInput.required = false;
  }
  
  modal.classList.add('active');
  document.body.style.overflow = 'hidden'; // Prevenir scroll del body
}

function renderizarEventos(filtrados) {
  const container = document.getElementById('eventosContainer');
  container.innerHTML = '';
  if (!filtrados.length) {
    container.innerHTML = '<p style="text-align:center;color:#777">No se encontraron eventos</p>';
    return;
  }

  filtrados.forEach((ev, index) => {
    const esGratuito = parseFloat(ev.costo) === 0;
    const tipoClase = esGratuito ? 'gratuito' : 'pagado';
    const precioTexto = esGratuito ? 'Gratuito' : `$${parseFloat(ev.costo || '0').toFixed(2)}`;
    const imagenEvento = obtenerImagenAleatoria();
    
    let requisitosHtml = '';
    // Necesitamos parsear la cadena JSON de requisitos si viene como string
    let requisitosParaMostrar = [];
    if (ev.requisitos) {
        try {
            requisitosParaMostrar = (typeof ev.requisitos === 'string') ? JSON.parse(ev.requisitos) : ev.requisitos;
        } catch (e) {
            console.error("Error al parsear requisitos para renderizado:", e);
            requisitosParaMostrar = [];
        }
    }

    const notaMinimaObj = requisitosParaMostrar.find(r => r.nom_req === 'Nota mínima');
    const asistenciaRequeridaObj = requisitosParaMostrar.find(r => r.nom_req === 'Asistencia');
    const tieneRequisitosArchivo = requisitosParaMostrar.some(r => r.valor_req === 'actual');

    const notaMinima = notaMinimaObj ? parseFloat(notaMinimaObj.valor_req) : NaN;
    const asistenciaRequerida = asistenciaRequeridaObj ? parseFloat(asistenciaRequeridaObj.valor_req) : NaN;

    if ((!isNaN(notaMinima) && notaMinima > 0) || (!isNaN(asistenciaRequerida) && asistenciaRequerida > 0) || tieneRequisitosArchivo) {
        requisitosHtml += `<p class="event-description"><strong>Requisitos:</strong></p>`;
        if (!isNaN(notaMinima) && notaMinima > 0) {
            requisitosHtml += `<p class="event-description">- Nota Mínima: ${notaMinima}</p>`;
        }
        if (!isNaN(asistenciaRequerida) && asistenciaRequerida > 0) {
            requisitosHtml += `<p class="event-description">- Asistencia Requerida: ${asistenciaRequerida}%</p>`;
        }
        if (tieneRequisitosArchivo) {
            requisitosHtml += `<p class="event-description">- Se requiere(n) archivo(s) adicional(es).</p>`;
        }
    } else {
        requisitosHtml += `<p class="event-description">No se requieren requisitos específicos para este evento.</p>`;
    }


    const card = document.createElement('div');
    card.className = 'event-card';
    card.innerHTML = `
      <div class="event-image">
        <img src="${imagenEvento}" alt="${ev.titulo}" onerror="this.src='https://via.placeholder.com/300x180/4361ee/ffffff?text=Evento'">
      </div>
      <div class="event-content">
        <h3 class="event-title">${ev.titulo}</h3>
        <div class="event-meta">
          <div class="event-date">
            <i class="far fa-calendar-alt"></i>
            ${formatearFecha(ev.fechaInicio)}
          </div>
          <span class="event-type ${tipoClase}">
            ${ev.tipo_evento || 'Sin especificar'}
          </span>
        </div>
        <p class="event-description">${ev.descripcion || 'Descripción no disponible'}</p>
        ${requisitosHtml} <div class="event-footer">
          <div class="event-price ${tipoClase}">${precioTexto}</div>
          <button class="event-btn" onclick='mostrarModal(${JSON.stringify(ev).replace(/'/g, "\\'")})'}>
            <i class="fas fa-ticket-alt"></i> Inscribirse
          </button>
        </div>
      </div>
    `;
    container.appendChild(card);
  });
}

function cerrarModal() {
  document.getElementById('modalEvento').classList.remove('active');
  document.body.style.overflow = 'auto'; // Restaurar scroll del body
  // Eliminar el evento_id o evento de la URL después de cerrar el modal
  const url = new URL(window.location.href);
  url.searchParams.delete('evento_id');
  url.searchParams.delete('evento');
  window.history.replaceState({}, document.title, url.toString());
}

// Funciones para el modal de evento no disponible
function mostrarModalNoDisponible() {
  const modal = document.getElementById('modalEventoNoDisponible');
  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function cerrarModalNoDisponible() {
  document.getElementById('modalEventoNoDisponible').classList.remove('active');
  document.body.style.overflow = 'auto';
  // Eliminar el evento_id o evento de la URL después de cerrar el modal
  const url = new URL(window.location.href);
  url.searchParams.delete('evento_id');
  url.searchParams.delete('evento');
  window.history.replaceState({}, document.title, url.toString());
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
  const costoInput = document.getElementById('modalCosto').value;
  const esGratuito = costoInput.toLowerCase() === 'gratuito';
  
  if (esGratuito) return true; // No se requiere comprobante para eventos gratuitos
  
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

document.addEventListener('DOMContentLoaded', function() {
  // Verificar evento preseleccionado desde PHP
  const preselectedEvent = <?php echo json_encode($preselected_event_data); ?>;
  const preselectedEventUnavailable = <?php echo json_encode($preselected_event_unavailable); ?>;

  if (preselectedEvent) {
    if (preselectedEventUnavailable) {
      mostrarModalNoDisponible();
    } else {
      mostrarModal(preselectedEvent);
    }
  }

  // Cargar y renderizar eventos
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

  // Cerrar modal al hacer clic fuera del contenido
  const modalEvento = document.getElementById('modalEvento');
  modalEvento.addEventListener('click', function(event) {
    if (event.target === modalEvento) {
      cerrarModal();
    }
  });

  const modalEventoNoDisponible = document.getElementById('modalEventoNoDisponible');
  modalEventoNoDisponible.addEventListener('click', function(event) {
    if (event.target === modalEventoNoDisponible) {
      cerrarModalNoDisponible();
    }
  });
});

function enviarInscripcion(event) {
  event.preventDefault();
  const form = event.target;
  const formData = new FormData(form);
  
  // Mostrar loader o mensaje de procesamiento
  const submitBtn = form.querySelector('button[type="submit"]');
  const originalBtnText = submitBtn.innerHTML;
  submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
  submitBtn.disabled = true;

  fetch(form.action, {
    method: 'POST',
    body: formData
  })
  .then(response => {
    if (!response.ok) {
      return response.text().then(text => {
        // Intenta parsear como JSON si es posible
        try {
          const jsonData = JSON.parse(text);
          throw new Error(jsonData.message || 'Error en el servidor');
        } catch {
          throw new Error(text || 'Error en la red: ' + response.status);
        }
      });
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      alert(data.message);
      cerrarModal();
      // Opcional: recargar la página o actualizar la lista de eventos
      window.location.reload();
    } else {
      throw new Error(data.message || 'Error desconocido');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('Error al procesar la inscripción: ' + error.message);
  })
  .finally(() => {
    submitBtn.innerHTML = originalBtnText;
    submitBtn.disabled = false;
  });
}
</script>
</body>
</html>