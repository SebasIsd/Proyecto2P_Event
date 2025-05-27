<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'];

    if ($accion === 'buscar') {
        $cedula = $_POST['ced_usu'];
        $sql = "SELECT * FROM usuarios WHERE ced_usu = $1";
        $stmt = pg_prepare($conexion, "buscar_usuario", $sql);
        $resultado = pg_execute($conexion, "buscar_usuario", [$cedula]);
        $usuario = pg_fetch_assoc($resultado);
        echo json_encode($usuario);
    }

    if ($accion === 'actualizar') {
        $sql = "UPDATE usuarios SET 
                    nom_pri_usu = $1,
                    nom_seg_usu = $2,
                    ape_pri_usu = $3,
                    ape_seg_usu = $4,
                    cor_usu = $5,
                    pas_usu = $6,
                    tel_usu = $7,
                    dir_usu = $8,
                    fec_nac_usu = $9
                WHERE ced_usu = $10";
        $stmt = pg_prepare($conexion, "actualizar_usuario", $sql);
        $resultado = pg_execute($conexion, "actualizar_usuario", [
            $_POST['nom_pri_usu'],
            $_POST['nom_seg_usu'],
            $_POST['ape_pri_usu'],
            $_POST['ape_seg_usu'],
            $_POST['cor_usu'],
            $_POST['pas_usu'],
            $_POST['tel_usu'],
            $_POST['dir_usu'],
            $_POST['fec_nac_usu'],
            $_POST['ced_usu']
        ]);

        echo json_encode(['mensaje' => 'Datos actualizados correctamente']);
    }
}
?>
