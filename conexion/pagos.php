<?php
require_once '../conexion/conexion2.php';

// Habilitar CORS para desarrollo
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';
    
    try {
        $conn = CConexion2::obtenerConexion();
        
        if (!$conn) {
            throw new Exception("No se pudo establecer conexión con la base de datos");
        }

        if ($accion === 'buscar') {
            $cedula = $_POST['cedula'] ?? '';
            
            if (empty($cedula)) {
                throw new Exception("Cédula no proporcionada");
            }

            // 1. Obtener información del usuario
            $queryUsuario = "SELECT * FROM USUARIOS WHERE CED_USU = :cedula";
            $stmtUsuario = $conn->prepare($queryUsuario);
            $stmtUsuario->bindParam(':cedula', $cedula);
            $stmtUsuario->execute();
            $usuario = $stmtUsuario->fetch();
            
            if (!$usuario) {
                throw new Exception("No se encontró un usuario con esta cédula");
            }
            
            // 2. Obtener inscripciones con pagos
            $queryInscripciones = "
                SELECT 
                    i.ID_INS as id_inscripcion, 
                    ec.TIT_EVE_CUR as nombre_evento, 
                    ec.TIP_EVE as tipo_evento,
                    ec.FEC_INI_EVE_CUR as fecha_inicio,
                    ec.FEC_FIN_EVE_CUR as fecha_fin,
                    ec.COS_EVE_CUR as costo_evento,
                    i.EST_PAG_INS as estado_pago,
                    p.FEC_PAG as fecha_pago, 
                    p.MON_PAG as monto_pago, 
                    p.MET_PAG as metodo_pago,
                    p.ID_PAG as id_pago
                FROM INSCRIPCIONES i
                INNER JOIN EVENTOS_CURSOS ec ON ec.ID_EVE_CUR = i.ID_EVE_CUR
                LEFT JOIN PAGOS p ON p.ID_INS = i.ID_INS
                WHERE i.CED_USU = :cedula
                ORDER BY ec.FEC_INI_EVE_CUR DESC
            ";
            
            $stmtInscripciones = $conn->prepare($queryInscripciones);
            $stmtInscripciones->bindParam(':cedula', $cedula);
            $stmtInscripciones->execute();
            $inscripciones = $stmtInscripciones->fetchAll();
            
            // 3. Agrupar múltiples pagos por inscripción
            $inscripcionesAgrupadas = [];
            foreach ($inscripciones as $insc) {
                $id = $insc['id_inscripcion'];
                if (!isset($inscripcionesAgrupadas[$id])) {
                    $inscripcionesAgrupadas[$id] = [
                        'id_inscripcion' => $id,
                        'nombre_evento' => $insc['nombre_evento'],
                        'tipo_evento' => $insc['tipo_evento'],
                        'fecha_inicio' => $insc['fecha_inicio'],
                        'fecha_fin' => $insc['fecha_fin'],
                        'costo_evento' => $insc['costo_evento'],
                        'estado_pago' => $insc['estado_pago'],
                        'pagos' => []
                    ];
                }
                
                if ($insc['id_pago']) {
                    $inscripcionesAgrupadas[$id]['pagos'][] = [
                        'fecha_pago' => $insc['fecha_pago'],
                        'monto_pago' => $insc['monto_pago'],
                        'metodo_pago' => $insc['metodo_pago'],
                        'id_pago' => $insc['id_pago']
                    ];
                }
            }
            
            echo json_encode([
                'success' => true,
                'usuario' => $usuario,
                'inscripciones' => array_values($inscripcionesAgrupadas)
            ]);
        }
        elseif ($accion === 'actualizar') {
            $id_inscripcion = $_POST['id_inscripcion'] ?? '';
            $estado = $_POST['estado_pago'] ?? '';
            
            if (empty($id_inscripcion) || empty($estado)) {
                throw new Exception("Datos incompletos para actualización");
            }
            
            $query = "UPDATE INSCRIPCIONES SET EST_PAG_INS = :estado WHERE ID_INS = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':estado', $estado);
            $stmt->bindParam(':id', $id_inscripcion);
            $success = $stmt->execute();
            
            echo json_encode(['success' => $success]);
        }
        else {
            throw new Exception("Acción no válida");
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
    finally {
        CConexion2::cerrarConexion();
    }
}
else {
    echo json_encode([
        'success' => false,
        'error' => 'Método no permitido'
    ]);
}
?>