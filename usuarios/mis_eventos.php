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