<?php
include_once '../conexion/conexion2.php';
header('Content-Type: application/json');

if (!isset($_GET['idEvento'])) {
    echo json_encode(['error' => 'Falta el parámetro idEvento']);
    exit;
}

$idEvento = $_GET['idEvento'];

try {
    $conn = CConexion::ConexionBD();
    $sql = "
        SELECT 
            I.ID_INS,
            CONCAT(U.NOM_PRI_USU, ' ', U.NOM_SEG_USU, ' ', U.APE_PRI_USU, ' ', U.APE_SEG_USU) AS nombre_completo,
            N.NOT_FIN_NOT_ASI,
            N.ASI_NOT_ASI
        FROM INSCRIPCIONES I
        INNER JOIN USUARIOS U ON I.CED_USU = U.CED_USU
        LEFT JOIN NOTAS_ASISTENCIAS N ON N.ID_INS = I.ID_INS
        WHERE I.ID_EVE_CUR = :idEvento
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':idEvento', $idEvento);
    $stmt->execute();

    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>
