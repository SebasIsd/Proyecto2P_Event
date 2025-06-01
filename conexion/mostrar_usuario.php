<?php
require_once 'conexionusu.php';

$conexion = ConexionUsu::obtenerConexion();

if (!$conexion) {
    die("Error al conectar a la base de datos.");
}

$consulta = "SELECT u.ced_usu, u.nom_pri_usu, u.nom_seg_usu,u.ape_pri_usu,u.ape_seg_usu, u.cor_usu, u.tel_usu, c.nom_rol AS cargo 
             FROM usuarios u
             LEFT JOIN roles c ON u.id_rol_usu = c.id_rol";
$resultado = pg_query($conexion, $consulta);

$usuarios = [];
while ($fila = pg_fetch_assoc($resultado)) {
    $usuarios[] = $fila;
}

header('Content-Type: application/json');
echo json_encode($usuarios);
?>
