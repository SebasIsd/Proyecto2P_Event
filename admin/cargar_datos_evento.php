<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../conexion/conexion.php';

$conn = CConexion::ConexionBD();

if (!$conn) {
    echo json_encode(['error' => 'No se pudo conectar a la base de datos']);
    exit;
}

$data = [
    'carreras' => [],
    'tipos_evento' => [],
    'requisitos' => []
];

// Carreras
$stmt = $conn->query("SELECT id_car AS id, nom_car AS nombre FROM carreras");
if ($stmt) {
    $data['carreras'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Tipos de evento
$stmt = $conn->query("SELECT id_tipo_eve AS id, nom_tipo_eve AS nombre FROM tipos_evento");
if ($stmt) {
    $data['tipos_evento'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Requisitos
$stmt = $conn->query("SELECT id_req AS id, nom_req AS nombre FROM requisitos");
if ($stmt) {
    $data['requisitos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

header('Content-Type: application/json');
echo json_encode($data);
