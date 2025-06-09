<?php
require_once 'conexion2.php';
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["success" => false, "error" => "Método no permitido"]);
    exit;
}

$accion = $_POST['accion'] ?? '';

try {
    $conn = CConexion2::obtenerConexion();
    if (!$conn) throw new Exception("Error de conexión");

    if ($accion === 'buscar') {
        $cedula = $_POST['cedula'] ?? '';
        if (!$cedula) throw new Exception("Cédula requerida");

        $stmt = $conn->prepare("
            SELECT i.ID_INS as id_inscripcion, ec.TIT_EVE_CUR as nombre_evento,
                ec.TIP_EVE as tipo_evento, ec.FEC_INI_EVE_CUR as fecha_inicio,
                ec.FEC_FIN_EVE_CUR as fecha_fin, ec.COS_EVE_CUR as costo_evento,
                i.EST_PAG_INS as estado_pago, img.COMPROBANTE_PAG_OID as comprobante_oid
            FROM INSCRIPCIONES i
            INNER JOIN EVENTOS_CURSOS ec ON ec.ID_EVE_CUR = i.ID_EVE_CUR
            LEFT JOIN IMAGENES img ON img.ID_INS = i.ID_INS
            WHERE i.CED_USU = :cedula
            ORDER BY ec.FEC_INI_EVE_CUR DESC
        ");
        $stmt->execute(['cedula' => $cedula]);
        $inscripciones = $stmt->fetchAll();

        echo json_encode(['success' => true, 'inscripciones' => $inscripciones]);
        /*$inscripciones = [];
        foreach ($rows as $row) {
            $id = $row['id_inscripcion'];
            if (!isset($inscripciones[$id])) {
                $inscripciones[$id] = [
                    'id_inscripcion' => $id,
                    'nombre_evento' => $row['nombre_evento'],
                    'tipo_evento' => $row['tipo_evento'],
                    'fecha_inicio' => $row['fecha_inicio'],
                    'fecha_fin' => $row['fecha_fin'],
                    'costo_evento' => $row['costo_evento'],
                    'estado_pago' => $row['estado_pago'],
                    'pagos' => []
                ];
            }
            if ($row['id_pago']) {
                $inscripciones[$id]['pagos'][] = [
                    'fecha_pago' => $row['fecha_pago'],
                    'monto_pago' => $row['monto_pago'],
                    'metodo_pago' => $row['metodo_pago']
                ];
            }
        }

        echo json_encode(['success' => true, 'inscripciones' => array_values($inscripciones)]);*/
    }

    elseif ($accion === 'actualizar') {
        $id = $_POST['id_inscripcion'] ?? '';
        $estado = $_POST['estado_pago'] ?? '';
        if (!$id || !$estado) throw new Exception("Datos incompletos");

        $stmt = $conn->prepare("UPDATE INSCRIPCIONES SET EST_PAG_INS = :estado WHERE ID_INS = :id");
        $success = $stmt->execute(['estado' => $estado, 'id' => $id]);

        echo json_encode(['success' => $success]);
    }

    else {
        throw new Exception("Acción inválida");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    CConexion2::cerrarConexion();
}
?>
