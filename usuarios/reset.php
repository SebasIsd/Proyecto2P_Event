<?php
session_start();
require_once('../includes/conexion1.php');

$conexion = new Conexion();
$conn = $conexion->getConexion();
$error = "";
$success = "";
$show_form = true;

$cedula = $_SESSION['reset_cedula'] ?? $_GET['ced'] ?? null;

if (!$cedula) {
    header("Location: recuperar.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($password) || strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Se elimino l aincriptación de la contraseña, ahora se almacena en texto plano
        $sql = "UPDATE usuarios SET pas_usu = $1 WHERE ced_usu = $2";
        $result = pg_query_params($conn, $sql, array($password, $cedula));
        
        if ($result && pg_affected_rows($result) > 0) {
            $success = "¡Contraseña actualizada! </a>.";
            unset($_SESSION['reset_cedula']);
            $show_form = false;
        } else {
            $error = "Hubo un error al actualizar la contraseña: " . pg_last_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../styles/css/recuperar.css">

</head>
<body>
    <div class="recovery-container">
        <div class="recovery-card"> <div class="recovery-header">
                <h1><i class="fas fa-lock"></i> Restablecer Contraseña</h1>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-error"> <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"> <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>


            <?php if ($show_form && empty($success)):  ?>
                <form method="POST">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Nueva contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn"> <i class="fas fa-save"></i> Guardar nueva contraseña
                    </button>
                </form>
            <?php endif; ?>

            <?php if (empty($cedula)):  ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> El enlace de recuperación no es válido o ha expirado.
                </div>
                <div class="login-footer" style="margin-top: 1.5rem;">
                    <p><a href="recuperar.php"><i class="fas fa-redo"></i> Solicitar nuevo enlace de recuperación</a></p>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)):  ?>
                <div class="login-footer" style="margin-top: 1.5rem;">
                    <p><a href="login.php"><i class="fas fa-sign-in-alt"></i> Volver al inicio de sesión</a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>