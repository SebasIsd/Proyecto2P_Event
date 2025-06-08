<?php
session_start();
require_once "../../includes/conexion1.php";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Sesión expirada']);
        exit();
    }
    header("Location: ../../login.php");
    exit();
}

// Verificar que se enviaron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['evento'])) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Datos incompletos']);
        exit();
    }
    header("Location: inscripciones.php?error=Datos incompletos");
    exit();
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Obtener y validar datos
$cedula = $_SESSION['cedula'];
$id_evento = filter_var($_POST['evento'], FILTER_VALIDATE_INT);
$fecha_inscripcion = $_POST['fecha_inscripcion'];
$estado_pago = $_POST['estado_pago']; // Definido por el tipo de evento
$tipo_evento = $_POST['tipo_evento']; // 'pagado' o 'gratis'

if (!$id_evento || !$fecha_inscripcion || !$estado_pago) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Datos de validación incompletos']);
        exit();
    }
    header("Location: inscripciones.php");
    exit();
}

try {
    // Verificar si el usuario ya está inscrito
    $sql_verificar = "SELECT 1 FROM INSCRIPCIONES 
                      WHERE CED_USU = $1 AND ID_EVE_CUR = $2";
    $result = pg_query_params($conn, $sql_verificar, array($cedula, $id_evento));
    
    if (pg_num_rows($result) > 0) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(409);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Ya está inscrito en este evento']);
            exit();
        }
        header("Location: inscripciones.php?error=Ya está inscrito en este evento");
        exit();
    }

    // Iniciar transacción
    pg_query($conn, "BEGIN");

    // Insertar la inscripción
    $sql_inscripcion = "INSERT INTO INSCRIPCIONES 
                         (CED_USU, ID_EVE_CUR, FEC_INI_INS, FEC_CIE_INS, EST_PAG_INS) 
                         VALUES ($1, $2, $3, $4, $5) RETURNING ID_INS";
    
    // Calcular fecha de cierre (30 días después)
    $fecha_cierre = date('Y-m-d', strtotime($fecha_inscripcion . ' +30 days'));
    
    $result = pg_query_params($conn, $sql_inscripcion, array(
        $cedula,
        $id_evento,
        $fecha_inscripcion,
        $fecha_cierre,
        $estado_pago
    ));
    
    $id_inscripcion = pg_fetch_result($result, 0, 0);

    // Manejar comprobante si es evento pagado
    if ($tipo_evento === 'pagado' && isset($_FILES['comprobante']) && $_FILES['comprobante']['error'] === UPLOAD_ERR_OK) {
        // Validar tipo de imagen
        $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipo_archivo = $_FILES['comprobante']['type'];
        
        if (!in_array($tipo_archivo, $permitidos)) {
            throw new Exception("Solo se permiten archivos de imagen (JPEG, PNG, GIF)");
        }
        
        // Importar archivo a PostgreSQL como Large Object
        $oid = pg_lo_import($conn, $_FILES['comprobante']['tmp_name']);
        
        if ($oid === false) {
            throw new Exception("Error al subir el comprobante a la base de datos");
        }
        
        // Guardar en tabla IMAGENES
        $sql_imagen = "INSERT INTO IMAGENES (ID_INS, COMPROBANTE_PAG_OID) VALUES ($1, $2)";
        pg_query_params($conn, $sql_imagen, array($id_inscripcion, $oid));
    }

    // Crear registro en NOTAS_ASISTENCIAS
    $sql_notas = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS) VALUES ($1)";
    pg_query_params($conn, $sql_notas, array($id_inscripcion));

    // Confirmar transacción
    pg_query($conn, "COMMIT");

    // Limpiar buffer de salida
    if (ob_get_length()) ob_clean();
    
    // Responder según tipo de petición
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        http_response_code(200);
        echo json_encode([
            'success' => true, 
            'message' => 'Inscripción realizada exitosamente',
            'id_inscripcion' => $id_inscripcion
        ]);
        exit();
    } else {
        header("Location: ../mis_eventos.php?success=InscripcionRealizada");
        exit();
    }

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($conn)) {
        pg_query($conn, "ROLLBACK");
    }
    
    // Limpiar buffer de salida
    if (ob_get_length()) ob_clean();
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    } else {
        header("Location: inscripciones.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} finally {
    // Cerrar conexión
    if (isset($conn)) {
        pg_close($conn);
    }
}