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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/css/componente.css">
<style>
    /* General Body and Container */
body {
    font-family: 'Open Sans', sans-serif;
    background-color: #f8fafd; /* Very light blue-grey */
    color: #34495e; /* Darker blue-grey for text */
    line-height: 1.6;
}

/* Add this :root block to define your custom color variable */
:root {
    --primary-event-color: rgb(129, 9, 9);
    --primary-event-color-light: rgba(129, 9, 9, 0.8); /* A slightly lighter version for gradients if needed */
}

.container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 20px;
}

/* Section Header */
.recent-activity h2 {
    text-align: center;
    color: #2c3e50; /* Dark blue */
    margin-bottom: 30px;
    font-size: 2em; /* Slightly smaller */
    position: relative;
    padding-bottom: 10px;
    font-family: 'Montserrat', sans-serif; /* More elegant font for headers */
    font-weight: 600;
}

.recent-activity h2 i {
    margin-right: 10px;
    color: #7c2020; /* Elegant blue */
}

.recent-activity h2::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: 0;
    transform: translateX(-50%);
    width: 60px; /* Smaller underline */
    height: 2px; /* Thinner underline */
    background-color: #7c2020;
    border-radius: 5px;
}

/* Events Container - Grid Layout */
#eventos-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Smaller min-width */
    gap: 20px; /* Slightly smaller gap */
    padding: 20px 0;
}

/* Individual Event Card */
.evento-moderno {
    background-color: #ffffff;
    border-radius: 8px; /* Softer corners */
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08); /* Lighter shadow */
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease; /* Faster transition */
    display: flex;
    flex-direction: column;
    border: 1px solid #e0e6ed; /* Subtle border */
}

.evento-moderno:hover {
    transform: translateY(-5px); /* Less dramatic lift */
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12); /* Slightly more prominent hover shadow */
}

/* Event Header */
.evento-header {
    /* Use your custom property here */
    background: linear-gradient(135deg, var(--primary-event-color), var(--primary-event-color-light)); /* Apply the root variable */
    color: white;
    padding: 15px 20px; /* Reduced padding */
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 8px;
    border-top-right-radius: 8px;
}

.evento-header h4 {
    margin: 0;
    font-size: 1.2em; /* Smaller title font */
    display: flex;
    align-items: center;
    font-family: 'Montserrat', sans-serif;
    font-weight: 600;
}

.evento-header h4 i {
    margin-right: 8px; /* Smaller margin */
    font-size: 1em; /* Consistent icon size */
    color: rgba(255, 255, 255, 0.8); /* Slightly transparent white */
}

/* Payment Status Badge */
.estado-pago {
    background-color: rgba(255, 255, 255, 0.15); /* More subtle background */
    padding: 4px 10px; /* Smaller padding */
    border-radius: 15px; /* More rounded */
    font-weight: 500; /* Lighter weight */
    font-size: 0.8em; /* Smaller font */
    text-transform: uppercase;
}

.estado-pago.pagado {
    background-color: #2ecc71; /* Emerald Green */
    color: white;
}

.estado-pago.pendiente {
    background-color: #f1c40f; /* Sunflower Yellow */
    color: #34495e; /* Dark text for contrast */
}

.estado-pago.rechazado {
    background-color: #e74c3c; /* Alizarin Red */
    color: white;
}

/* Event Body */
.evento-body {
    padding: 15px 20px; /* Reduced padding */
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-size: 0.9em; /* Smaller body text */
}

.evento-body p {
    margin-bottom: 8px; /* Less margin between paragraphs */
    display: flex;
    align-items: flex-start; /* Align icons to the top of multi-line text */
    color: #555;
}

.evento-body p i {
    margin-right: 8px;
    color: #6c7eaf; /* Softer blue for icons */
    width: 18px; /* Slightly smaller icon width */
    text-align: center;
    flex-shrink: 0; /* Prevent icon from shrinking */
}

.evento-body .description-text {
    max-height: 3.2em; /* Roughly 2 lines of text, adjusted for smaller font */
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.description-text.expanded {
    max-height: none;
    overflow: visible;
    -webkit-line-clamp: unset;
}

.read-more-btn {
    background: none;
    border: none;
    color: #4a69bd; /* Match header blue */
    cursor: pointer;
    text-decoration: underline;
    padding: 0;
    font-size: 0.85em; /* Smaller button text */
    margin-top: 5px;
    text-align: left;
}

.read-more-btn:hover {
    color: #34495e; /* Darker blue on hover */
}

.fechas-evento {
    font-weight: 600;
    color: #34495e;
    margin-top: 5px; /* More space for dates */
}

/* No Events Message */
#eventos-container .evento-moderno:only-child { /* Target only if it's the sole child */
    grid-column: 1 / -1;
    text-align: center;
    padding: 30px; /* Slightly smaller padding */
    font-size: 1.1em;
    color: #7f8c8d;
    background-color: #eff3f7; /* Lighter background */
    box-shadow: none;
    border: 1px dashed #c0d0e0; /* Dashed border for visual cue */
    display: block; /* Override flex for centered text */
}

#eventos-container .evento-moderno:only-child p {
    margin: 0;
    justify-content: center; /* Center text within the paragraph */
}

#eventos-container .evento-moderno:only-child:hover {
    transform: none;
    box-shadow: none;
}
</style>
</head>
<body>
<?php include "../includes/header.php"; ?>
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
<?php include "../includes/footer.php"; ?>
<script>
    function formatearFecha(fecha) {
        if (!fecha) return 'Sin fecha';

        try {
            const fechaObj = new Date(fecha);
            if (isNaN(fechaObj.getTime())) {
                return fecha;
            }

            return fechaObj.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (error) {
            return fecha;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const eventos = document.querySelectorAll('.evento-moderno');

        eventos.forEach(evento => {
            const fechaInicio = evento.dataset.fechaInicio;
            const fechaFin = evento.dataset.fechaFin;

            if (evento.querySelector('.fecha-inicio')) {
                evento.querySelector('.fecha-inicio').textContent = formatearFecha(fechaInicio);
            }

            if (evento.querySelector('.fecha-fin')) {
                evento.querySelector('.fecha-fin').textContent = formatearFecha(fechaFin);
            }

            const spanFechaInscripcion = evento.querySelector('.fecha-inscripcion');
            if (spanFechaInscripcion) {
                spanFechaInscripcion.textContent = formatearFecha(spanFechaInscripcion.textContent);
            }
        });
    });
</script>
</body>
</html>