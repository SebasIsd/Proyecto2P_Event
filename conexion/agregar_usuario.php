<?php
include("conexionusu.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST["cedula"];
    $nombres = $_POST["nombres"];
    $apellidos = $_POST["apellidos"];
    $correo = $_POST["correo"];
    $id_cargo = $_POST["id_cargo"];

    $sql = "INSERT INTO usuarios (cedula, nombres, apellidos, correo, id_cargo)
            VALUES ('$cedula', '$nombres', '$apellidos', '$correo', $id_cargo)";

    if (pg_query($conexion, $sql)) {
        echo "Usuario agregado correctamente.";
    } else {
        echo "Error al agregar usuario: " . pg_last_error($conexion);
    }
}
?>

