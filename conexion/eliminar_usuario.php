<?php
require_once("conexionusu.php");

$conexion = ConexionUsu::obtenerConexion();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cedula = $_POST["cedula"] ?? '';

    if (empty($cedula)) {
        echo json_encode(['error' => 'Cédula no recibida']);
        exit;
    }

    // Iniciar transacción
    pg_query($conexion, "BEGIN");

    try {
        // Obtener todas las inscripciones del usuario
        $sql_ins = "SELECT ID_INS FROM INSCRIPCIONES WHERE CED_USU = $1";
        $res_ins = pg_query_params($conexion, $sql_ins, [$cedula]);

        while ($row = pg_fetch_assoc($res_ins)) {
            $id_ins = $row['id_ins'];

            // Eliminar primero dependencias en orden inverso
            pg_query_params($conexion, "DELETE FROM IMAGENES WHERE ID_INS = $1", [$id_ins]);
            pg_query_params($conexion, "DELETE FROM NOTAS_ASISTENCIAS WHERE ID_INS = $1", [$id_ins]);
            pg_query_params($conexion, "DELETE FROM CERTIFICADOS WHERE ID_INS = $1", [$id_ins]);
            pg_query_params($conexion, "DELETE FROM PAGOS WHERE ID_INS = $1", [$id_ins]);
        }

        // Eliminar las inscripciones
        pg_query_params($conexion, "DELETE FROM INSCRIPCIONES WHERE CED_USU = $1", [$cedula]);

        // Finalmente eliminar el usuario
        pg_query_params($conexion, "DELETE FROM USUARIOS WHERE CED_USU = $1", [$cedula]);

        // Confirmar transacción
        pg_query($conexion, "COMMIT");
        echo json_encode(['success' => true, 'mensaje' => 'Usuario y datos relacionados eliminados correctamente']);
    } catch (Exception $e) {
        pg_query($conexion, "ROLLBACK");
        echo json_encode(['error' => 'Error al eliminar: ' . $e->getMessage()]);
    }
}
?>