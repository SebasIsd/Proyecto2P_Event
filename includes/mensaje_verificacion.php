<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Correo Enviada</title>
    <link rel="stylesheet" href="../styles/css/registro.css"> <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos específicos para esta página */
        .message-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f5f5f5;
            padding: 2rem;
        }
        .message-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            padding: 2.5rem;
            text-align: center;
        }
        .message-card h1 {
            color: #6c1313;
            margin-bottom: 1.5rem;
        }
        .message-card p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .message-card .icon {
            font-size: 4rem;
            color: #28a745; /* Color para indicar éxito/información */
            margin-bottom: 1.5rem;
        }
        .btn-primary {
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
        .btn-primary:hover {
            background: #5a0f0f;
        }
    </style>
</head>
<body>
    <div class="message-container">
        <div class="message-card">
            <div class="icon"><i class="fas fa-envelope-open-text"></i></div>
            <h1>¡Correo de Verificación Enviado!</h1>
            <p>Hemos enviado un correo electrónico a tu dirección para verificar tu cuenta. Por favor, revisa tu bandeja de entrada (y la carpeta de spam) y haz clic en el enlace de verificación para activar tu cuenta.</p>
            <p>Una vez verificado, podrás iniciar sesión.</p>
            <a href="login.php" class="btn-primary">Ir a Iniciar Sesión</a>
        </div>
    </div>
</body>
</html>