<?php
header('Content-Type: application/json');
require_once '../includes/conexion1.php';

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
    $queryProximosEventos = "SELECT 
                                TIT_EVE_CUR as titulo, 
                                DES_EVE_CUR as descripcion,
                                FEC_INI_EVE_CUR as fechaInicio, 
                                FEC_FIN_EVE_CUR as fechaFin,
                                COS_EVE_CUR as costo,
                                TIP_EVE as tipo,
                                MOD_EVE_CUR as modalidad
                            FROM EVENTOS_CURSOS
                            ORDER BY FEC_INI_EVE_CUR ASC";

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