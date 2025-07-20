<?php
session_start();
require_once '../includes/conexion1.php';

// Configuración
$uploadDir = '../uploads/about/';
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

try {
    $db = new Conexion();
    $conn = $db->getConexion();

    // Verificar método
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    // Crear directorio si no existe
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
        throw new Exception("No se pudo crear el directorio de uploads");
    }

    // Validar acción
    $action = $_POST['action'] ?? '';
    if (!in_array($action, ['add', 'update', 'delete'])) {
        throw new Exception("Acción no válida");
    }

    // Funciones auxiliares
    function isValidId($id) {
        return !empty($id) && is_numeric($id) && $id > 0;
    }

    function sanitizeInput($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    function isValidImageUrl($url) {
        return filter_var($url, FILTER_VALIDATE_URL) && 
               preg_match('/\.(jpg|jpeg|png|gif)$/i', $url);
    }

    function handleImageInput($uploadDir, $allowedExtensions, $maxFileSize) {
        // Caso 1: URL de imagen proporcionada
        if (!empty($_POST['imagen_url']) && isValidImageUrl($_POST['imagen_url'])) {
            return sanitizeInput($_POST['imagen_url']);
        }
        
        // Caso 2: Subida de archivo
        if (!empty($_FILES['imagen_archivo']['name'])) {
            $file = $_FILES['imagen_archivo'];
            
            if ($file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Error al subir el archivo: " . $file['error']);
            }

            if ($file['size'] > $maxFileSize) {
                throw new Exception("El archivo es demasiado grande (máx. 2MB)");
            }

            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                throw new Exception("Formato no permitido. Use JPG, PNG o GIF.");
            }

            $newFileName = 'about_' . uniqid() . '.' . $fileExtension;
            $destPath = $uploadDir . $newFileName;

            if (!move_uploaded_file($file['tmp_name'], $destPath)) {
                throw new Exception("Error al guardar el archivo");
            }

            return 'uploads/about/' . $newFileName;
        }
        
        // Caso 3: Sin imagen (dejar vacío o mantener la existente)
        return '';
    }

    // Procesar la acción
    switch ($action) {
        case 'add':
            $required = ['titulo', 'descripcion'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo $field es obligatorio");
                }
            }

            $titulo = sanitizeInput($_POST['titulo']);
            $descripcion = sanitizeInput($_POST['descripcion']);
            $imagen = handleImageInput($uploadDir, $allowedExtensions, $maxFileSize);

            $query = "INSERT INTO sobre_nosotros (titulo, descripcion, imagen_url) VALUES ($1, $2, $3)";
            $result = pg_query_params($conn, $query, [$titulo, $descripcion, $imagen]);

            if (!$result) {
                throw new Exception("Error al agregar: " . pg_last_error($conn));
            }

            $_SESSION['success'] = "Sección agregada correctamente";
            break;

        case 'update':
            if (!isset($_POST['id']) || !isValidId($_POST['id'])) {
                throw new Exception("ID inválido para actualización");
            }

            $id = $_POST['id'];
            $required = ['titulo', 'descripcion'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    throw new Exception("El campo $field es obligatorio");
                }
            }

            // Obtener datos actuales
            $currentQuery = pg_query_params($conn, "SELECT imagen_url FROM sobre_nosotros WHERE id = $1", [$id]);
            if (!$currentQuery || pg_num_rows($currentQuery) === 0) {
                throw new Exception("Registro no encontrado");
            }

            $currentData = pg_fetch_assoc($currentQuery);
            $oldImage = $currentData['imagen_url'] ?? '';
            $newImage = handleImageInput($uploadDir, $allowedExtensions, $maxFileSize);

            // Si no se proporcionó nueva imagen, mantener la existente
            if ($newImage === '' && $oldImage !== '') {
                $newImage = $oldImage;
            }

            // Eliminar imagen anterior si fue reemplazada y es un archivo local
            if ($newImage !== $oldImage && $oldImage !== '' && strpos($oldImage, 'http') !== 0) {
                if (file_exists('../' . $oldImage)) {
                    unlink('../' . $oldImage);
                }
            }

            $titulo = sanitizeInput($_POST['titulo']);
            $descripcion = sanitizeInput($_POST['descripcion']);

            $query = "UPDATE sobre_nosotros SET titulo = $1, descripcion = $2, imagen_url = $3 WHERE id = $4";
            $result = pg_query_params($conn, $query, [$titulo, $descripcion, $newImage, $id]);

            if (!$result) {
                throw new Exception("Error al actualizar: " . pg_last_error($conn));
            }

            $_SESSION['success'] = "Sección actualizada correctamente";
            break;

        case 'delete':
            if (!isset($_POST['id']) || !isValidId($_POST['id'])) {
                throw new Exception("ID inválido para eliminación");
            }

            $id = $_POST['id'];

            // Obtener datos actuales
            $currentQuery = pg_query_params($conn, "SELECT imagen_url FROM sobre_nosotros WHERE id = $1", [$id]);
            if (!$currentQuery || pg_num_rows($currentQuery) === 0) {
                throw new Exception("Registro no encontrado");
            }

            $currentData = pg_fetch_assoc($currentQuery);
            $imageUrl = $currentData['imagen_url'] ?? '';

            // Eliminar registro
            $deleteQuery = pg_query_params($conn, "DELETE FROM sobre_nosotros WHERE id = $1", [$id]);
            if (!$deleteQuery) {
                throw new Exception("Error al eliminar: " . pg_last_error($conn));
            }

            // Eliminar imagen asociada solo si es un archivo local
            if ($imageUrl && strpos($imageUrl, 'http') !== 0 && file_exists('../' . $imageUrl)) {
                unlink('../' . $imageUrl);
            }

            $_SESSION['success'] = "Sección eliminada correctamente";
            break;
    }

} catch (Exception $e) {
    error_log("Error en acciones_sobre.php: " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
} finally {
    if (isset($db)) {
        $db->cerrar();
    }
}

// Redirección
header("Location: administrarInicio.php#dataSobre");
exit;
?>