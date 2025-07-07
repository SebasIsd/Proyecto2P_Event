<?php
// Activar reporte de errores para desarrollo
ini_set('display_errors', 0); // Considerar cambiar a 1 para depuración en desarrollo
ini_set('log_errors', 1);
ini_set('error_log', '../../logs/php_errors.log');

header('Content-Type: application/json');
require_once '../../includes/conexion1.php';

session_start();

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    if (!$conn) {
        throw new Exception("Error de conexión a la base de datos");
    }

    // Obtener cédula desde la sesión
    $cedula = isset($_SESSION['cedula']) ? $_SESSION['cedula'] : null;

    if (!$cedula) {
        throw new Exception("Cédula no encontrada en la sesión.");
    }

    // CONSULTA PARA TRAER EVENTOS CON TODOS SUS REQUISITOS
    $query = "SELECT 
                ec.ID_EVE_CUR as codigo,
                ec.TIT_EVE_CUR as titulo,
                ec.DES_EVE_CUR as descripcion,
                ec.FEC_INI_EVE_CUR as fechaInicio,
                ec.FEC_FIN_EVE_CUR as fechaFin,
                ec.COS_EVE_CUR as costo,
                ec.MOD_EVE_CUR as tipo_evento,
                CASE
                    WHEN COUNT(er.id_req) = 0 THEN '[]'::jsonb
                    ELSE jsonb_agg(jsonb_build_object('nom_req', r.nom_req, 'valor_req', er.valor_req))
                END as requisitos
            FROM EVENTOS_CURSOS ec
            LEFT JOIN eventos_requisitos er ON ec.ID_EVE_CUR = er.id_eve_cur
            LEFT JOIN requisitos r ON er.id_req = r.id_req
            WHERE ec.FEC_FIN_EVE_CUR >= CURRENT_DATE
            AND ec.ID_EVE_CUR NOT IN (
                SELECT i.ID_EVE_CUR 
                FROM INSCRIPCIONES i 
                WHERE i.CED_USU = $1
            )
            GROUP BY 
                ec.ID_EVE_CUR,
                ec.TIT_EVE_CUR,
                ec.DES_EVE_CUR,
                ec.FEC_INI_EVE_CUR,
                ec.FEC_FIN_EVE_CUR,
                ec.COS_EVE_CUR,
                ec.MOD_EVE_CUR
            ORDER BY ec.FEC_INI_EVE_CUR ASC, ec.TIT_EVE_CUR ASC";

    $params = [$cedula];    
    $result = pg_query_params($conn, $query, $params);

    if (!$result) {
        throw new Exception("Error en la consulta SQL: " . pg_last_error($conn));
    }

    $eventos = [];
    while ($row = pg_fetch_assoc($result)) {
        // Decodificar la columna 'requisitos' que viene como JSONB
        $row['requisitos'] = json_decode($row['requisitos'], true);
        $eventos[] = $row;
    }

    if (ob_get_length()) ob_clean();
    echo json_encode($eventos);

} catch (Exception $e) {
    if (ob_get_length()) ob_clean();
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    // Cerrar conexión si está abierta
    if (isset($conn) && is_resource($conn)) {
        pg_close($conn);
    }
}
?>