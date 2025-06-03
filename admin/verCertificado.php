<?php
include_once '../conexion/conexion.php';
header('Content-Type: text/html; charset=utf-8');

$idIns = $_GET['id_ins'] ?? null;

if (!$idIns) {
    echo "ID inválido";
    exit;
}

try {
    $conn = CConexion::ConexionBD();

    $stmt = $conn->prepare("SELECT HTML_GENERADO FROM CERTIFICADOS WHERE ID_INS = :id");
    $stmt->execute([':id' => $idIns]);
    $cert = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$cert || !file_exists($cert['html_generado'])) {
        echo "Certificado no encontrado.";
        exit;
    }

    header('Content-type: application/pdf');
    readfile($cert['html_generado']);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
