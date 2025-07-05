<?php
header('Content-Type: application/json');
require_once '../includes/conexion1.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

try {
    $conexion = new Conexion();
    $conn = $conexion->getConexion();
    
    // Consulta para contar usuarios
    $queryUsuarios = "SELECT COUNT(*) as total FROM USUARIOS";
    $resultUsuarios = pg_query($conn, $queryUsuarios);
    $totalUsuarios = pg_fetch_assoc($resultUsuarios)['total'];
    
    // Consulta para contar eventos activos (fecha mayor o igual a hoy)
    $queryEventos = "SELECT COUNT(*) as total FROM EVENTOS_CURSOS";
    $resultEventos = pg_query($conn, $queryEventos);
    $totalEventos = pg_fetch_assoc($resultEventos)['total'];
    
    // Consulta para contar inscripciones
    $queryInscripciones = "SELECT COUNT(*) as total FROM INSCRIPCIONES";
    $resultInscripciones = pg_query($conn, $queryInscripciones);
    $totalInscripciones = pg_fetch_assoc($resultInscripciones)['total'];
    
// Consulta para obtener todos los eventos
// Consulta para obtener eventos favoritos
$queryProximosEventos = "SELECT 
                            ec.ID_EVE_CUR as codigo,
                            ec.TIT_EVE_CUR as titulo, 
                            ec.DES_EVE_CUR as descripcion,
                            ec.FEC_INI_EVE_CUR as fechaInicio, 
                            ec.FEC_FIN_EVE_CUR as fechaFin,
                            ec.COS_EVE_CUR as costo,
                            ec.MOD_EVE_CUR as modalidad,
                            true as es_favorito
                        FROM 
                            EVENTOS_CURSOS ec
                        JOIN 
                            favoritos_evento f ON ec.ID_EVE_CUR = f.ID_EVE_CUR
                        ORDER BY 
                            ec.FEC_INI_EVE_CUR ASC";

$resultProximosEventos = pg_query($conn, $queryProximosEventos);
$proximosEventos = [];
while ($row = pg_fetch_assoc($resultProximosEventos)) {
    $proximosEventos[] = $row;
}
    
    // Devolver datos como JSON
    echo json_encode([
        'totalUsuarios' => $totalUsuarios,
        'totalEventos' => $totalEventos,
        'totalInscripciones' => $totalInscripciones,
        'proximosEventos' => $proximosEventos
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>