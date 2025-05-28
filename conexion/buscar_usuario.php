<?php
require_once 'conexionusu.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido", 405);
    }

    $conexion = ConexionUsu::obtenerConexion();
    if (!$conexion) {
        throw new Exception("No se pudo conectar a la base de datos", 500);
    }

    $accion = $_POST['accion'] ?? '';
    $cedula = $_POST['ced_usu'] ?? '';

    if ($accion === 'buscar') {
        if (empty($cedula)) {
            throw new Exception("Cédula no proporcionada", 400);
        }

        $sql = "SELECT * FROM usuarios WHERE ced_usu = $1";
        $resultado = pg_query_params($conexion, $sql, [$cedula]);
        
        if (!$resultado) {
            throw new Exception("Error en la consulta: " . pg_last_error($conexion), 500);
        }

        $usuario = pg_fetch_assoc($resultado);
        if (!$usuario) {
            throw new Exception("Usuario no encontrado", 404);
        }

        http_response_code(200);
        echo json_encode($usuario);
    }
    elseif ($accion === 'actualizar') {
        $sql = "UPDATE usuarios SET 
                    nom_pri_usu = $1,
                    nom_seg_usu = $2,
                    ape_pri_usu = $3,
                    ape_seg_usu = $4,
                    cor_usu = $5,
                    pas_usu = $6,
                    tel_usu = $7,
                    dir_usu = $8,
                    fec_nac_usu = $9
                WHERE ced_usu = $10";
                
        $resultado = pg_query_params($conexion, $sql, [
            $_POST['nom_pri_usu'],
            $_POST['nom_seg_usu'],
            $_POST['ape_pri_usu'],
            $_POST['ape_seg_usu'],
            $_POST['cor_usu'],
            $_POST['pas_usu'],
            $_POST['tel_usu'],
            $_POST['dir_usu'],
            $_POST['fec_nac_usu'],
            $_POST['ced_usu']
        ]);

        if ($resultado) {
            echo json_encode(['success' => true, 'mensaje' => 'Datos actualizados correctamente']);
        } else {
            throw new Exception("Error al actualizar: " . pg_last_error($conexion), 500);
        }
    }
    else {
        throw new Exception("Acción no válida", 400);
    }
} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'codigo' => $e->getCode()
    ]);
} finally {
    ConexionUsu::cerrarConexion();
}
?>