<?php
session_start();
require_once "../../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

if (ob_get_length()) ob_clean();

function sendJsonResponse($success, $message, $statusCode = 200, $id_inscripcion = null) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'id_inscripcion' => $id_inscripcion
    ]);
    exit();
}

if (!isset($_SESSION['usuario']) || !isset($_SESSION['cedula'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        sendJsonResponse(false, 'Sesión expirada. Por favor, inicie sesión nuevamente.', 401);
    } else {
        header("Location: ../../login.php");
        exit();
    }
}

$cedula = $_SESSION['cedula'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['evento'])) {
    sendJsonResponse(false, 'Datos incompletos para la inscripción.', 400);
}

$id_evento = filter_var($_POST['evento'], FILTER_VALIDATE_INT);
$fecha_inscripcion = date('Y-m-d'); // Corrección: siempre usamos la fecha actual para inscripción

if (!$id_evento) {
    sendJsonResponse(false, 'ID de evento no válido.', 400);
}

try {
    // Verificar si el evento existe y obtener su tipo (pagado o gratuito)
    $sql_evento = "SELECT 1, mod_eve_cur FROM eventos_cursos WHERE id_eve_cur = $1";
    $result_evento = pg_query_params($conn, $sql_evento, array($id_evento));

    if (pg_num_rows($result_evento) === 0) {
        throw new Exception("El evento seleccionado no existe o ya fue eliminado.");
    }

    $evento_data = pg_fetch_assoc($result_evento);
    $tipo_evento = strtolower(trim($evento_data['mod_eve_cur'] ?? ''));

    // Definir estado de pago automáticamente
    $estado_pago = ($tipo_evento === 'gratis' || $tipo_evento === 'gratuito') ? 'Pagado' : 'Pendiente';

    // Verificar si ya está inscrito
    $sql_verificar = "SELECT 1 FROM INSCRIPCIONES WHERE CED_USU = $1 AND ID_EVE_CUR = $2";
    $result_verificar = pg_query_params($conn, $sql_verificar, array($cedula, $id_evento));

    if (pg_num_rows($result_verificar) > 0) {
        throw new Exception("Ya está inscrito en este evento.");
    }

    pg_query($conn, "BEGIN");

    $fecha_cierre_ins = date('Y-m-d', strtotime($fecha_inscripcion . ' +30 days'));

    // Insertar inscripción
    $sql_inscripcion = "INSERT INTO INSCRIPCIONES 
        (CED_USU, ID_EVE_CUR, FEC_INI_INS, FEC_CIE_INS, EST_PAG_INS) 
        VALUES ($1, $2, $3, $4, $5) RETURNING ID_INS";

    $result_inscripcion = pg_query_params($conn, $sql_inscripcion, array(
        $cedula,
        $id_evento,
        $fecha_inscripcion,
        $fecha_cierre_ins,
        $estado_pago
    ));

    if (!$result_inscripcion) {
        throw new Exception("Error al registrar la inscripción: " . pg_last_error($conn));
    }

    $id_inscripcion = pg_fetch_result($result_inscripcion, 0, 0);

    // Si el evento es pagado (pendiente), se requiere comprobante
    if ($estado_pago === 'Pendiente') {
        if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Se requiere un comprobante de pago para eventos pagados.");
        }

        $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipo_archivo = $_FILES['comprobante']['type'];
        $tamano_archivo = $_FILES['comprobante']['size'];

        if (!in_array($tipo_archivo, $permitidos)) {
            throw new Exception("Solo se permiten archivos de imagen (JPEG, PNG, GIF).");
        }
        if ($tamano_archivo > 2 * 1024 * 1024) {
            throw new Exception("El tamaño del archivo no debe superar los 2MB.");
        }

        $oid = pg_lo_import($conn, $_FILES['comprobante']['tmp_name']);
        if ($oid === false) {
            throw new Exception("Error al subir el comprobante a la base de datos.");
        }

        $sql_imagen = "INSERT INTO IMAGENES (ID_INS, COMPROBANTE_PAG_OID) VALUES ($1, $2)";
        $result_imagen = pg_query_params($conn, $sql_imagen, array($id_inscripcion, $oid));
        if (!$result_imagen) {
            throw new Exception("Error al asociar el comprobante con la inscripción: " . pg_last_error($conn));
        }
    }

    // Insertar en NOTAS_ASISTENCIAS
    // 8. Insertar un registro en NOTAS_ASISTENCIAS con valores por defecto
$sql_notas = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS, PORC_ASI_NOT_ASI, NOT_FIN_NOT_ASI, FINALIZADO) 
              VALUES ($1, null, null, false)";
    $result_notas = pg_query_params($conn, $sql_notas, array($id_inscripcion));
    if (!$result_notas) {
        throw new Exception("Error al crear el registro de notas/asistencias: " . pg_last_error($conn));
    }

pg_query($conn, "COMMIT");

sendJsonResponse(true, 'Inscripción realizada exitosamente.', 200, $id_inscripcion);
header("Location: ../mis_eventos.php");

} catch (Exception $e) {
    if (isset($conn)) {
        pg_query($conn, "ROLLBACK");
    }
    sendJsonResponse(false, $e->getMessage(), 500);

} finally {
    if (isset($conn)) {
        pg_close($conn);
    }
}

?>
