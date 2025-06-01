<?php
require_once "../includes/conexion1.php";
error_reporting(E_ALL);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Recoger y limpiar datos
    $cedula = pg_escape_string($conn, $_POST['cedula']);
    $nombre1 = pg_escape_string($conn, $_POST['nombre1']);
    $nombre2 = pg_escape_string($conn, $_POST['nombre2']);
    $apellido1 = pg_escape_string($conn, $_POST['apellido1']);
    $apellido2 = pg_escape_string($conn, $_POST['apellido2']);
    $carrera = pg_escape_string($conn, $_POST['carrera']);
    $correo = pg_escape_string($conn, $_POST['correo']);
    $telefono = pg_escape_string($conn, $_POST['telefono']);
    $direccion = pg_escape_string($conn, $_POST['direccion']);
    $fecha_nac = pg_escape_string($conn, $_POST['fecha_nac']);
    $password = pg_escape_string($conn, $_POST['password']);
    $confirm_password = pg_escape_string($conn, $_POST['confirm_password']);
    


        // Validaciones básicas
    if ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden";
    } elseif (!preg_match('/@uta\.edu\.ec$/i', $correo)) {
        $error = "Debe usar un correo institucional @uta.edu.ec";
    } else {
        // Verificar si el usuario ya existe
        $check_sql = "SELECT ced_usu FROM usuarios WHERE ced_usu = $1";
        $check_result = pg_query_params($conn, $check_sql, array($cedula));
        
        if (pg_num_rows($check_result) > 0) {
            $error = "El usuario con esta cédula ya existe";
        } else {
        $carreras_permitidas = ['Ing. Software', 'Ing. Industrial', 'Ing. Tecnologias de la Informacion', 'Ing. Telecomunicaciones', 'Ing. en Automatizacion y Robotica'];
        if (!in_array($carrera, $carreras_permitidas)) {
        $error = "La carrera seleccionada no es válida";
        }
            // Hash de la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $insert_sql = "INSERT INTO usuarios 
              (ced_usu, nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, 
               car_usu, cor_usu, tel_usu, dir_usu, fec_nac_usu, pas_usu, id_rol_usu)
              VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

// Rol de usuario común
$params = array(
    $cedula, $nombre1, $nombre2, $apellido1, $apellido2,
    $carrera, $correo, $telefono, $direccion, $fecha_nac, $hashed_password,
    2 );

$result = pg_query_params($conn, $insert_sql, $params);
            
            if ($result) {
                $success = "Registro exitoso. Ahora puedes iniciar sesión.";
                header("refresh:3;url=login.php");
            } else {
                $error = "Error al registrar el usuario: " . pg_last_error($conn);
            }
        }
    }
    
    pg_close($conn);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/registro.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>


    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
             <img src="../imagenes/evento2.png" alt="Logo UTA" class="header-logo">
                <h2><i class="fas fa-user-plus"></i> Crear Cuenta</h2>
                <p>Regístrate para acceder al sistema</p>
            </div>
            
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
            
            <form action="registro.php" method="POST" class="register-form">
                <div class="form-group">
                    <label for="cedula"><i class="fas fa-id-card"></i> Cédula</label>
                    <input type="text" id="cedula" name="cedula" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre1"><i class="fas fa-user"></i> Primer Nombre</label>
                        <input type="text" id="nombre1" name="nombre1" required>
                    </div>
                    <div class="form-group">
                        <label for="nombre2">Segundo Nombre</label>
                        <input type="text" id="nombre2" name="nombre2">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="apellido1"><i class="fas fa-user"></i> Primer Apellido</label>
                        <input type="text" id="apellido1" name="apellido1" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido2">Segundo Apellido</label>
                        <input type="text" id="apellido2" name="apellido2">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="carrera"><i class="fas fa-graduation-cap"></i> Carrera</label>
                    <select id="carrera" name="carrera" required>
                        <option value="">Seleccione una carrera</option>
                        <option value="Ing. Software">Ing. Software</option>
                        <option value="Ing. Industrial">Ing. Industrial</option>
                        <option value="Ing. Tecnologias de la Informacion">Ing. Tecnologias de la Informacion</option>
                        <option value="Ing. Telecomunicaciones">Ing. Telecomunicaciones</option>
                        <option value="Ing. en Automatizacion y Robotica">Ing. en Automatizacion y Robotica</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="correo"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                
                <div class="form-group">
                    <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>
                
                <div class="form-group">
                    <label for="direccion"><i class="fas fa-home"></i> Dirección</label>
                    <input type="text" id="direccion" name="direccion">
                </div>
                
                <div class="form-group">
                    <label for="fecha_nac"><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
                    <input type="date" id="fecha_nac" name="fecha_nac">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                
                <button type="submit" class="register-btn">
                    <i class="fas fa-user-plus"></i> Registrarse
                </button>
                
                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Inicia sesión</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>