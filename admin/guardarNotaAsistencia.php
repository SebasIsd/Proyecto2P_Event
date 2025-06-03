<?php
include_once '../conexion/conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);


$idIns = $data['id_ins'];
$nota = $data['nota'];
$asistencia = $data['asistencia'];
$porcentaje = $data['porcentaje']; 

try {
    $conn = CConexion::ConexionBD();

   
    $check = $conn->prepare("SELECT COUNT(*) FROM NOTAS_ASISTENCIAS WHERE ID_INS = :id");
    $check->bindParam(':id', $idIns);
    $check->execute();
    $existe = $check->fetchColumn();

    if ($existe > 0) {
       
        $sql = "UPDATE NOTAS_ASISTENCIAS 
                SET NOT_FIN_NOT_ASI = :nota, 
                    ASI_NOT_ASI = :asistencia, 
                    PORC_ASI_NOT_ASI = :porcentaje 
                WHERE ID_INS = :id";
    } else {
        
        $sql = "INSERT INTO NOTAS_ASISTENCIAS (ID_INS, NOT_FIN_NOT_ASI, ASI_NOT_ASI, PORC_ASI_NOT_ASI) 
                VALUES (:id, :nota, :asistencia, :porcentaje)";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $idIns);
    $stmt->bindParam(':nota', $nota);
    $stmt->bindParam(':asistencia', $asistencia);
    $stmt->bindParam(':porcentaje', $porcentaje);
    $stmt->execute();

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
