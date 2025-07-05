<?php
session_start();
require_once '../includes/conexion1.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ./usuarios/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $cargo = $_POST['cargo'];
    $dependencia = $_POST['dependencia'];
    $imagen_url = $_POST['imagen_url'];

    // Subida de imagen si se envió un archivo
    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen_archivo']['tmp_name'];
        $nombreArchivo = basename($_FILES['imagen_archivo']['name']);
        $nombreArchivo = time() . "_" . preg_replace("/[^a-zA-Z0-9\._]/", "", $nombreArchivo);
        $ruta = "../uploads/autoridades/" . $nombreArchivo;

        if (!is_dir("../uploads/autoridades")) {
            mkdir("../uploads/autoridades", 0777, true);
        }

        if (move_uploaded_file($tmp_name, $ruta)) {
            $imagen_url = "uploads/autoridades/" . $nombreArchivo;
        }
    }

    $db = new Conexion();
    $conn = $db->getConexion();

    $sql = "UPDATE autoridades SET nombre = $1, cargo = $2, dependencia = $3, imagen_url = $4 WHERE id = $5";
    $params = [$nombre, $cargo, $dependencia, $imagen_url, $id];

    $res = pg_query_params($conn, $sql, $params);

    if (!$res) {
        echo "Error al actualizar autoridad.";
        exit;
    } else {
        header("Location: administrarInicio.php");
        exit;
    }
}
?>
