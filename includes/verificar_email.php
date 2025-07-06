<?php
session_start();
require_once "conexion1.php"; // Asegúrate de que la ruta a tu archivo de conexión sea correcta

$conexion = new Conexion();
$conn = $conexion->getConexion();

$message = '';
$success = false;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = pg_escape_string($conn, $_GET['token']);

    // Buscar el usuario con el token de verificación
    $sql = "SELECT ced_usu, email_verified FROM usuarios WHERE email_verification_token = $1";
    $result = pg_query_params($conn, $sql, array($token));

    if (pg_num_rows($result) > 0) {
        $usuario = pg_fetch_assoc($result);

        if ($usuario['email_verified']) {
            $message = "Tu correo electrónico ya ha sido verificado previamente.";
            $success = true; // Ya está verificado, es un "éxito" en ese sentido
        } else {
            // Actualizar el estado de verificación
            $update_sql = "UPDATE usuarios SET email_verified = TRUE, email_verification_token = NULL WHERE email_verification_token = $1";
            $update_result = pg_query_params($conn, $update_sql, array($token));

            if ($update_result) {
                $message = "¡Tu correo electrónico ha sido verificado exitosamente! Ahora puedes iniciar sesión.";
                $success = true;
            } else {
                $message = "Error al verificar tu correo electrónico. Por favor, inténtalo de nuevo o contacta al soporte.";
            }
        }
    } else {
        $message = "Token de verificación inválido o expirado.";
    }
} else {
    $message = "Token de verificación no proporcionado.";
}

pg_close($conn);

// Muestra un mensaje al usuario y ofrece un enlace para ir al login
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estado de Verificación de Correo</title>
    <link rel="stylesheet" href="../styles/css/registro.css"> <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .verification-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            padding: 2rem;
        }
        .verification-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 2.5rem;
            text-align: center;
        }
        .verification-card h1 {
            color: #6c1313;
            margin-bottom: 1.5rem;
        }
        .verification-card p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .verification-card .icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }
        .verification-card .icon.success {
            color: #28a745; /* Verde para éxito */
        }
        .verification-card .icon.error {
            color: #dc3545; /* Rojo para error */
        }
        .btn-return {
            display: inline-block;
            padding: 12px 25px;
            background: #6c1313;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }
        .btn-return:hover {
            background: #5a0f0f;
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-card">
            <?php if ($success): ?>
                <div class="icon success"><i class="fas fa-check-circle"></i></div>
                <h1>¡Verificación Exitosa!</h1>
            <?php else: ?>
                <div class="icon error"><i class="fas fa-times-circle"></i></div>
                <h1>Error en la Verificación</h1>
            <?php endif; ?>
            <p><?php echo htmlspecialchars($message); ?></p>
            <a href="login.php" class="btn-return">Ir a Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>