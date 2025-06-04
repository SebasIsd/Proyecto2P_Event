<?php
require_once '../conexion/conexion.php';

$data = json_decode(file_get_contents('php://input'), true);

try {
    $conn = CConexion::ConexionBD();
    $sql = "UPDATE eventos_cursos SET
                tit_eve_cur = :titulo,
                des_eve_cur = :descripcion,
                fec_ini_eve_cur = :fecha_inicio,
                fec_fin_eve_cur = :fecha_fin,
                cos_eve_cur = :costo,
                tip_eve = :tipo,
                mod_eve_cur = :modalidad
            WHERE id_eve_cur = :id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':titulo' => $data['titulo'],
        ':descripcion' => $data['descripcion'],
        ':fecha_inicio' => $data['fecha_inicio'],
        ':fecha_fin' => $data['fecha_fin'],
        ':costo' => $data['costo'],
        ':tipo' => $data['tipo'],
        ':modalidad' => $data['modalidad'],
        ':id' => $data['id']
    ]);

    echo json_encode(['mensaje' => 'Evento actualizado correctamente']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al actualizar evento: ' . $e->getMessage()]);
}
?>
