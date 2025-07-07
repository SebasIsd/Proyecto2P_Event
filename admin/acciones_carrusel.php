<?php
session_start();
require_once '../includes/conexion1.php';

$db = new Conexion();
$conn = $db->getConexion();

$uploadDir = '../uploads/'; // Directorio para las imágenes del carrusel

// Asegurarse de que el directorio de subida exista
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $link_url = $_POST['link_url'] ?? '';
        $imagen_url_final = $_POST['imagen_url'] ?? '';

        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen_archivo']['tmp_name'];
            $fileName = $_FILES['imagen_archivo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('slide_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen_url_final = "uploads/" . $newFileName;
            } else {
                error_log("Error al mover el archivo subido para carrusel: " . $destPath);
            }
        }

        $query = "INSERT INTO carrusel (titulo, descripcion, link_url, imagen_url) VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query, array($titulo, $descripcion, $link_url, $imagen_url_final));

        if (!$result) {
            error_log("Error al agregar slide: " . pg_last_error($conn));
            echo "Error al agregar el slide.";
        }
        break;

    case 'update':
        $id = $_POST['id'];
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $link_url = $_POST['link_url'] ?? '';
        $imagen_url_final = $_POST['imagen_url'] ?? '';

        // Obtener la URL de la imagen actual para posible eliminación
        $currentImageQuery = pg_query_params($conn, "SELECT imagen_url FROM carrusel WHERE id = $1", array($id));
        $currentImageData = pg_fetch_assoc($currentImageQuery);
        $oldImageUrl = $currentImageData['imagen_url'] ?? '';

        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen_archivo']['tmp_name'];
            $fileName = $_FILES['imagen_archivo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('slide_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen_url_final = "uploads/" . $newFileName;
                // Eliminar la imagen antigua si existe y es diferente a la nueva
                if (!empty($oldImageUrl) && file_exists('../' . $oldImageUrl) && ('../' . $oldImageUrl) !== $destPath) {
                    unlink('../' . $oldImageUrl);
                }
            } else {
                error_log("Error al subir la nueva imagen para carrusel.");
            }
        } else if (empty($imagen_url_final) && !empty($oldImageUrl)) {
            // Si la URL se vació y no se subió un archivo, eliminar la imagen antigua
            if (file_exists('../' . $oldImageUrl)) {
                unlink('../' . $oldImageUrl);
            }
        }

        $query = "UPDATE carrusel SET titulo = $1, descripcion = $2, link_url = $3, imagen_url = $4 WHERE id = $5";
        $result = pg_query_params($conn, $query, array($titulo, $descripcion, $link_url, $imagen_url_final, $id));

        if (!$result) {
            error_log("Error al actualizar slide: " . pg_last_error($conn));
            echo "Error al actualizar el slide.";
        }
        break;

    case 'delete':
        $id = $_POST['id'];

        // Obtener la URL de la imagen para eliminar el archivo
        $currentImageQuery = pg_query_params($conn, "SELECT imagen_url FROM carrusel WHERE id = $1", array($id));
        $currentImageData = pg_fetch_assoc($currentImageQuery);
        $imageUrlToDelete = $currentImageData['imagen_url'] ?? '';

        $query = "DELETE FROM carrusel WHERE id = $1";
        $result = pg_query_params($conn, $query, array($id));

        if ($result) {
            // Eliminar el archivo de imagen si existe
            if (!empty($imageUrlToDelete) && file_exists('../' . $imageUrlToDelete)) {
                unlink('../' . $imageUrlToDelete);
            }
        } else {
            error_log("Error al eliminar slide: " . pg_last_error($conn));
            echo "Error al eliminar el slide.";
        }
        break;

    default:
        // No action specified, do nothing or redirect
        break;
}

$db->cerrar();
header("Location: administrarInicio.php#carrusel");
exit;
?>
