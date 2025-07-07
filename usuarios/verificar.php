<?php
require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $sql = "UPDATE usuarios SET email_verified = TRUE, email_verification_token = NULL WHERE email_verification_token = $1";
    $result = pg_query_params($conn, $sql, array($token));

    if (pg_affected_rows($result) > 0) {
        echo "✅ Verificación exitosa. Ahora puedes iniciar sesión.";
    } else {
        echo "❌ Token inválido o ya usado.";
    }
} else {
    echo "❌ Token no proporcionado.";
}
?>
