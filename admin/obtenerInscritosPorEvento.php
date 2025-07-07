<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

if (!isset($_GET['idEvento'])) {
    echo json_encode([]);
    exit;
}

$idEvento = $_GET['idEvento'];

$sql = "
    SELECT 
        ins.ID_INS,
        CONCAT(u.NOM_PRI_USU, ' ', u.NOM_SEG_USU, ' ', u.APE_PRI_USU, ' ', u.APE_SEG_USU) AS nombre_completo,
        na.NOT_FIN_NOT_ASI,
        na.PORC_ASI_NOT_ASI
    FROM INSCRIPCIONES ins
    INNER JOIN USUARIOS u ON ins.CED_USU = u.CED_USU
    LEFT JOIN NOTAS_ASISTENCIAS na ON na.ID_INS = ins.ID_INS AND (na.FINALIZADO IS FALSE OR na.FINALIZADO IS NULL)
    WHERE ins.ID_EVE_CUR = :idEvento
";

$stmt = $conn->prepare($sql);
$stmt->execute([':idEvento' => $idEvento]);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($datos);
?>

