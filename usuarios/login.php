<?php
session_start();
require_once('../includes/conexion.php');

$conexion = new Conexion();
$conn = $conexion->getConexion();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    

    $sql = "SELECT * FROM usuarios WHERE cor_usu = $1";
    $result = pg_query_params($conn, $sql, array($correo));

    if ($usuario = pg_fetch_assoc($result)) {
        if ($password === $usuario['pas_usu']) {
            $_SESSION['usuario'] = $usuario['nom_pri_usu'];
            $_SESSION['rol'] = $usuario['id_rol_usu'];
            $_SESSION['correo'] = $usuario['cor_usu'];
            $_SESSION['cedula'] = $usuario['ced_usu'];
            $_SESSION['id'] = $usuario['id_usu'];

            if ($_SESSION['rol'] == 1) {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: inicio.php");
            }
            exit();
        }
    }

    $error = "Correo o contraseña incorrectos.";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Proyecto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Iniciar Sesión</h4>
                </div>
                <div class="card-body">
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Ingresar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
