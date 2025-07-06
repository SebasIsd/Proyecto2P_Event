<?php
session_start();
require_once '../includes/conexion1.php';

$db = new Conexion();
$conn = $db->getConexion();

$uploadDir = '../uploads/'; // Directorio para las imágenes de "Sobre Nosotros"

// Asegurarse de que el directorio de subida exista
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update': // "Sobre Nosotros" generalmente solo se actualiza
        $id = $_POST['id'] ?? '';
        $titulo = $_POST['titulo'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $imagen_url_final = $_POST['imagen_url'] ?? '';

        // Obtener la URL de la imagen actual para posible eliminación
        $currentImageQuery = pg_query_params($conn, "SELECT imagen_url FROM sobre_nosotros WHERE id = $1", array($id));
        $currentImageData = pg_fetch_assoc($currentImageQuery);
        $oldImageUrl = $currentImageData['imagen_url'] ?? '';

        if (isset($_FILES['imagen_archivo']) && $_FILES['imagen_archivo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen_archivo']['tmp_name'];
            $fileName = $_FILES['imagen_archivo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $newFileName = uniqid('sobre_') . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $imagen_url_final = "uploads/" . $newFileName;
                // Eliminar la imagen antigua si existe y es diferente a la nueva
                if (!empty($oldImageUrl) && file_exists('../' . $oldImageUrl) && ('../' . $oldImageUrl) !== $destPath) {
                    unlink('../' . $oldImageUrl);
                }
            } else {
                error_log("Error al subir la nueva imagen para 'Sobre Nosotros'.");
            }
        } else if (empty($imagen_url_final) && !empty($oldImageUrl)) {
            // Si la URL se vació y no se subió un archivo, eliminar la imagen antigua
            if (file_exists('../' . $oldImageUrl)) {
                unlink('../' . $oldImageUrl);
            }
        }

        $query = "UPDATE sobre_nosotros SET titulo = $1, descripcion = $2, imagen_url = $3 WHERE id = $4";
        $result = pg_query_params($conn, $query, array($titulo, $descripcion, $imagen_url_final, $id));

        if (!$result) {
            error_log("Error al actualizar 'Sobre Nosotros': " . pg_last_error($conn));
            echo "Error al actualizar la sección 'Sobre Nosotros'.";
        }
        break;

    // Puedes añadir un caso 'add' si necesitas inicializar la sección si no existe
    // case 'add':
    //     $titulo = $_POST['titulo'] ?? '';
    //     $descripcion = $_POST['descripcion'] ?? '';
    //     $imagen_url_final = $_POST['imagen_url'] ?? '';
    //     // ... lógica de subida de imagen ...
    //     $query = "INSERT INTO sobre_nosotros (titulo, descripcion, imagen_url) VALUES ($1, $2, $3)";
    //     $result = pg_query_params($conn, $query, array($titulo, $descripcion, $imagen_url_final));
    //     if (!$result) {
    //         error_log("Error al agregar 'Sobre Nosotros': " . pg_last_error($conn));
    //         echo "Error al agregar la sección 'Sobre Nosotros'.";
    //     }
    //     break;

    default:
        // No action specified, do nothing or redirect
        break;
}

$db->cerrar();
header("Location: administrarInicio.php#dataSobre");
exit;
?>
