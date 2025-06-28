<?php
session_start();
require_once('../includes/conexion1.php');

// PHPMailer
require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$conexion = new Conexion();
$conn = $conexion->getConexion();
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);

    // Validar formato email sin restricción de dominio
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico no válido";
    } else {
        // Buscar usuario por correo
        $sql = "SELECT ced_usu FROM usuarios WHERE cor_usu = $1";
        $result = pg_query_params($conn, $sql, [$correo]);

        if (pg_num_rows($result) > 0) {
            $usuario = pg_fetch_assoc($result);
            $cedula = $usuario['ced_usu'];

            // Generar token seguro y expiración
            $token = bin2hex(random_bytes(32));
            $expira = date("Y-m-d H:i:s", time() + 900); // 15 minutos

            // Guardar token y expiración en BD
            $sql_token = "UPDATE usuarios SET token_recuperacion = $1, token_expira = $2 WHERE ced_usu = $3";
            pg_query_params($conn, $sql_token, [$token, $expira, $cedula]);

            // Enlace de recuperación (ajusta URL a producción)
            $enlace = "http://localhost/Proyecto2P_Event/usuarios/reset.php?token=$token";

            $asunto = "Recuperación de contraseña - UTA";
            $mensaje = "Hola,\n\nPara restablecer tu contraseña haz clic en el siguiente enlace:\n$enlace\n\nEste enlace expirará en 15 minutos.";

            // Configurar PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'anthonysemblantes619@gmail.com'; 
                $mail->Password = 'ynytinytvxevgolm'; 
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
                $mail->CharSet = 'UTF-8';

                $mail->setFrom('noreply@uta.edu.ec', 'Sistema UTA');
                $mail->addAddress($correo);

                $mail->isHTML(false);
                $mail->Subject = $asunto;
                $mail->Body = $mensaje;

                $mail->send();
                $success = "Se ha enviado un enlace de recuperación a tu correo electrónico.";
            } catch (Exception $e) {
                $error = "No se pudo enviar el correo. Por favor, intenta nuevamente más tarde.";
            }
        } else {
            $error = "Este correo no está registrado en nuestro sistema.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Recuperar Contraseña - UTA</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="../styles/css/recuperar.css" />
</head>
<body>
    <div class="recovery-container">
        <div class="recovery-card">
            <img src="../imagenes/evento2.png" alt="Logo UTA" class="logo" />
            <h2>Recuperar Contraseña</h2>
            <p class="subtitle">Ingresa tu correo electrónico para restablecer tu contraseña</p>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
                <form method="POST" novalidate>
                    <div class="form-group">
                        <label for="correo"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input type="email" id="correo" name="correo" placeholder="ejemplo@correo.com" required />
                    </div>
                    <button type="submit" class="btn">
                        <i class="fas fa-key"></i> Enviar enlace de recuperación
                    </button>
                </form>
            <?php endif; ?>

            <a href="login.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Volver al inicio de sesión
            </a>
        </div>
    </div>
</body>
</html>
