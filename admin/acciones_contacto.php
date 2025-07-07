<?php
session_start();
require_once '../includes/conexion1.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ./usuarios/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    $db = new Conexion();
    $conn = $db->getConexion();

    $sql = "UPDATE contacto SET direccion = $1, telefono = $2, correo = $3 WHERE id = $4";
    $params = [$direccion, $telefono, $correo, $id];

    $res = pg_query_params($conn, $sql, $params);

    if (!$res) {
        echo "Error al actualizar contacto.";
        exit;
    } else {
        header("Location: administrarInicio.php");
        exit;
    }
}
?>
