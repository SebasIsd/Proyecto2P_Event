<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

if (!$conn) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error al conectar con la base de datos']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    // 1. Validación mínima
    if (empty($_POST['titulo'])) {
        throw new Exception("El título del evento es obligatorio.");
    }

    // 2. Insertar nuevo tipo si viene desde "Otros"
    $nuevoTipoId = null;
    if (!empty($_POST['nuevoTipo'])) {
        $stmt = $conn->prepare("SELECT id_tipo_eve FROM tipos_evento WHERE nom_tipo_eve = ?");
        $stmt->execute([$_POST['nuevoTipo']]);
        $tipoExistente = $stmt->fetchColumn();

        if (!$tipoExistente) {
            $stmt = $conn->prepare("INSERT INTO tipos_evento (nom_tipo_eve) VALUES (?) RETURNING id_tipo_eve");
            $stmt->execute([$_POST['nuevoTipo']]);
            $nuevoTipoId = $stmt->fetchColumn();
        } else {
            $nuevoTipoId = $tipoExistente;
        }
    }

    // 3. Insertar evento
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'] ?? '';
    $modalidad = $_POST['modalidad'];
    $costo = ($modalidad === 'Gratis') ? 0.00 : $_POST['costo'];
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];

    $stmt = $conn->prepare("INSERT INTO eventos_cursos (tit_eve_cur, des_eve_cur, fec_ini_eve_cur, fec_fin_eve_cur, cos_eve_cur, mod_eve_cur)
                            VALUES (?, ?, ?, ?, ?, ?) RETURNING id_eve_cur");
    $stmt->execute([$titulo, $descripcion, $fechaInicio, $fechaFin, $costo, $modalidad]);
    $eventoId = $stmt->fetchColumn();

    // 4. Tipos de evento (radio o nuevo)
    $tiposEvento = $_POST['tipoEvento'] ?? [];

    // Si viene como string (radio), convertirlo en array
    if (!is_array($tiposEvento)) {
        $tiposEvento = [$tiposEvento];
    }

    if ($nuevoTipoId && !in_array($nuevoTipoId, $tiposEvento)) {
        $tiposEvento[] = $nuevoTipoId;
    }

    foreach ($tiposEvento as $tipoId) {
        if ($tipoId === 'otros') continue;
        $stmt = $conn->prepare("INSERT INTO eventos_tipos (id_eve_cur, id_tipo_eve) VALUES (?, ?)");
        $stmt->execute([$eventoId, $tipoId]);
    }

    // 5. Carreras participantes
    $carreras = $_POST['carreras'] ?? [];
    if (!is_array($carreras)) {
        $carreras = [$carreras];
    }

    foreach ($carreras as $idCar) {
        $stmt = $conn->prepare("INSERT INTO eventos_carreras (id_eve_cur, id_car) VALUES (?, ?)");
        $stmt->execute([$eventoId, $idCar]);
    }

    // 6. Requisitos + valores (nota y asistencia si aplica)
    $requisitos = $_POST['requisitos'] ?? [];
    if (!is_array($requisitos)) {
        $requisitos = [$requisitos];
    }

    $notaMinima = $_POST['notaMinima'] ?? null;
    $asistenciaMinima = $_POST['asistenciaMinima'] ?? null;

    foreach ($requisitos as $idReq) {
        $valorReq = null;

        // Obtener el nombre del requisito
        $stmt = $conn->prepare("SELECT LOWER(nom_req) FROM requisitos WHERE id_req = ?");
        $stmt->execute([$idReq]);
        $nombre = $stmt->fetchColumn();

        if (strpos($nombre, 'nota') !== false && $notaMinima !== null) {
            $valorReq = $notaMinima;
        } elseif (strpos($nombre, 'asistencia') !== false && $asistenciaMinima !== null) {
            $valorReq = $asistenciaMinima;
        }

        $stmt = $conn->prepare("INSERT INTO eventos_requisitos (id_eve_cur, id_req, valor_req) VALUES (?, ?, ?)");
        $stmt->execute([$eventoId, $idReq, $valorReq]);
    }

    $response['success'] = true;
    $response['message'] = 'Evento guardado correctamente.';
    if ($nuevoTipoId) {
        $response['nuevoTipoId'] = $nuevoTipoId;
    }
} catch (Exception $e) {
    http_response_code(500);
    $response['message'] = 'Error al guardar el evento: ' . $e->getMessage();
}

echo json_encode($response);
