<?php
header('Content-Type: application/json');
require_once '../conexion/conexion.php'; // Asegúrate que contiene la clase CConexion

$conn = CConexion::ConexionBD();

if (!$conn) {
    echo json_encode(['error' => true, 'mensaje' => 'No se pudo conectar a la base de datos']);
    exit;
}

try {
    $sql = "SELECT ID_EVE_CUR FROM FAVORITOS_EVENTO";
    $stmt = $conn->query($sql);
    $favoritos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($favoritos);
} catch (PDOException $e) {
    echo json_encode(['error' => true, 'mensaje' => 'Error al obtener favoritos']);
}
?>
