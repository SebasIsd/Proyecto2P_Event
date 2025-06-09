<?php
require_once '../conexion/conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if (!$id) {
        echo json_encode(['error' => 'ID no proporcionado']);
        exit;
    }

    try {
        $conn = CConexion::ConexionBD();
        
        // Begin transaction to ensure all deletions are successful
        $conn->beginTransaction();

        // First, delete related records in other tables due to foreign key constraints
        // Order matters here - we need to delete from tables that reference INSCRIPCIONES first
        
        // Delete from IMAGENES (references INSCRIPCIONES)
        $sql = "DELETE FROM IMAGENES WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Delete from CERTIFICADOS (references INSCRIPCIONES)
        $sql = "DELETE FROM CERTIFICADOS WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Delete from NOTAS_ASISTENCIAS (references INSCRIPCIONES)
        $sql = "DELETE FROM NOTAS_ASISTENCIAS WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Delete from PAGOS (references INSCRIPCIONES)
        $sql = "DELETE FROM PAGOS WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Finally, delete the inscription itself
        $sql = "DELETE FROM INSCRIPCIONES WHERE ID_INS = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Commit the transaction if all deletions were successful
        $conn->commit();
        
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        // Roll back the transaction if any error occurs
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollBack();
        }
        echo json_encode(['error' => 'Error al eliminar inscripción: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Método no permitido']);
}