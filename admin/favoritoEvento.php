<?php
header('Content-Type: application/json');
require_once '../conexion/conexion.php'; // Asegúrate que contiene la clase CConexion

$conn = CConexion::ConexionBD();

if (!$conn) {
    echo json_encode(['ok' => false, 'mensaje' => 'No se pudo conectar a la base de datos']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['id']) || !isset($data['accion'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
    exit;
}

$idEvento = intval($data['id']);
$accion = $data['accion'];

try {
    if ($accion === 'agregar') {
        $stmt = $conn->prepare("INSERT INTO FAVORITOS_EVENTO (ID_EVE_CUR) VALUES (:id)");
        $stmt->execute(['id' => $idEvento]);
        echo json_encode(['ok' => true]);
    } elseif ($accion === 'remover') {
        $stmt = $conn->prepare("DELETE FROM FAVORITOS_EVENTO WHERE ID_EVE_CUR = :id");
        $stmt->execute(['id' => $idEvento]);
        echo json_encode(['ok' => true]);
    } else {
        echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida']);
    }
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al actualizar favorito']);
}
?>
