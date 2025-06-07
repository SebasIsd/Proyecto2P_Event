<?php
require_once ("../conexion/conexion.php");

try {
    $conn = CConexion::ConexionBD();

    $sql = "SELECT * FROM eventos_cursos ORDER BY id_eve_cur DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($eventos);
} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error al obtener eventos: ' . $e->getMessage()]);
}
?>
