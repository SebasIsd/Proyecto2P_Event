<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost'; //cambiar con lo de la base de datos
$dbname = 'nombre_base_datos';
$user = 'usuario';
$password = 'contraseña';

try {
    $conn = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Recibir datos del formulario
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validar datos
    $requiredFields = ['cedula', 'id_evento', 'fecha_inscripcion', 'fecha_cierre', 'estado_pago'];
    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            throw new Exception("El campo $field es requerido");
        }
    }

    // Insertar inscripción
    $stmt = $conn->prepare("
        INSERT INTO INSCRIPCIONES (
            CED_USU, 
            ID_EVE_CUR, 
            FEC_INI_INS, 
            FEC_CIE_INS, 
            EST_PAG_INS
        ) VALUES (
            :cedula, 
            :id_evento, 
            :fecha_inscripcion, 
            :fecha_cierre, 
            :estado_pago::estado_pago
        ) RETURNING ID_INS
    ");
    
    $stmt->bindParam(':cedula', $data['cedula']);
    $stmt->bindParam(':id_evento', $data['id_evento']);
    $stmt->bindParam(':fecha_inscripcion', $data['fecha_inscripcion']);
    $stmt->bindParam(':fecha_cierre', $data['fecha_cierre']);
    $stmt->bindParam(':estado_pago', $data['estado_pago']);
    
    $stmt->execute();
    $inscripcionId = $stmt->fetchColumn();
    
    // Si es pago, registrar el pago
    if ($data['estado_pago'] === 'Pagado' && !empty($data['pago'])) {
        $stmtPago = $conn->prepare("
            INSERT INTO PAGOS (
                ID_INS, 
                FEC_PAG, 
                MON_PAG, 
                MET_PAG
            ) VALUES (
                :id_inscripcion, 
                :fecha_pago, 
                :monto, 
                :metodo
            )
        ");
        
        $stmtPago->bindParam(':id_inscripcion', $inscripcionId);
        $stmtPago->bindParam(':fecha_pago', $data['pago']['fecha']);
        $stmtPago->bindParam(':monto', $data['pago']['monto']);
        $stmtPago->bindParam(':metodo', $data['pago']['metodo']);
        $stmtPago->execute();
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Inscripción registrada correctamente',
        'id_inscripcion' => $inscripcionId
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error de base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}