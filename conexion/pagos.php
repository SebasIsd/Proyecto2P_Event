<?php
require_once 'conexion/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accion = $_POST['accion'] ?? '';

    $conn = CConexion::ConexionBD();
    header('Content-Type: application/json');

    if ($accion === 'buscar') {
        $cedula = $_POST['cedula'] ?? '';
        
        // Primero obtener información del usuario
        $queryUsuario = "SELECT * FROM USUARIOS WHERE CED_USU = :cedula";
        $stmtUsuario = $conn->prepare($queryUsuario);
        $stmtUsuario->bindParam(':cedula', $cedula);
        $stmtUsuario->execute();
        $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario) {
            echo json_encode(['error' => 'No se encontró un usuario con esta cédula']);
            exit;
        }
        
        // Luego obtener las inscripciones con sus pagos
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
                p.MET_PAG as metodo_pago
            FROM INSCRIPCIONES i
            INNER JOIN EVENTOS_CURSOS ec ON ec.ID_EVE_CUR = i.ID_EVE_CUR
            LEFT JOIN PAGOS p ON p.ID_INS = i.ID_INS
            WHERE i.CED_USU = :cedula
            ORDER BY ec.FEC_INI_EVE_CUR DESC
        ";
        
        $stmtInscripciones = $conn->prepare($queryInscripciones);
        $stmtInscripciones->bindParam(':cedula', $cedula);
        $stmtInscripciones->execute();
        $inscripciones = $stmtInscripciones->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'usuario' => $usuario,
            'inscripciones' => $inscripciones
        ]);
    }

    if ($accion === 'actualizar') {
        $id_inscripcion = $_POST['id_inscripcion'] ?? '';
        $estado = $_POST['estado_pago'] ?? '';
        
        $query = "UPDATE INSCRIPCIONES SET EST_PAG_INS = :estado WHERE ID_INS = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id_inscripcion);
        $success = $stmt->execute();
        
        echo json_encode(['success' => $success]);
    }
}
