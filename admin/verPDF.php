<?php
require_once '../conexion/conexion.php';

if (!isset($_GET['id_ins']) || !isset($_GET['id_req'])) {
    http_response_code(400);
    echo "Faltan parámetros.";
    exit;
}

$id_ins = (int)$_GET['id_ins'];
$id_req = (int)$_GET['id_req'];

try {
    // Obtener el OID desde la tabla EVIDENCIAS_REQUISITOS
    $connPDO = CConexion::ConexionBD();
    $stmt = $connPDO->prepare("SELECT ARCHIVO_OID FROM EVIDENCIAS_REQUISITOS WHERE ID_INS = :id_ins AND ID_REQ = :id_req");
    $stmt->execute([':id_ins' => $id_ins, ':id_req' => $id_req]);
    $archivo = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$archivo || !$archivo['archivo_oid']) {
        http_response_code(404);
        echo "Archivo no encontrado.";
        exit;
    }

    $oid = (int)$archivo['archivo_oid'];

    // Conexión con pg_connect para manejo de OID
    $connPG = pg_connect("host=maglev.proxy.rlwy.net port=10622 dbname=railway user=postgres password=knFFZcmuIhowgwGNQmnUMGuSMxkNTdqA");
    if (!$connPG) {
        http_response_code(500);
        echo "Error al conectar a PostgreSQL.";
        exit;
    }

    pg_query($connPG, "BEGIN");
    $lo = pg_lo_open($connPG, $oid, "r");

    if (!$lo) {
        pg_query($connPG, "ROLLBACK");
        http_response_code(404);
        echo "No se pudo abrir el archivo.";
        exit;
    }

    $contenido = '';
    while ($chunk = pg_lo_read($lo, 8192)) {
        $contenido .= $chunk;
    }

    pg_lo_close($lo);
    pg_query($connPG, "COMMIT");

    // Mostrar PDF
    header("Content-Type: application/pdf");
    header("Content-Disposition: inline; filename=\"evidencia.pdf\"");
    echo $contenido;

} catch (Exception $e) {
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
