<?php
include_once '../conexion/conexion.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$idIns = $data['id_ins'];

try {
  $conn = CConexion::ConexionBD();

  $sql = "INSERT INTO CERTIFICADOS (ID_INS, FEC_EMI_CER) VALUES (:id, CURRENT_DATE)";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':id', $idIns);
  $stmt->execute();

  echo json_encode(['success' => true]);
} catch (PDOException $e) {
  echo json_encode(['error' => $e->getMessage()]);
}
?>
