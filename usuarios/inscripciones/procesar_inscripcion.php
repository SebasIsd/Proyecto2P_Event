<?php
session_start();

// Verifica sesión y parámetros
if (empty($_SESSION['usuario']) || empty($_POST['id_evento']) || empty($_POST['cedula'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

$id_evento = (int)$_POST['id_evento'];
$cedula = $_POST['cedula'];
$fecha_actual = date('Y-m-d');
$estado_pago = 'pendiente'; // puedes cambiarlo a 'pagado' si es necesario

// Verifica si ya está inscrito
$sql_verificar = "SELECT COUNT(*) FROM inscripciones WHERE ced_usu = $1 AND id_eve_cur = $2";
$result_verificar = pg_query_params($conn, $sql_verificar, [$cedula, $id_evento]);
$ya_inscrito = pg_fetch_result($result_verificar, 0, 0);

if ($ya_inscrito > 0) {
    echo "<script>alert('Ya estás inscrito en este evento.'); window.location.href = 'index.php';</script>";
    exit();
}

// Insertar inscripción
$sql_insertar = "INSERT INTO inscripciones (ced_usu, id_eve_cur, fec_ini_ins, fec_cie_ins, est_pag_ins)
                 VALUES ($1, $2, $3, NULL, $4)";
$result_insertar = pg_query_params($conn, $sql_insertar, [$cedula, $id_evento, $fecha_actual, $estado_pago]);

if ($result_insertar) {
    echo "<script>alert('¡Inscripción realizada con éxito!'); window.location.href = 'index.php';</script>";
} else {
    echo "<script>alert('Ocurrió un error al registrar tu inscripción.'); window.history.back();</script>";
}
?>
