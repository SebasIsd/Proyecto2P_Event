<?php
session_start();
require_once '../includes/conexion1.php';

$db = new Conexion();
$conn = $db->getConexion();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $nombre = $_POST['nombre'] ?? '';
        $cargo = $_POST['cargo'] ?? '';
        $dependencia = $_POST['dependencia'] ?? '';
        $imagen_url = $_POST['imagen_url'] ?? '';
        
        $query = "INSERT INTO autoridades (nombre, cargo, dependencia, imagen_url) 
                  VALUES ($1, $2, $3, $4)";
        $result = pg_query_params($conn, $query, array($nombre, $cargo, $dependencia, $imagen_url));
        
        if (!$result) {
            error_log("Error al agregar autoridad: " . pg_last_error($conn));
            $_SESSION['error'] = "Error al agregar la autoridad.";
        } else {
            $_SESSION['success'] = "Autoridad agregada correctamente.";
        }
        break;

    case 'update':
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $cargo = $_POST['cargo'] ?? '';
        $dependencia = $_POST['dependencia'] ?? '';
        $imagen_url = $_POST['imagen_url'] ?? '';
        
        $query = "UPDATE autoridades 
                  SET nombre = $1, cargo = $2, dependencia = $3, imagen_url = $4 
                  WHERE id = $5";
        $result = pg_query_params($conn, $query, array($nombre, $cargo, $dependencia, $imagen_url, $id));
        
        if (!$result) {
            error_log("Error al actualizar autoridad: " . pg_last_error($conn));
            $_SESSION['error'] = "Error al actualizar la autoridad.";
        } else {
            $_SESSION['success'] = "Autoridad actualizada correctamente.";
        }
        break;

    case 'delete':
        $id = $_POST['id'] ?? '';
        
        $query = "DELETE FROM autoridades WHERE id = $1";
        $result = pg_query_params($conn, $query, array($id));
        
        if (!$result) {
            error_log("Error al eliminar autoridad: " . pg_last_error($conn));
            $_SESSION['error'] = "Error al eliminar la autoridad.";
        } else {
            $_SESSION['success'] = "Autoridad eliminada correctamente.";
        }
        break;

    default:
        $_SESSION['error'] = "Acción no válida.";
        break;
}

$db->cerrar();
header("Location: administrarInicio.php#autoridades");
exit;
?>