<?php
require_once "../includes/conexion1.php";
require_once "validaciones.php";

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

    // Validaciones
 // Validaciones
if (!validarCedulaEcuatoriana($cedula)) {
    $error = "La cédula ingresada no es válida";
} elseif (!tiene17OMas($fecha_nac)) {
    $error = "Debes tener al menos 17 años para registrarte";
} elseif ($password !== $confirm_password) {
    $error = "Las contraseñas no coinciden";
} elseif (!esContrasenaSegura($password)) {
    $error = "La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial";
} elseif (!esCorreoInstitucional($correo)) {
    $error = "Debe usar un correo institucional @uta.edu.ec";
} elseif (!validarCelularEcuatoriano($telefono)) {
    $error = "El número de teléfono debe ser un celular ecuatoriano válido (09XXXXXXXX)";
}

    // Si no hubo errores, continuar
    if (empty($error)) {
        // Verificar si el usuario ya existe
        $check_sql = "SELECT ced_usu FROM usuarios WHERE ced_usu = $1";
        $check_result = pg_query_params($conn, $check_sql, array($cedula));

        if (pg_num_rows($check_result) > 0) {
            $error = "El usuario con esta cédula ya existe";
        } else {
            // Verificar carrera
            $carreras_permitidas = ['Ing. Software', 'Ing. Industrial', 'Ing. Tecnologias de la Informacion', 'Ing. Telecomunicaciones', 'Ing. en Automatizacion y Robotica'];
            if (!in_array($carrera, $carreras_permitidas)) {
                $error = "La carrera seleccionada no es válida";
            } else {
                // Insertar usuario
                $insert_sql = "INSERT INTO usuarios 
                  (ced_usu, nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, 
                   car_usu, cor_usu, tel_usu, dir_usu, fec_nac_usu, pas_usu, id_rol_usu)
                  VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12)";

                $params = array(
                    $cedula, $nombre1, $nombre2, $apellido1, $apellido2,
                    $carrera, $correo, $telefono, $direccion, $fecha_nac, $password, 2
                );

                $result = pg_query_params($conn, $insert_sql, $params);

                if ($result) {
                    $success = "Registro exitoso. Ahora puedes iniciar sesión.";
                    header("refresh:3;url=login.php");
                } else {
                    $error = "Error al registrar el usuario: " . pg_last_error($conn);
                }
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".register-form");

    const inputs = {
    cedula: document.getElementById("cedula"),
    correo: document.getElementById("correo"),
    fecha_nac: document.getElementById("fecha_nac"),
    password: document.getElementById("password"),
    confirm_password: document.getElementById("confirm_password"),
    telefono: document.getElementById("telefono") // Añade esta línea
};
    const showError = (input, message) => {
        input.classList.add("input-error");
        let error = input.nextElementSibling;
        if (!error || !error.classList.contains("input-msg")) {
            error = document.createElement("div");
            error.classList.add("input-msg");
            input.parentNode.appendChild(error);
        }
        error.textContent = message;
    };

    const clearError = (input) => {
        input.classList.remove("input-error");
        let error = input.nextElementSibling;
        if (error && error.classList.contains("input-msg")) {
            error.remove();
        }
    };

    // Validaciones similares a las del archivo PHP
    const validarCedula = (cedula) => {
        if (!/^\d{10}$/.test(cedula)) return false;
        const provincia = parseInt(cedula.substring(0, 2));
        if (provincia < 1 || provincia > 24) return false;

        const digitoVerificador = parseInt(cedula[9]);
        let suma = 0;
        for (let i = 0; i < 9; i++) {
            let digito = parseInt(cedula[i]);
            if (i % 2 === 0) {
                digito *= 2;
                if (digito > 9) digito -= 9;
            }
            suma += digito;
        }
        let verificador = 10 - (suma % 10);
        if (verificador === 10) verificador = 0;
        return verificador === digitoVerificador;
    };

    const tiene17OMas = (fechaStr) => {
        const hoy = new Date();
        const nacimiento = new Date(fechaStr);
        const edad = hoy.getFullYear() - nacimiento.getFullYear();
        const mes = hoy.getMonth() - nacimiento.getMonth();
        const dia = hoy.getDate() - nacimiento.getDate();
        return edad > 17 || (edad === 17 && (mes > 0 || (mes === 0 && dia >= 0)));
    };

    const esCorreoInstitucional = (correo) => {
        return correo.toLowerCase().endsWith("@uta.edu.ec");
    };

    const esContrasenaSegura = (password) => {
        return /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(password);
    };
    
    const validarCelularEcuatoriano = (celular) => {
    return /^09\d{8}$/.test(celular);
    };

    form.addEventListener("submit", function (e) {
        let valid = true;

        clearError(inputs.cedula);
        if (!validarCedula(inputs.cedula.value)) {
            showError(inputs.cedula, "Cédula ecuatoriana inválida");
            valid = false;
        }

        clearError(inputs.correo);
        if (!esCorreoInstitucional(inputs.correo.value)) {
            showError(inputs.correo, "Use un correo institucional @uta.edu.ec");
            valid = false;
        }

        clearError(inputs.fecha_nac);
        if (!tiene17OMas(inputs.fecha_nac.value)) {
            showError(inputs.fecha_nac, "Debes tener al menos 17 años");
            valid = false;
        }

        clearError(inputs.password);
        if (!esContrasenaSegura(inputs.password.value)) {
            showError(inputs.password, "Mínimo 8 caracteres, 1 mayúscula, 1 minúscula, 1 número y 1 símbolo");
            valid = false;
        }

        clearError(inputs.confirm_password);
        if (inputs.password.value !== inputs.confirm_password.value) {
            showError(inputs.confirm_password, "Las contraseñas no coinciden");
            valid = false;
        }
            clearError(inputs.telefono);
        if (inputs.telefono.value && !validarCelularEcuatoriano(inputs.telefono.value)) {
            showError(inputs.telefono, "Ingrese un celular ecuatoriano válido (09XXXXXXXX)");
            valid = false;
        }


        if (!valid) {
            e.preventDefault(); 
        }
    });
});
</script>

