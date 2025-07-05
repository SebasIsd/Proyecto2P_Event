<?php
require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

$idEvento = $_GET['idEvento'] ?? null;

if (!$idEvento) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT 
    er.ID_REQ,
    er.ID_EVE_CUR,
    ir.ID_INS,
    r.NOM_REQ,
    e.CED_USU,
    CONCAT(u.NOM_PRI_USU, ' ', u.NOM_SEG_USU, ' ', u.APE_PRI_USU, ' ', u.APE_SEG_USU) AS nombre_completo,
    ir.ARCHIVO_OID,
    ir.ESTADO_VALIDACION,
    ir.OBSERVACION
FROM EVENTOS_REQUISITOS er
JOIN REQUISITOS r ON r.ID_REQ = er.ID_REQ
JOIN INSCRIPCIONES e ON e.ID_EVE_CUR = er.ID_EVE_CUR
JOIN USUARIOS u ON u.CED_USU = e.CED_USU
JOIN EVIDENCIAS_REQUISITOS ir ON ir.ID_INS = e.ID_INS AND ir.ID_REQ = er.ID_REQ
WHERE er.ID_EVE_CUR = :idEvento
AND LOWER(r.NOM_REQ) NOT IN ('nota mínima', 'asistencia mínima')
ORDER BY nombre_completo, r.NOM_REQ;
";

$stmt = $conn->prepare($sql);
$stmt->execute([':idEvento' => $idEvento]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));