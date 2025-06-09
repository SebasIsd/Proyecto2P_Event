<?php
require_once("../conexion/conexionusu.php");

$conexion = ConexionUsu::obtenerConexion();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger todos los datos del formulario
    $cedula       = $_POST["ced_usu"] ?? '';
    $nom_pri_usu  = $_POST["nom_pri_usu"] ?? '';
    $nom_seg_usu  = $_POST["nom_seg_usu"] ?? '';
    $ape_pri_usu  = $_POST["ape_pri_usu"] ?? '';
    $ape_seg_usu  = $_POST["ape_seg_usu"] ?? '';
    $cor_usu      = $_POST["cor_usu"] ?? '';
    $pas_usu      = $_POST["pas_usu"] ?? '';
    $tel_usu      = $_POST["tel_usu"] ?? '';
    $dir_usu      = $_POST["dir_usu"] ?? '';
    $id_rol_usu   = $_POST["id_rol_usu"] ?? '';
    $carrera      = $_POST["carrera"] ?? '';
    $fec_nac_usu  = $_POST["fec_nac_usu"] ?? '';

    // Validación básica de campos obligatorios según tu esquema
    if (empty($cedula) || empty($nom_pri_usu) || empty($ape_pri_usu) || empty($cor_usu) || 
        empty($tel_usu) || empty($dir_usu) || empty($fec_nac_usu) || empty($id_rol_usu)) {
        echo json_encode(['error' => 'Faltan campos obligatorios']);
        exit;
    }

    try {
        // Construir la consulta SQL dinámicamente
        $sql = "UPDATE usuarios SET 
                nom_pri_usu = $1,
                nom_seg_usu = $2,
                ape_pri_usu = $3,
                ape_seg_usu = $4,
                cor_usu = $5,
                tel_usu = $6,
                dir_usu = $7,
                id_rol_usu = $8,
                car_usu = $9,
                fec_nac_usu = $10";
        
        $params = [
            $nom_pri_usu,
            $nom_seg_usu,
            $ape_pri_usu,
            $ape_seg_usu,
            $cor_usu,
            $tel_usu,
            $dir_usu,
            $id_rol_usu,
            $carrera,
            $fec_nac_usu
        ];

        // Si se proporcionó una nueva contraseña, actualizarla (sin hashear)
        if (!empty($pas_usu)) {
            $sql .= ", pas_usu = $11";
            $params[] = $pas_usu;
        }

        // Finalizar la consulta con la condición WHERE
        $sql .= " WHERE ced_usu = $".(count($params) + 1);
        $params[] = $cedula;

        // Ejecutar la consulta
        $resultado = pg_query_params($conexion, $sql, $params);

        if ($resultado) {
            echo json_encode(['success' => true, 'mensaje' => 'Usuario actualizado correctamente']);
        } else {
            echo json_encode(['error' => 'Error al actualizar usuario: ' . pg_last_error($conexion)]);
        }
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
    }
}
?>