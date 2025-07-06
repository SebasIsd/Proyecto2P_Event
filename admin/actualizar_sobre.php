<?php
require_once '../includes/conexion1.php';
$db = new Conexion();
$conn = $db->getConexion();

$id = $_POST['id'];
$descripcion = $_POST['descripcion'];
$imagen_url = $_POST['imagen_url'];

if (!empty($_FILES['imagen_archivo']['name'])) {
    $archivo_nombre = basename($_FILES['imagen_archivo']['name']);
    $ruta_destino = "../uploads/" . $archivo_nombre;
    move_uploaded_file($_FILES['imagen_archivo']['tmp_name'], $ruta_destino);
    $imagen_url = $ruta_destino;
}

$query = "UPDATE sobre_nosotros SET descripcion = $1, imagen_url = $2 WHERE id = $3";
$resultado = pg_query_params($conn, $query, [$descripcion, $imagen_url, $id]);

header("Location: admin.php");
exit;
?>
