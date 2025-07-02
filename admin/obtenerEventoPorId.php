<?php
require_once '../conexion/conexion.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['error' => 'ID no proporcionado']);
    exit;
}

try {
    $conn = CConexion::ConexionBD();

    // Consulta con JOIN para traer tipo de evento e imagen
    $stmt = $conn->prepare("
        SELECT 
            ec.*, 
            te.nom_tipo_eve AS tip_eve, 
            te.img_tipo_eve AS img_tipo_eve
        FROM eventos_cursos ec
        LEFT JOIN tipos_evento te ON ec.id_tipo_eve = te.id_tipo_eve
        WHERE ec.id_eve_cur = ?
    ");
    $stmt->execute([$id]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$evento) {
        echo json_encode(['error' => 'Evento no encontrado']);
        exit;
    }

    // Obtener carreras asociadas
    $stmtCarreras = $conn->prepare("
        SELECT c.nom_car 
        FROM eventos_carreras ec
        JOIN carreras c ON ec.id_car = c.id_car
        WHERE ec.id_eve_cur = ?
    ");
    $stmtCarreras->execute([$id]);
    $evento['carreras'] = $stmtCarreras->fetchAll(PDO::FETCH_COLUMN);

    // Obtener requisitos
    $stmtRequisitos = $conn->prepare("
        SELECT r.nom_req AS nombre, er.valor_req AS valor
        FROM eventos_requisitos er
        JOIN requisitos r ON er.id_req = r.id_req
        WHERE er.id_eve_cur = ?
    ");
    $stmtRequisitos->execute([$id]);
    $evento['requisitos'] = $stmtRequisitos->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($evento);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al obtener evento: ' . $e->getMessage()]);
}
?>
