<?php
session_start();
require_once "../../includes/conexion1.php";

// Configura esto tú mismo para probar directamente
$_SESSION['cedula'] = '0504171588'; // Asegúrate de que este valor exista en la tabla USUARIOS

// Datos fijos de prueba (ajusta según tu base)
$cedula = $_SESSION['cedula'];
$id_evento = 1; // Asegúrate que este ID exista en EVENTOS_CURSOS
$fecha_inscripcion = date('Y-m-d');
$estado_pago = 'Pagado'; // O 'Pendiente'
$fecha_cierre = date('Y-m-d', strtotime('+30 days'));

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();

    if (!$conn) {
        throw new Exception("No se pudo conectar a la base de datos.");
    }

    // Insertar en INSCRIPCIONES
    $sql = "INSERT INTO INSCRIPCIONES (CED_USU, ID_EVE_CUR, FEC_INI_INS, FEC_CIE_INS, EST_PAG_INS) 
            VALUES ($1, $2, $3, $4, $5) RETURNING ID_INS";

    $result = pg_query_params($conn, $sql, array(
        $cedula,
        $id_evento =12,
        $fecha_inscripcion,
        $fecha_cierre,
        $estado_pago
    ));

    if (!$result) {
        throw new Exception("Error en el INSERT: " . pg_last_error($conn));
    }

    $id_ins = pg_fetch_result($result, 0, 0);
    echo "✅ Inscripción exitosa. ID_INS = $id_ins";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
