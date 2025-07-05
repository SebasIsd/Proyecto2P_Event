    <?php
    // validaciones.php

    function validarCedulaEcuatoriana($cedula) {
        if (strlen($cedula) != 10 || !is_numeric($cedula)) {
            return false;
        }

        $provincia = (int) substr($cedula, 0, 2);
        if ($provincia < 1 || $provincia > 24) {
            return false;
        }

        $digito_verificador = (int) substr($cedula, 9, 1);
        $suma = 0;

        for ($i = 0; $i < 9; $i++) {
            $digito = (int) $cedula[$i];
            if ($i % 2 == 0) {
                $digito *= 2;
                if ($digito > 9) $digito -= 9;
            }
            $suma += $digito;
        }

        $verificador = 10 - ($suma % 10);
        if ($verificador == 10) $verificador = 0;

        return $verificador === $digito_verificador;
    }

    function tiene17OMas($fecha_nac) {
        $hoy = new DateTime();
        $nacimiento = new DateTime($fecha_nac);
        $edad = $hoy->diff($nacimiento)->y;
        return $edad >= 17;
    }

    function esCorreoInstitucional($correo) {
        return preg_match('/@uta\.edu\.ec$/i', $correo);
    }
    
    function esContrasenaSegura($password) {
    return preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}
function validarCelularEcuatoriano($celular) {
    $celular = preg_replace('/[^0-9]/', '', $celular);
    
    if (strlen($celular) != 10 || substr($celular, 0, 2) != '09') {
        return false;
    }
    
    // Validar que el tercer dígito (después de 09) sea entre 0 y 9
    $tercerDigito = (int) substr($celular, 2, 1);
    if ($tercerDigito < 0 || $tercerDigito > 9) {
        return false;
    }
    
    // Todos los dígitos deben ser numéricos
    if (!ctype_digit($celular)) {
        return false;
    }
    
    return true;
}

    ?>
