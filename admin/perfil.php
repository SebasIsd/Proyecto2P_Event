<?php
session_start();

if (!isset($_SESSION['cedula'])) {
    header("Location: /login.php");
    exit();
}

$cedula = $_SESSION['cedula'];

include '../conexion/conexion2.php'; 

$sql = "SELECT u.*, r.NOM_ROL FROM USUARIOS u
        JOIN ROLES r ON u.ID_ROL_USU = r.ID_ROL
        WHERE u.CED_USU = :cedula";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':cedula', $cedula);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Pasamos los datos a la vista
include 'perfil.php';
