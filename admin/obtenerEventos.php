<?php
require_once ("../conexion/conexion.php");

try {
    $conn = CConexion::ConexionBD();

    $sql = "SELECT ec.*, te.img_tipo_eve
FROM eventos_cursos ec
JOIN tipos_evento te ON ec.id_tipo_eve = te.id_tipo_eve
";
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
