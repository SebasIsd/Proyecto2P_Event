<?php
include_once("../conexion/conexion.php");
//Verificar si es una solicitud POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
       
        // Obtener y limpiar datos del formulario
        $titulo = $_POST["titulo"] ?? '';
        $descripcion = $_POST["descripcion"] ?? '';
        $tipo = $_POST["tipo"] ?? '';
        $fechaInicio = $_POST["fechaInicio"] ?? '';
        $fechaFin = $_POST["fechaFin"] ?? '';
        $modalidad = $_POST["modalidad"] ?? '';
        $costo = $_POST["costo"] ?? '0.00';
        
try {
        $conn = CConexion::ConexionBD();
        $sql = "INSERT INTO EVENTOS_CURSOS (
                TIT_EVE_CUR, 
                DES_EVE_CUR, 
                FEC_INI_EVE_CUR, 
                FEC_FIN_EVE_CUR, 
                COS_EVE_CUR, 
                TIP_EVE, 
                MOD_EVE_CUR
            ) VALUES (
                :titulo, 
                :descripcion, 
                :fechaInicio, 
                :fechaFin, 
                :costo, 
                :tipo, 
                :modalidad)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':modalidad', $modalidad);
        
        $stmt->execute();
     echo "✅ Evento guardado correctamente.";   
     
    } catch (PDOException $e) {
        echo "❌ Error al guardar el evento: " . $e->getMessage();
    }
} else {
    echo "❌ Solicitud inválida.";
}
?>