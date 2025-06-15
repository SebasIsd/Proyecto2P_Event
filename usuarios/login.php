<?php
session_start();
require_once('../includes/conexion1.php');

$conexion = new Conexion();
$conn = $conexion->getConexion();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    
    // Validar formato de correo en el servidor también
    if (!preg_match('/@uta\.edu\.ec$/', $correo)) {
        $error = "Debe usar un correo institucional @uta.edu.ec";
    } else {
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
                    header("Location: ../admin/admin.html");
                } else {
                    header("Location: inicio.php");
                }
                exit();
            }
        }

        $error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos adicionales específicos para el login */
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            padding: 2rem;
        }
        
        .login-box {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #6c1313, #8a1a1a);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .login-header p {
            opacity: 0.9;
            font-size: 0.9rem;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .login-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .login-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .login-form input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .login-form input:focus {
            border-color: #6c1313;
            box-shadow: 0 0 0 3px rgba(108, 19, 19, 0.1);
            outline: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #6c1313;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }
        
        .btn-login:hover {
            background: #5a0f0f;
        }
        
        .btn-secondary {
            width: 100%;
            padding: 12px;
            background: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 1rem;
        }
        
        .button-group .btn-login {
            flex: 2;
        }
        
        .button-group .btn-secondary {
            flex: 1;
        }
        
        .login-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
            font-size: 0.9rem;
        }
        
        .login-footer a {
            color: #6c1313;
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .error-message {
            background: #ffeeee;
            color: #6c1313;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #d32f2f;
            font-size: 0.9rem;
            display: <?php echo !empty($error) ? 'block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1><i class="fas fa-user-lock"></i> Acceso al Sistema</h1>
                <p>Ingresa tus credenciales para continuar</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <form class="login-form" method="POST" id="loginForm">
                    <div class="form-group">
                        <label for="correo"><i class="fas fa-envelope"></i> Correo electrónico</label>
                        <input type="email" id="correo" name="correo" required placeholder="tu@uta.edu.ec">
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••">
                    </div>
                    
                    <div class="button-group">
                        <button type="submit" class="btn-login">
                            <i class="fas fa-sign-in-alt"></i> Ingresar
                        </button>
                        <br>
                        <a href="../index.html" class="btn-secondary">
                            <i class="fas fa-arrow-left"></i> Regresar
                        </a>
                    </div>
                </form>
                
                <div class="login-footer">
                    <p>¿No tienes una cuenta? <a href="../usuarios/registro.php" class="btn-register" style="background: none; border: none; color: #6c1313; cursor: pointer; font-weight: 500; padding: 0;">Regístrate aquí</a></p>
                    <p>¿Olvidaste tu contraseña? <a href="recuperar.php">Recupérala aquí</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <footer style="background: #222; color: white; text-align: center; padding: 1.5rem;">
        <div class="container">
            <div style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 2rem;">
                <div style="flex: 1; min-width: 200px;">
                    <h3 style="color: #6c1313; margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Sobre el Sistema</h3>
                    <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <h3 style="color: #6c1313; margin-bottom: 1rem;"><i class="fas fa-envelope"></i> Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</p>
                    <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p>
                    <p><i class="fas fa-phone"></i> +123 456 7890</p>
                </div>
                <div style="flex: 1; min-width: 200px;">
                    <h3 style="color: #6c1313; margin-bottom: 1rem;"><i class="fas fa-link"></i> Enlaces Rápidos</h3>
                    <ul style="list-style: none; padding: 0;">
                        <li><a href="../index.html" style="color: #ccc; text-decoration: none;"><i class="fas fa-chevron-right"></i> Inicio</a></li>
                        <li><a href="#" style="color: #ccc; text-decoration: none;"><i class="fas fa-chevron-right"></i> Eventos</a></li>
                        <li><a href="#" style="color: #ccc; text-decoration: none;"><i class="fas fa-chevron-right"></i> Políticas</a></li>
                    </ul>
                </div>
            </div>
            <div style="margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #444;">
                <p>&copy; 2023 Sistema de Inscripciones. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const correo = document.getElementById('correo').value;
            
            // Validar que el correo termine con @uta.edu.ec
            if (!correo.endsWith('@uta.edu.ec')) {
                e.preventDefault();
                alert('Debe usar un correo institucional @uta.edu.ec');
                document.getElementById('correo').focus();
                
                // Mostrar el mensaje de error si está oculto
                const errorDiv = document.querySelector('.error-message');
                if (errorDiv) {
                    errorDiv.textContent = 'Debe usar un correo institucional @uta.edu.ec';
                    errorDiv.style.display = 'block';
                }
            }

            
        });
    </script>
</body>
</html>