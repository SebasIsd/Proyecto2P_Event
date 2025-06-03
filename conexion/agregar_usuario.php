<?php
require_once("conexionusu.php");

$conexion = ConexionUsu::obtenerConexion();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $cedula       = $_POST["cedula"] ?? '';
    $nom_pri_usu  = $_POST["nom_pri_usu"] ?? '';
    $nom_seg_usu  = $_POST["nom_seg_usu"] ?? '';
    $ape_pri_usu  = $_POST["ape_pri_usu"] ?? '';
    $ape_seg_usu  = $_POST["ape_seg_usu"] ?? '';
    $correo       = $_POST["correo"] ?? '';
    $password     = $_POST["password"] ?? '';
    $telefono     = $_POST["telefono"] ?? '';
    $direccion    = $_POST["direccion"] ?? '';
    $id_rol_usu   = $_POST["id_rol_usu"] ?? '';
    $carrera      = $_POST["carrera"] ?? '';
    $fec_nac_usu  = $_POST["fec_nac_usu"] ?? ''; // ✅ Nuevo campo

    // Validación básica
    if (empty($cedula) || empty($nom_pri_usu) || empty($ape_pri_usu) || empty($correo) || empty($password) || empty($id_rol_usu) || empty($fec_nac_usu)) {
        echo "Faltan campos obligatorios.";
        exit;
    }

    // Hashear la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Consulta preparada con el nuevo campo fec_nac_usu
    $sql = "INSERT INTO usuarios (
                ced_usu, nom_pri_usu, nom_seg_usu,
                ape_pri_usu, ape_seg_usu, cor_usu,
                pas_usu, tel_usu, dir_usu,
                id_rol_usu, car_usu, fec_nac_usu
            ) VALUES (
                $1, $2, $3,
                $4, $5, $6,
                $7, $8, $9,
                $10, $11, $12
            )";

    $params = [
        $cedula,
        $nom_pri_usu,
        $nom_seg_usu,
        $ape_pri_usu,
        $ape_seg_usu,
        $correo,
        $password_hash,
        $telefono,
        $direccion,
        $id_rol_usu,
        $carrera,
        $fec_nac_usu
    ];

    $resultado = pg_query_params($conexion, $sql, $params);

    if ($resultado) {
        echo "Usuario agregado correctamente.";
    } else {
        echo "Error al agregar usuario: " . pg_last_error($conexion);
    }
}
?>
