<?php
require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

$idEvento = $_GET['idEvento'] ?? null;

if (!$idEvento) exit(json_encode([]));

$stmt = $conn->prepare("
  SELECT r.NOM_REQ
  FROM EVENTOS_REQUISITOS er
  JOIN REQUISITOS r ON er.ID_REQ = r.ID_REQ
  WHERE er.ID_EVE_CUR = :id
");
$stmt->execute([':id' => $idEvento]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
