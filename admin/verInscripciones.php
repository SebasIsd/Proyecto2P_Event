<?php
include_once '../conexion/conexion.php'; // Asegúrate de tener tu conexión bien configurada

header('Content-Type: application/json');

try {
    $conn = CConexion::ConexionBD();

    if (!$conn) {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo establecer la conexión"]);
        exit;
    }

    $sql = "SELECT 
                I.ID_INS As id_inscripcion,
                U.NOM_PRI_USU || ' ' || U.NOM_SEG_USU || ' ' || U.APE_PRI_USU || ' ' || U.APE_SEG_USU AS nombre_completo,
                E.TIT_EVE_CUR AS evento,
                 I.FEC_INI_INS AS fecha_inicio,
                I.FEC_CIE_INS AS fecha_cierre,
                I.EST_PAG_INS AS estado_pago
            FROM INSCRIPCIONES I
            INNER JOIN USUARIOS U ON I.CED_USU = U.CED_USU
            INNER JOIN EVENTOS_CURSOS E ON I.ID_EVE_CUR = E.ID_EVE_CUR";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $inscripciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($inscripciones);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Error al obtener inscripciones: " . $e->getMessage()]);
}
?>
