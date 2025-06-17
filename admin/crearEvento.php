<?php
include '../conexion.php';

try {
    // Datos principales
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $modalidad = $_POST['modalidad'];
    $costo = $_POST['costo'];
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];

    // Insertar evento
    $sql = "INSERT INTO eventos_cursos (tit_eve_cur, des_eve_cur, fec_ini_eve_cur, fec_fin_eve_cur, cos_eve_cur, mod_eve_cur)
            VALUES (?, ?, ?, ?, ?, ?) RETURNING id_eve_cur";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$titulo, $descripcion, $fechaInicio, $fechaFin, $costo, $modalidad]);
    $eventoId = $stmt->fetchColumn();

    // Tipos de evento
    if (!empty($_POST['tiposEvento'])) {
        foreach ($_POST['tiposEvento'] as $tipoId) {
            $conn->prepare("INSERT INTO eventos_tipos (id_eve_cur, id_tipo_eve) VALUES (?, ?)")
                 ->execute([$eventoId, $tipoId]);
        }
    }

    // Carreras
    if (!empty($_POST['carreras'])) {
        foreach ($_POST['carreras'] as $carreraId) {
            $conn->prepare("INSERT INTO eventos_carreras (id_eve_cur, id_car) VALUES (?, ?)")
                 ->execute([$eventoId, $carreraId]);
        }
    }

    // Requisitos
    if (!empty($_POST['requisitos'])) {
        foreach ($_POST['requisitos'] as $reqId) {
            $valor = null;

            // ID de requisitos conocidos
            if ($reqId == 1 && isset($_POST['valor_nota'])) {
                $valor = $_POST['valor_nota'];
            } elseif ($reqId == 2 && isset($_POST['valor_asistencia'])) {
                $valor = $_POST['valor_asistencia'];
            }

            $conn->prepare("INSERT INTO eventos_requisitos (id_eve_cur, id_req, valor_req) VALUES (?, ?, ?)")
                 ->execute([$eventoId, $reqId, $valor]);
        }
    }

    echo "Evento guardado correctamente.";
} catch (Exception $e) {
    http_response_code(500);
    echo "Error al guardar el evento: " . $e->getMessage();
}
?>
