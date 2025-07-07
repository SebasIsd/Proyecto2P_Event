<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require_once "../../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

if (ob_get_length()) ob_clean();

function sendJsonResponse($success, $message, $statusCode = 200, $data = []) {
    header('Content-Type: application/json');
    http_response_code($statusCode);
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $data));
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
$fecha_inscripcion = date('Y-m-d'); 

if (!$id_evento) {
    sendJsonResponse(false, 'ID de evento no válido.', 400);
}

try {
    // Verificar si el evento existe y obtener su costo y tipo
    $sql_evento = "SELECT COS_EVE_CUR, MOD_EVE_CUR FROM eventos_cursos WHERE id_eve_cur = $1"; // Corrected column name
    $result_evento = pg_query_params($conn, $sql_evento, array($id_evento));

    if (!$result_evento) { // Add check for query failure
        throw new Exception("Error al consultar el evento: " . pg_last_error($conn));
    }

    if (pg_num_rows($result_evento) === 0) {
        throw new Exception("El evento seleccionado no existe o ya fue eliminado.");
    }

    $evento_data = pg_fetch_assoc($result_evento);
    $costo_evento = (float)$evento_data['cos_eve_cur']; // Use the correct column name
    $tipo_evento = strtolower(trim($evento_data['mod_eve_cur'] ?? ''));

    // Definir estado de pago automáticamente
    $estado_pago = ($costo_evento == 0) ? 'Pagado' : 'Pendiente';

    // Verificar si ya está inscrito
    $sql_verificar = "SELECT 1 FROM INSCRIPCIONES WHERE CED_USU = $1 AND ID_EVE_CUR = $2";
    $result_verificar = pg_query_params($conn, $sql_verificar, array($cedula, $id_evento));

    if (!$result_verificar) { // Add check for query failure
        throw new Exception("Error al verificar inscripción existente: " . pg_last_error($conn));
    }

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

    // --- Manejo de comprobante de pago para eventos pagados ---
    if ($estado_pago === 'Pendiente') {
        if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception("Se requiere un comprobante de pago para eventos pagados.");
        }

        $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipo_archivo = $_FILES['comprobante']['type'];
        $tamano_archivo = $_FILES['comprobante']['size'];

        if (!in_array($tipo_archivo, $permitidos)) {
            throw new Exception("Solo se permiten archivos de imagen (JPEG, PNG, GIF) para el comprobante.");
        }
        if ($tamano_archivo > 2 * 1024 * 1024) { // 2MB
            throw new Exception("El tamaño del comprobante no debe superar los 2MB.");
        }

        $oid = pg_lo_import($conn, $_FILES['comprobante']['tmp_name']);
        if ($oid === false) {
            throw new Exception("Error al subir el comprobante a la base de datos.");
        }

        // Insertar el OID del comprobante en la tabla IMAGENES
        $sql_imagen = "INSERT INTO IMAGENES (ID_INS, COMPROBANTE_PAG_OID) VALUES ($1, $2)";
        $result_imagen = pg_query_params($conn, $sql_imagen, array($id_inscripcion, $oid));
        if (!$result_imagen) {
            throw new Exception("Error al asociar el comprobante con la inscripción: " . pg_last_error($conn));
        }
    }

    // --- Manejo de requisitos 'actual' que requieren subida de PDF ---
    // 1. Obtener los requisitos del evento que son de tipo 'actual'
    $sql_requisitos_actuales = "SELECT er.id_req, r.nom_req 
                                FROM eventos_requisitos er
                                JOIN requisitos r ON er.id_req = r.id_req
                                WHERE er.id_eve_cur = $1 AND er.valor_req = 'actual'";
    $result_requisitos_actuales = pg_query_params($conn, $sql_requisitos_actuales, array($id_evento));

    if (!$result_requisitos_actuales) {
        throw new Exception("Error al obtener requisitos del evento: " . pg_last_error($conn));
    }

    $requisitos_actuales = pg_fetch_all($result_requisitos_actuales);

    // 2. Procesar cada archivo de requisito subido
    foreach ($requisitos_actuales as $req) {
        $id_req = $req['id_req'];
        // Frontend debe nombrar los inputs de archivos como 'requisito_file_[ID_REQ]'
        $nombre_input_file = "requisito_file_" . $id_req; 

        if (isset($_FILES[$nombre_input_file]) && $_FILES[$nombre_input_file]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$nombre_input_file];

            $permitidos_req = ['application/pdf']; // Solo PDFs para requisitos
            $tipo_archivo_req = $file['type'];
            $tamano_archivo_req = $file['size'];

            if (!in_array($tipo_archivo_req, $permitidos_req)) {
                throw new Exception("El archivo para el requisito '" . htmlspecialchars($req['nom_req']) . "' debe ser un PDF.");
            }
            if ($tamano_archivo_req > 5 * 1024 * 1024) { // Por ejemplo, 5MB para requisitos
                throw new Exception("El tamaño del archivo para el requisito '" . htmlspecialchars($req['nom_req']) . "' no debe superar los 5MB.");
            }

            $oid_req = pg_lo_import($conn, $file['tmp_name']);
            if ($oid_req === false) {
                throw new Exception("Error al subir el archivo para el requisito '" . htmlspecialchars($req['nom_req']) . "' a la base de datos.");
            }

            // Insertar en evidencias_requisitos
            $sql_evidencia = "INSERT INTO evidencias_requisitos 
                                (id_ins, id_req, archivo_oid, estado_validacion, fecha_subida) 
                                VALUES ($1, $2, $3, $4, CURRENT_TIMESTAMP)";
            $result_evidencia = pg_query_params($conn, $sql_evidencia, array(
                $id_inscripcion,
                $id_req,
                $oid_req,
                'Pendiente' // Estado inicial de validación
            ));

            if (!$result_evidencia) {
                throw new Exception("Error al asociar el archivo con el requisito '" . htmlspecialchars($req['nom_req']) . "': " . pg_last_error($conn));
            }
        } else {
            // Si el requisito es 'actual' y no se subió un archivo, se considera un error
            throw new Exception("Falta el archivo para el requisito '" . htmlspecialchars($req['nom_req']) . "'.");
        }
    }

    // Insertar en NOTAS_ASISTENCIAS con valores por defecto
    $sql_notas = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS, PORC_ASI_NOT_ASI, NOT_FIN_NOT_ASI, FINALIZADO) 
                  VALUES ($1, null, null, false)";
    $result_notas = pg_query_params($conn, $sql_notas, array($id_inscripcion));
    if (!$result_notas) {
        throw new Exception("Error al crear el registro de notas/asistencias: " . pg_last_error($conn));
    }

    pg_query($conn, "COMMIT");

    sendJsonResponse(true, 'Inscripción realizada exitosamente.', 200, ['id_inscripcion' => $id_inscripcion]);

} catch (Exception $e) {
    if (isset($conn)) {
        pg_query($conn, "ROLLBACK");
    }
    sendJsonResponse(false, $e->getMessage(), 500);

} finally {
    if (isset($conn) && is_resource($conn)) {
        pg_close($conn);
    }
}
?>