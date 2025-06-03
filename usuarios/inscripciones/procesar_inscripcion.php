<?php
session_start();
require_once "../../includes/conexion1.php";

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// Verificar que se enviaron los datos necesarios
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['evento'])) {
    header("Location: inscripciones.php?error=Datos incompletos");
    exit();
}

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Obtener y validar datos
$cedula = $_SESSION['cedula'];
$id_evento = filter_var($_POST['evento'], FILTER_VALIDATE_INT);
$fecha_inscripcion = $_POST['fecha_inscripcion'];
$estado_pago = $_POST['estado_pago'];

if (!$id_evento || !$fecha_inscripcion || !$estado_pago) {
    header("Location: inscripciones.php?error=Datos inválidos");
    exit();
}

try {
    // Verificar si el usuario ya está inscrito en este evento
    $sql_verificar = "SELECT 1 FROM INSCRIPCIONES 
                      WHERE CED_USU = $1 AND ID_EVE_CUR = $2";
    $result = pg_query_params($conn, $sql_verificar, array($cedula, $id_evento));
    
    if (pg_num_rows($result) > 0) {
        header("Location: inscripciones.php?error=Ya estás inscrito en este evento");
        exit();
    }

    // Iniciar transacción
    pg_query($conn, "BEGIN");

    // Insertar la inscripción
    $sql_inscripcion = "INSERT INTO INSCRIPCIONES 
                        (CED_USU, ID_EVE_CUR, FEC_INI_INS, FEC_CIE_INS, EST_PAG_INS) 
                        VALUES ($1, $2, $3, $4, $5) RETURNING ID_INS";
    
    // Calcular fecha de cierre (30 días después de la inscripción)
    $fecha_cierre = date('Y-m-d', strtotime($fecha_inscripcion . ' +30 days'));
    
    $result = pg_query_params($conn, $sql_inscripcion, array(
        $cedula,
        $id_evento,
        $fecha_inscripcion,
        $fecha_cierre,
        $estado_pago
    ));
    
    $id_inscripcion = pg_fetch_result($result, 0, 0);

    // Si el pago es "Pagado", registrar también el pago
    if ($estado_pago === 'Pagado' && isset($_POST['fecha_pago'])) {
        $fecha_pago = $_POST['fecha_pago'];
        $monto_pago = filter_var($_POST['monto_pago'], FILTER_VALIDATE_FLOAT);
        $metodo_pago = htmlspecialchars($_POST['metodo_pago']);
        
        if ($monto_pago === false || $monto_pago <= 0) {
            throw new Exception("Monto de pago inválido");
        }
        
        $sql_pago = "INSERT INTO PAGOS 
                     (ID_INS, FEC_PAG, MON_PAG, MET_PAG) 
                     VALUES ($1, $2, $3, $4)";
        
        pg_query_params($conn, $sql_pago, array(
            $id_inscripcion,
            $fecha_pago,
            $monto_pago,
            $metodo_pago
        ));
    }

    // Crear registro en NOTAS_ASISTENCIAS
    $sql_notas = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS) VALUES ($1)";
    pg_query_params($conn, $sql_notas, array($id_inscripcion));

    // Confirmar transacción
    pg_query($conn, "COMMIT");

    // Limpiar buffer de salida antes de redireccionar
    if (ob_get_length()) ob_clean();
    
    header("Location: inscripciones.php?");
    exit(); // Asegurarse de terminar la ejecución aquí

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if (isset($conn)) {
        pg_query($conn, "ROLLBACK");
    }
    // Limpiar buffer de salida antes de redireccionar
    if (ob_get_length()) ob_clean();
    header("Location: inscripciones.php?error=" . urlencode($e->getMessage()));
    exit();
}