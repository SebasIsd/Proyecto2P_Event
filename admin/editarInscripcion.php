<?php
include_once '../conexion/conexion2.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $estado_pago = $_POST['estado_pago'] ?? null;

    if (!$id || !$estado_pago) {
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    try {
        $conn = CConexion::ConexionBD();

        $sql = "UPDATE INSCRIPCIONES SET EST_PAG_INS = :estado_pago WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':estado_pago', $estado_pago);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Error al actualizar inscripción: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}
