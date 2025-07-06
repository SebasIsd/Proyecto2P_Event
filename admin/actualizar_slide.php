<?php
require_once '../includes/conexion1.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $enlace = $_POST['imagen_url']; // URL del enlace
    $link_url = $_POST['link_url']; // Puede ser sobrescrita

    // Subida de imagen si se envió un archivo
    if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['imagen_archivo']['tmp_name'];
        $nombre = basename($_FILES['imagen_archivo']['name']);
        $nombre = time() . "_" . preg_replace("/[^a-zA-Z0-9\._]/", "", $nombre); // Limpieza
        $ruta = "../uploads/" . $nombre;

        if (!is_dir("../uploads")) {
            mkdir("../uploads", 0777, true);
        }

        if (move_uploaded_file($tmp_name, $ruta)) {
            $link_url = "uploads/" . $nombre; // URL accesible relativa
        }
    }

    $db = new Conexion();
    $conn = $db->getConexion();

    $sql = "UPDATE carrusel SET titulo = $1, descripcion = $2, link_url = $3, imagen_url = $4 WHERE id = $5";
    $params = [$titulo, $descripcion, $enlace, $link_url, $id];

    $res = pg_query_params($conn, $sql, $params);

    if (!$res) {
        echo "Error al actualizar.";
        exit;
    }
}
?>
