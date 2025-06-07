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
$estado_pago = $_POST['estado_pago']; // Este ahora viene definido por el tipo de evento
$tipo_evento = $_POST['tipo_evento']; // 'pagado' o 'gratis'

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
        $estado_pago // Usamos el estado definido por el tipo de evento
    ));
    
    $id_inscripcion = pg_fetch_result($result, 0, 0);

    // Solo manejar comprobante si es evento pagado
    if ($tipo_evento === 'pagado' && isset($_FILES['comprobante'])) {
        $directorio = "../../comprobantes/";
        
        // Crear directorio si no existe
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // Validar que sea una imagen
        $permitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipo_archivo = $_FILES['comprobante']['type'];
        
        if (!in_array($tipo_archivo, $permitidos)) {
            throw new Exception("Solo se permiten archivos de imagen (JPEG, PNG, GIF)");
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "comprobante_" . $id_inscripcion . "_" . time() . "." . $extension;
        $ruta_archivo = $directorio . $nombre_archivo;
        
        // Mover el archivo subido
        if (!move_uploaded_file($_FILES['comprobante']['tmp_name'], $ruta_archivo)) {
            throw new Exception("Error al subir el comprobante de pago");
        }
        
        // Guardar en la tabla IMAGENES
        $sql_imagen = "INSERT INTO IMAGENES (ID_INS, COMPROBANTE_PAG) VALUES ($1, $2)";
        pg_query_params($conn, $sql_imagen, array($id_inscripcion, $ruta_archivo));
    }

    // Crear registro en NOTAS_ASISTENCIAS
    $sql_notas = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS) VALUES ($1)";
    pg_query_params($conn, $sql_notas, array($id_inscripcion));

    // Confirmar transacción
    pg_query($conn, "COMMIT");

    // Limpiar buffer de salida antes de redireccionar
    if (ob_get_length()) ob_clean();
    
    header("Location: inscripciones.php?success");
    exit();

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