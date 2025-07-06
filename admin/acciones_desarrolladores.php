<?php
session_start();
require_once '../includes/conexion1.php';

$db = new Conexion();
$conn = $db->getConexion();

// Directorio donde se guardarán las imágenes
$uploadDir = '../imagenes/desarrolladores/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $nombre = $_POST['nombre'] ?? '';
        $cargo = $_POST['cargo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $habilidades = $_POST['habilidades'] ?? '';
        $imagen_url = $_POST['imagen_url'] ?? '';
        $github_url = $_POST['github_url'] ?? '';
        $linkedin_url = $_POST['linkedin_url'] ?? '';
        $email = $_POST['email'] ?? '';

        // Manejo de la subida de imagen
        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen_archivo']['tmp_name'];
            $fileName = $_FILES['imagen_archivo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('dev_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen_url = '../imagenes/desarrolladores/' . $newFileName;
            } else {
                // Manejar error de subida
                echo "Error al subir la imagen.";
                exit;
            }
        }

        $query = "INSERT INTO desarrolladores (nombre, cargo, descripcion, habilidades, imagen_url, github_url, linkedin_url, email) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        $result = pg_query_params($conn, $query, array($nombre, $cargo, $descripcion, $habilidades, $imagen_url, $github_url, $linkedin_url, $email));

        if ($result) {
            header('Location: administrarInicio.php#desarrolladores');
            exit;
        } else {
            echo "Error al agregar desarrollador: " . pg_last_error($conn);
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $cargo = $_POST['cargo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $habilidades = $_POST['habilidades'] ?? '';
        $imagen_url = $_POST['imagen_url'] ?? ''; // URL existente o vacía si se sube una nueva
        $github_url = $_POST['github_url'] ?? '';
        $linkedin_url = $_POST['linkedin_url'] ?? '';
        $email = $_POST['email'] ?? '';

        // Obtener la URL de la imagen actual para posible eliminación
        $currentImageQuery = pg_query_params($conn, "SELECT imagen_url FROM desarrolladores WHERE id = $1", array($id));
        $currentImageData = pg_fetch_assoc($currentImageQuery);
        $oldImageUrl = $currentImageData['imagen_url'] ?? '';

        // Manejo de la subida de imagen
        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen_archivo']['tmp_name'];
            $fileName = $_FILES['imagen_archivo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('dev_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen_url = '../imagenes/desarrolladores/' . $newFileName;
                // Eliminar la imagen antigua si existe y es diferente a la nueva
                if (!empty($oldImageUrl) && file_exists($oldImageUrl) && $oldImageUrl !== $imagen_url) {
                    unlink($oldImageUrl);
                }
            } else {
                echo "Error al subir la nueva imagen.";
                exit;
            }
        } else if (empty($imagen_url) && !empty($oldImageUrl)) {
            // Si la URL se vació y no se subió un archivo, eliminar la imagen antigua
            if (file_exists($oldImageUrl)) {
                unlink($oldImageUrl);
            }
        }


        $query = "UPDATE desarrolladores SET nombre = $1, cargo = $2, descripcion = $3, habilidades = $4, imagen_url = $5, github_url = $6, linkedin_url = $7, email = $8 WHERE id = $9";
        $result = pg_query_params($conn, $query, array($nombre, $cargo, $descripcion, $habilidades, $imagen_url, $github_url, $linkedin_url, $email, $id));

        if ($result) {
            header('Location: administrarInicio.php#desarrolladores');
            exit;
        } else {
            echo "Error al actualizar desarrollador: " . pg_last_error($conn);
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';

        // Obtener la URL de la imagen para eliminar el archivo
        $currentImageQuery = pg_query_params($conn, "SELECT imagen_url FROM desarrolladores WHERE id = $1", array($id));
        $currentImageData = pg_fetch_assoc($currentImageQuery);
        $imageUrlToDelete = $currentImageData['imagen_url'] ?? '';

        $query = "DELETE FROM desarrolladores WHERE id = $1";
        $result = pg_query_params($conn, $query, array($id));

        if ($result) {
            // Eliminar el archivo de imagen si existe
            if (!empty($imageUrlToDelete) && file_exists($imageUrlToDelete)) {
                unlink($imageUrlToDelete);
            }
            header('Location: administrarInicio.php#desarrolladores');
            exit;
        } else {
            echo "Error al eliminar desarrollador: " . pg_last_error($conn);
        }
        break;

    default:
        header('Location: administrarInicio.php');
        exit;
}

$db->cerrar();
?>
