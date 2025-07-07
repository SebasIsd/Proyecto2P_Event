<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Validar ID del evento
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID de evento no válido.";
    exit;
}

$idEvento = $_GET['id'];
$cedula = $_SESSION['cedula'] ?? null;

if (!$cedula) {
    echo "Usuario no identificado.";
    exit;
}

// Obtener carrera del usuario
$sqlCarrera = "SELECT CAR_USU FROM USUARIOS WHERE CED_USU = $1";
$resCarrera = pg_query_params($conn, $sqlCarrera, [$cedula]);

if (!$resCarrera || pg_num_rows($resCarrera) === 0) {
    echo "No se encontró la carrera del usuario.";
    exit;
}

$carreraNombre = pg_fetch_result($resCarrera, 0, 0);

// Obtener ID_CAR de la carrera del usuario
$sqlIdCarrera = "SELECT ID_CAR FROM CARRERAS WHERE NOM_CAR = $1";
$resIdCarrera = pg_query_params($conn, $sqlIdCarrera, [$carreraNombre]);

if (!$resIdCarrera || pg_num_rows($resIdCarrera) === 0) {
    echo "No se encontró el ID de la carrera.";
    exit;
}

$idCarrera = pg_fetch_result($resIdCarrera, 0, 0);

// Verificar si el evento es ofertado a esa carrera
$sqlVerificar = "SELECT COUNT(*) FROM EVENTOS_CARRERAS WHERE ID_EVE_CUR = $1 AND ID_CAR = $2";
$resVerificar = pg_query_params($conn, $sqlVerificar, [$idEvento, $idCarrera]);

if (!$resVerificar || pg_fetch_result($resVerificar, 0, 0) == 0) {
    echo "<script>alert('Usted no pertenece a la carrera a la que está dirigido este evento.'); window.location.href = 'eventos.php';</script>";
    exit;
}

// Obtener detalles del evento
$sqlEvento = "SELECT ec.*, te.NOM_TIPO_EVE
              FROM EVENTOS_CURSOS ec
              LEFT JOIN TIPOS_EVENTO te ON te.ID_TIPO_EVE = ec.ID_TIPO_EVE
              WHERE ec.ID_EVE_CUR = $1";
$resEvento = pg_query_params($conn, $sqlEvento, [$idEvento]);

if (!$resEvento || pg_num_rows($resEvento) === 0) {
    echo "No se encontró información del evento.";
    exit;
}

$evento = pg_fetch_assoc($resEvento);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Evento</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
        }
        .evento-detalle {
            max-width: 700px;
            margin: 0 auto;
            background-color: #f7f7f7;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .evento-detalle h2 {
            color: #8B0000;
        }
        .evento-detalle p {
            margin-bottom: 1rem;
        }
        .btn-inscribirse {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            background-color: #FF5722;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .btn-inscribirse:hover {
            background-color: #e64a19;
        }
    </style>
</head>
<body>
    <div class="evento-detalle">
        <h2><?= htmlspecialchars($evento['tit_eve_cur']) ?></h2>
        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($evento['des_eve_cur'])) ?></p>
        <p><strong>Fecha de Inicio:</strong> <?= htmlspecialchars($evento['fec_ini_eve_cur']) ?></p>
        <p><strong>Fecha de Fin:</strong> <?= htmlspecialchars($evento['fec_fin_eve_cur']) ?></p>
        <p><strong>Costo:</strong> <?= $evento['cos_eve_cur'] == 0 ? 'Gratuito' : '$' . number_format($evento['cos_eve_cur'], 2) ?></p>
        <p><strong>Modalidad:</strong> <?= htmlspecialchars($evento['mod_eve_cur']) ?></p>
        <p><strong>Tipo de Evento:</strong> <?= htmlspecialchars($evento['nom_tipo_eve']) ?></p>

        <a class="btn-inscribirse" href="inscripciones/inscripciones.php?evento_id=<?= $evento['id_eve_cur'] ?>">Inscribirse</a>
    </div>
</body>
</html>
