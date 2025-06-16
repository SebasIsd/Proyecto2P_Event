<?php
session_start();
require_once('../includes/conexion1.php');

$conexion = new Conexion();
$conn = $conexion->getConexion();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    
    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !str_ends_with($correo, '@uta.edu.ec')) {
        $error = "Solo correos @uta.edu.ec válidos";
    } else {
        // Buscar al usuario
        $sql = "SELECT ced_usu FROM usuarios WHERE cor_usu = $1";
        $result = pg_query_params($conn, $sql, [$correo]);
        
        if (pg_num_rows($result) > 0) {
            $usuario = pg_fetch_assoc($result);
           


            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !str_ends_with($correo, '@uta.edu.ec')) {
        $error = "Solo correos @uta.edu.ec válidos";
    } else {
        $sql = "SELECT ced_usu FROM usuarios WHERE cor_usu = $1";
        $result = pg_query_params($conn, $sql, [$correo]);

        if (pg_num_rows($result) > 0) {
            $usuario = pg_fetch_assoc($result);
            $cedula = $usuario['ced_usu'];

            // 1. Generar token
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", time() + 900); // 15 minutos desde ahora

            // 2. Guardar token y expiración
            $sql_token = "UPDATE usuarios SET token_recuperacion = $1, token_expira = $2 WHERE ced_usu = $3";
            pg_query_params($conn, $sql_token, [$token, $expira, $cedula]);

            // 3. Enviar correo con el enlace
            $enlace = "http://localhost/Proyecto2P_Event/views/reset.php?token=$token";

            // Envío usando mail() (reemplázalo con PHPMailer si puedes)
            $asunto = "Recuperación de contraseña - UTA";
            $mensaje = "Hola, para restablecer tu contraseña haz clic en el siguiente enlace:\n\n$enlace\n\nEste enlace expirará en 15 minutos.";
            $cabeceras = "From: noreply@uta.edu.ec";

            if (mail($correo, $asunto, $mensaje, $cabeceras)) {
                $success = "Enlace de recuperación enviado a tu correo.";
            } else {
                $error = "Error al enviar el correo. Intenta más tarde.";
            }
        } else {
            $error = "Correo no registrado en el sistema";
        }
    }
}





            
            exit();
        } else {
            $error = "Correo no registrado en el sistema";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - UTA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
   
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/css/recuperar.css">

</head>
<body>
    <div class="recovery-container">
        <div class="recovery-card">
            <img src="../imagenes/evento2.png" alt="Logo UTA" class="logo">
            <h2>Recuperar Contraseña</h2>
            <p class="subtitle">Ingresa tu correo institucional para restablecer tu contraseña</p>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="correo"><i class="fas fa-envelope"></i> Correo institucional</label>
                    <input type="email" id="correo" name="correo" placeholder="usuario@uta.edu.ec" required>
                </div>
                
                <button type="submit" class="btn">
                    <i class="fas fa-key"></i> Continuar
                </button>
            </form>
            
            <a href="login.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>
</body>
</html>