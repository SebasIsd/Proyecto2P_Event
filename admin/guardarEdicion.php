<?php
require_once '../conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: editarEvento.php');
    exit;
}

$conn = CConexion::ConexionBD();

$idEvento = $_POST['id_eve_cur'] ?? null;
$titulo = trim($_POST['titulo'] ?? '');
$descripcion = trim($_POST['descripcion'] ?? '');
$modalidad = $_POST['modalidad'] ?? '';
$costo = $_POST['costo'] ?? 0;
$fechaInicio = $_POST['fecha_inicio'] ?? '';
$fechaFin = $_POST['fecha_fin'] ?? '';
$idTipoEvento = $_POST['id_tipo_evento'] ?? null;
$carreras = $_POST['carreras'] ?? [];
$requisitos = $_POST['requisitos'] ?? [];
$valoresReq = $_POST['valores'] ?? [];

if (!$idEvento || !$titulo || !$modalidad || !$fechaInicio || !$fechaFin || !$idTipoEvento) {
    die('Faltan datos obligatorios.');
}

try {
    $conn->beginTransaction();

    // Actualizar evento principal
    $sqlActualizar = "UPDATE EVENTOS_CURSOS SET
        TIT_EVE_CUR = ?,
        DES_EVE_CUR = ?,
        MOD_EVE_CUR = ?,
        COS_EVE_CUR = ?,
        FEC_INI_EVE_CUR = ?,
        FEC_FIN_EVE_CUR = ?,
        ID_TIPO_EVE = ?
        WHERE ID_EVE_CUR = ?";

    $stmt = $conn->prepare($sqlActualizar);
    $stmt->execute([
        $titulo,
        $descripcion,
        $modalidad,
        $costo,
        $fechaInicio,
        $fechaFin,
        $idTipoEvento,
        $idEvento
    ]);

    // Reemplazar carreras relacionadas
    $conn->prepare("DELETE FROM EVENTOS_CARRERAS WHERE ID_EVE_CUR = ?")->execute([$idEvento]);
    if (is_array($carreras) && count($carreras) > 0) {
        $sqlInsertCarreras = "INSERT INTO EVENTOS_CARRERAS (ID_EVE_CUR, ID_CAR) VALUES (?, ?)";
        $stmtInsertCar = $conn->prepare($sqlInsertCarreras);
        foreach ($carreras as $idCar) {
            $stmtInsertCar->execute([$idEvento, $idCar]);
        }
    }

    // Reemplazar requisitos relacionados
    $conn->prepare("DELETE FROM EVENTOS_REQUISITOS WHERE ID_EVE_CUR = ?")->execute([$idEvento]);
    if (is_array($requisitos) && count($requisitos) > 0) {
        $sqlInsertReq = "INSERT INTO EVENTOS_REQUISITOS (ID_EVE_CUR, ID_REQ, VALOR_REQ) VALUES (?, ?, ?)";
        $stmtInsertReq = $conn->prepare($sqlInsertReq);
        foreach ($requisitos as $idReq => $_) {
            // Nota: el checkbox solo manda valor si está marcado, por eso $_ está aquí
            $valor = $valoresReq[$idReq] ?? '';
            $stmtInsertReq->execute([$idEvento, $idReq, $valor]);
        }
    }

    $conn->commit();

    header("Location: editarEvento.php?id=" . urlencode($idEvento) . "&success=1");
    exit;

} catch (PDOException $e) {
    $conn->rollBack();
    die("Error al guardar: " . $e->getMessage());
}
