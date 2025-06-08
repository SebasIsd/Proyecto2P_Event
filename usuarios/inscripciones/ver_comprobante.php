<?php
// ver_comprobante.php
session_start();
require_once "../../includes/conexion1.php";

if (!isset($_SESSION['usuario'])) {
    header("HTTP/1.1 403 Forbidden");
    exit();
}

if (!isset($_GET['id_inscripcion'])) {
    header("Content-Type: image/png");
    readfile("../../images/error_comprobante.png");
    exit();
}

$id_inscripcion = filter_var($_GET['id_inscripcion'], FILTER_VALIDATE_INT);

$conexion = new Conexion();
$conn = $conexion->getConexion();

try {
    $sql = "SELECT COMPROBANTE_PAG_OID FROM IMAGENES WHERE ID_INS = $1";
    $result = pg_query_params($conn, $sql, array($id_inscripcion));
    
    if (pg_num_rows($result) === 0) {
        throw new Exception("No existe comprobante");
    }
    
    $oid = pg_fetch_result($result, 0, 0);
    
    header("Content-Type: image/jpeg");
    header("Content-Disposition: inline; filename=comprobante_$id_inscripcion.jpg");
    
    pg_query($conn, "BEGIN");
    $lo = pg_lo_open($conn, $oid, "r");
    
    if (!$lo) {
        throw new Exception("Error al abrir archivo");
    }
    
    while ($data = pg_lo_read($lo, 8192)) {
        echo $data;
    }
    
    pg_lo_close($lo);
    pg_query($conn, "COMMIT");
    
} catch (Exception $e) {
    if (isset($conn)) pg_query($conn, "ROLLBACK");
    header("Content-Type: image/png");
    readfile("../../images/error_comprobante.png");
} finally {
    if (isset($conn)) pg_close($conn);
}
?>