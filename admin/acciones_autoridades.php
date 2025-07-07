<?php
session_start();
require_once '../includes/conexion1.php';

$db = new Conexion();
$conn = $db->getConexion();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update': // Contacto generalmente solo se actualiza, no se añade o elimina múltiples
        $id = $_POST['id'] ?? '';
        $direccion = $_POST['direccion'] ?? '';
        $telefono = $_POST['telefono'] ?? '';
        $correo = $_POST['correo'] ?? '';

        $query = "UPDATE contacto SET direccion = $1, telefono = $2, correo = $3 WHERE id = $4";
        $result = pg_query_params($conn, $query, array($direccion, $telefono, $correo, $id));

        if (!$result) {
            error_log("Error al actualizar contacto: " . pg_last_error($conn));
            echo "Error al actualizar la información de contacto.";
        }
        break;

    // Puedes añadir un caso 'add' si necesitas inicializar el contacto si no existe
    // case 'add':
    //     $direccion = $_POST['direccion'] ?? '';
    //     $telefono = $_POST['telefono'] ?? '';
    //     $correo = $_POST['correo'] ?? '';
    //     $query = "INSERT INTO contacto (direccion, telefono, correo) VALUES ($1, $2, $3)";
    //     $result = pg_query_params($conn, $query, array($direccion, $telefono, $correo));
    //     if (!$result) {
    //         error_log("Error al agregar contacto: " . pg_last_error($conn));
    //         echo "Error al agregar la información de contacto.";
    //     }
    //     break;

    default:
        // No action specified, do nothing or redirect
        break;
}

$db->cerrar();
header("Location: administrarInicio.php#contacto");
exit;
?>
