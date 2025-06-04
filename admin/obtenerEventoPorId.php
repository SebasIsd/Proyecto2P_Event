<?php
include_once("../conexion/conexion2.php");

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

try {
    $conn = CConexion::ConexionBD();
    $stmt = $conn->prepare("SELECT * FROM eventos_cursos WHERE id_eve_cur = ?");
    $stmt->execute([$id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($evento);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener evento: ' . $e->getMessage()]);
}
?>