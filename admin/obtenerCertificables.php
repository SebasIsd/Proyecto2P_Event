<?php
include_once '../conexion/conexion.php';
header('Content-Type: application/json');

try {
    $conn = CConexion::ConexionBD();

   $stmt = $conn->prepare("
    SELECT 
        I.ID_INS AS id_ins,
        U.CED_USU AS cedula,
        CONCAT(U.NOM_PRI_USU, ' ', U.APE_PRI_USU) AS nombre_completo,
        E.TIT_EVE_CUR AS titulo_evento,
        N.NOT_FIN_NOT_ASI AS nota,
        N.PORC_ASI_NOT_ASI AS porcentaje_asistencia,
        E.MOD_EVE_CUR AS modalidad,
        E.FEC_FIN_EVE_CUR AS fecha_fin_evento,
        I.EST_PAG_INS AS estado_pago,
        P.FEC_PAG AS fecha_pago
    FROM INSCRIPCIONES I
    JOIN USUARIOS U ON I.CED_USU = U.CED_USU
    JOIN EVENTOS_CURSOS E ON I.ID_EVE_CUR = E.ID_EVE_CUR
    JOIN NOTAS_ASISTENCIAS N ON I.ID_INS = N.ID_INS
    LEFT JOIN (
        SELECT DISTINCT ON (ID_INS) * 
        FROM PAGOS 
        ORDER BY ID_INS, FEC_PAG DESC
    ) P ON I.ID_INS = P.ID_INS
    WHERE 
        N.NOT_FIN_NOT_ASI >= 8 AND 
        N.PORC_ASI_NOT_ASI >= 80 AND (
            E.MOD_EVE_CUR = 'Gratis' OR (
                I.EST_PAG_INS = 'Pagado' AND 
                P.FEC_PAG <= E.FEC_FIN_EVE_CUR - INTERVAL '1 day'
            )
        )
");


    $stmt->execute();
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>