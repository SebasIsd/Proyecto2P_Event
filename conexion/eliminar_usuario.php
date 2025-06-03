<?php
include("conexionusu.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST["cedula"];
    $sql = "DELETE FROM usuarios WHERE cedula = '$cedula'";

    if (pg_query($conexion, $sql)) {
        echo "Usuario eliminado correctamente.";
    } else {
        echo "Error al eliminar usuario: " . pg_last_error($conexion);
    }
}
?>