<body>

    <div class="register-container">
        <div class="register-card">
        <div class="register-header">
        <h1>Registro de Usuario</h1>
        <img src="../imagenes/evento2.png" alt="Logo UTA" class="header-logo">
    </div>  

    <div class="register-form">
        <?php if (!empty($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php elseif (!empty($success)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

            
          <form action="registro.php" method="POST" class="register-form" autocomplete="off">
    <div class="form-group">
        <label for="cedula"><i class="fas fa-id-card"></i> Cédula</label>
        <input type="text" id="cedula" name="cedula" required maxlength="10" placeholder="Ej. 0102030405">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="nombre1"><i class="fas fa-user"></i> Primer Nombre</label>
            <input type="text" id="nombre1" name="nombre1" required placeholder="Ej. Juan">
        </div>
        <div class="form-group">
            <label for="nombre2">Segundo Nombre</label>
            <input type="text" id="nombre2" name="nombre2" placeholder="Ej. Carlos">
        </div>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="apellido1"><i class="fas fa-user"></i> Primer Apellido</label>
            <input type="text" id="apellido1" name="apellido1" required placeholder="Ej. Pérez">
        </div>
        <div class="form-group">
            <label for="apellido2">Segundo Apellido</label>
            <input type="text" id="apellido2" name="apellido2" placeholder="Ej. Morales">
        </div>
    </div>

    <div class="form-group">
        <label for="carrera"><i class="fas fa-graduation-cap"></i> Carrera</label>
        <select id="carrera" name="carrera" required>
            <option value="">Seleccione una carrera</option>
            <option value="Ing. Software">Ing. Software</option>
            <option value="Ing. Industrial">Ing. Industrial</option>
            <option value="Ing. Tecnologias de la Informacion">Ing. Tecnologías de la Información</option>
            <option value="Ing. Telecomunicaciones">Ing. Telecomunicaciones</option>
            <option value="Ing. en Automatizacion y Robotica">Ing. en Automatización y Robótica</option>
        </select>
    </div>

    <div class="form-group">
        <label for="correo"><i class="fas fa-envelope"></i> Correo Institucional</label>
        <input type="email" id="correo" name="correo" required placeholder="ejemplo@uta.edu.ec">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="telefono"><i class="fas fa-phone"></i> Teléfono</label>
            <input type="tel" id="telefono" name="telefono" placeholder="Ej. 0991234567">
        </div>
        <div class="form-group">
            <label for="fecha_nac"><i class="fas fa-birthday-cake"></i> Fecha de Nacimiento</label>
            <input type="date" id="fecha_nac" name="fecha_nac" required>
        </div>
    </div>

    <div class="form-group">
        <label for="direccion"><i class="fas fa-home"></i> Dirección</label>
        <input type="text" id="direccion" name="direccion" placeholder="Ej. Av. Universitaria y Bolívar">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> Contraseña</label>
            <input type="password" id="password" name="password" required
                placeholder="Mínimo 8 caracteres con símbolos y mayúsculas">
        </div>
        <div class="form-group">
            <label for="confirm_password"><i class="fas fa-lock"></i> Confirmar Contraseña</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repita su contraseña">
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