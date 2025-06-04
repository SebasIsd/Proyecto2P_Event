<?php
include_once("../conexion/conexion2.php");

if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

$id = intval($_GET['id']);

try {
    $conn = CConexion::ConexionBD();

    $sql = "DELETE FROM eventos_cursos WHERE id_eve_cur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);

    echo json_encode(['mensaje' => 'Evento eliminado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al eliminar el evento: ' . $e->getMessage()]);
}
?>