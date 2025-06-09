<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();
$cedula = $_SESSION['cedula']; 
// Obtener eventos inscritos por el usuario
$sql = "SELECT 
            ec.ID_EVE_CUR, 
            ec.TIT_EVE_CUR as titulo, 
            ec.DES_EVE_CUR as descripcion,
            ec.FEC_INI_EVE_CUR as fecha_inicio, 
            ec.FEC_FIN_EVE_CUR as fecha_fin,
            ec.COS_EVE_CUR as costo,
            ec.MOD_EVE_CUR as modalidad,
            i.EST_PAG_INS as estado_pago,
            i.FEC_INI_INS as fecha_inscripcion
        FROM EVENTOS_CURSOS ec
        JOIN INSCRIPCIONES i ON ec.ID_EVE_CUR = i.ID_EVE_CUR
        WHERE i.CED_USU = $1
        ORDER BY ec.FEC_INI_EVE_CUR ASC";


$result = pg_query_params($conn, $sql, array($cedula));

$eventosInscritos = pg_fetch_all($result) ?: [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Mis Eventos Inscritos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/css/style.css">
</head>
<body>
    <header>
            <div class="container">
                <div class="logo">
                <h1>Bienvenido! 👋</h1>

                </div>
                <nav>
        <ul>
            <li><a href="inicio.php" class="active"><i class="fas fa-home"></i> Inicio</a></li>
            <li><a href="mis_eventos.php"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
            <li><a href="./inscripciones/inscripciones.php"><i class="fas fa-edit"></i> Inscripciones</a></li>
            <li><a href="../usuarios/SolicitudesCambios/solicitudCambios.html"><i class="fas fa-chart-bar"></i> Solicitudes de Cambios</a></li>
            <li class="profile-link">
                <a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
            </li>
            <li><a href="./logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
        </ul>
    </nav>
            </div>
        </header>

    <main class="container">
        <section class="recent-activity">
            <h2><i class="fas fa-calendar-check"></i> Mis Eventos Inscritos</h2>
            <div id="eventos-container">
                <?php if (empty($eventosInscritos)): ?>
                    <div class="evento-moderno">
                        <p>No estás inscrito en ningún evento actualmente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($eventosInscritos as $evento): ?>
                        <div class="evento-moderno" 
                             data-fecha-inicio="<?= htmlspecialchars($evento['fecha_inicio']) ?>"
                             data-fecha-fin="<?= htmlspecialchars($evento['fecha_fin']) ?>">
                            <div class="evento-header">
                                <h4><i class="fas fa-calendar-alt"></i> <?= htmlspecialchars($evento['titulo']) ?></h4>
                                <span class="estado-pago <?= strtolower($evento['estado_pago']) ?>">
                                    <?= htmlspecialchars($evento['estado_pago']) ?>
                                </span>
                            </div>
                            <div class="evento-body">
                                <p><i class="fas fa-align-left"></i> <?= htmlspecialchars(substr($evento['descripcion'], 0, 100)) ?>...</p>
                                <p class="fechas-evento"><i class="fas fa-calendar-day"></i> 
                                    <span class="fecha-inicio"></span> - 
                                    <span class="fecha-fin"></span>
                                </p>
                                <p><i class="fas fa-dollar-sign"></i> $<?= htmlspecialchars($evento['costo']) ?></p>
                                <p><i class="fas fa-laptop-house"></i> Evento tipo: <?= htmlspecialchars($evento['modalidad']) ?></p>
                                <p><i class="fas fa-calendar-plus"></i> Inscrito el: <span class="fecha-inscripcion"><?= htmlspecialchars($evento['fecha_inscripcion']) ?></span></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    
    <style>
        .evento-moderno {
            background-color: #fff;
            padding: 1.2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .evento-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #eee;
        }
        
        .evento-header h4 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .estado-pago {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .estado-pago.pagado {
            background-color: #d4edda;
            color: #155724;
        }
        
        .estado-pago.pendiente {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .evento-body p {
            margin: 0.6rem 0;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
        }
        
        .evento-body i {
            color: var(--primary-color);
            font-size: 0.9rem;
            margin-top: 2px;
            flex-shrink: 0;
        }
    </style>

    <script>
        function formatearFecha(fecha) {
            if (!fecha) return 'Sin fecha';
            
            try {
                const fechaObj = new Date(fecha);
                if (isNaN(fechaObj.getTime())) {
                    return fecha; // Devolver la fecha original si no se puede parsear
                }
                
                return fechaObj.toLocaleDateString('es-ES', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                });
            } catch (error) {
                return fecha; // Devolver la fecha original si hay error
            }
        }

        // Aplicar formato a todas las fechas al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            const eventos = document.querySelectorAll('.evento-moderno');
            
            eventos.forEach(evento => {
                const fechaInicio = evento.dataset.fechaInicio;
                const fechaFin = evento.dataset.fechaFin;
                
                // Formatear fechas
                if (evento.querySelector('.fecha-inicio')) {
                    evento.querySelector('.fecha-inicio').textContent = formatearFecha(fechaInicio);
                }
                
                if (evento.querySelector('.fecha-fin')) {
                    evento.querySelector('.fecha-fin').textContent = formatearFecha(fechaFin);
                }
                
                // Formatear fecha de inscripción
                const spanFechaInscripcion = evento.querySelector('.fecha-inscripcion');
                if (spanFechaInscripcion) {
                    spanFechaInscripcion.textContent = formatearFecha(spanFechaInscripcion.textContent);
                }
            });
        });
    </script>
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
        <p>&copy; 2025 Sistema de Inscripciones. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
</body>
</html>