<?php
require_once("conexionusu.php");

$conexion = ConexionUsu::obtenerConexion(); // conexión segura

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST["cedula"] ?? '';

    if (empty($cedula)) {
        echo "Cédula no recibida.";
        exit;
    }

    // Consulta segura usando parámetros
    $sql = "DELETE FROM usuarios WHERE ced_usu = $1";
    $resultado = pg_query_params($conexion, $sql, [$cedula]);

    if ($resultado) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar usuario: " . pg_last_error($conexion);
    }
}
?>
