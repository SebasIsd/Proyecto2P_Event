<?php
include '../conexion.php';

if (!isset($_POST['nombre']) || trim($_POST['nombre']) === '') {
    echo json_encode(['success' => false, 'message' => 'Nombre vacío']);
    exit;
}

$nombre = trim($_POST['nombre']);
$stmt = $conn->prepare("INSERT INTO TIPOS_EVENTO (NOM_TIPO_EVE) VALUES (?)");

try {
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    echo json_encode(['success' => true]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['success' => false, 'message' => 'Tipo ya existe o error en DB']);
}
?>
