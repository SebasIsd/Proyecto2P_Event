<?php
// detalles.php
session_start();
require_once "../../includes/conexion1.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// Verificar que se proporcionó el ID
if (!isset($_GET['id_inscripcion'])) {
    die("Error: No se proporcionó ID de inscripción");
}

$id_inscripcion = filter_var($_GET['id_inscripcion'], FILTER_VALIDATE_INT);

if (!$id_inscripcion) {
    die("Error: ID de inscripción inválido");
}

// Obtener datos de la inscripción
$conexion = new Conexion();
$conn = $conexion->getConexion();

try {
    $sql = "SELECT i.ID_INS, ec.TIT_EVE_CUR, ec.MOD_EVE_CUR,
                   u.NOM_PRI_USU, u.APE_PRI_USU
            FROM INSCRIPCIONES i
            JOIN EVENTOS_CURSOS ec ON i.ID_EVE_CUR = ec.ID_EVE_CUR
            JOIN USUARIOS u ON i.CED_USU = u.CED_USU
            WHERE i.ID_INS = $1";
    
    $result = pg_query_params($conn, $sql, array($id_inscripcion));
    
    if (pg_num_rows($result) === 0) {
        die("No se encontró la inscripción con ID: $id_inscripcion");
    }
    
    $inscripcion = pg_fetch_assoc($result);
    
} catch (Exception $e) {
    die("Error al obtener datos: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Detalles de Inscripción #<?= htmlspecialchars($id_inscripcion) ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .comprobante-box { 
            margin: 20px 0; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 5px;
            background: #f9f9f9;
        }
        .comprobante-img {
            max-width: 100%;
            max-height: 500px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>
    <h1>Detalles de Inscripción</h1>

    <input type="text" id="inputIdInscripcion" placeholder="Escribe un ID de inscripción">
<button type="button" onclick="verComprobante()">Ver Comprobante</button>

<script>
function verComprobante() {
    const id = document.getElementById('inputIdInscripcion').value;
    if (id && !isNaN(id)) {
        window.location.href = 'detalles.php?id_inscripcion=' + encodeURIComponent(id);
    } else {
        alert('Por favor, ingresa un ID válido.');
    }
}
</script>

    
    <p><strong>ID:</strong> <?= $id_inscripcion ?></p>
    
    <div class="comprobante-box">
        <h3>Comprobante de Pago:</h3>
        <?php
        // Verificar si existe comprobante
        $sql_comprobante = "SELECT 1 FROM IMAGENES WHERE ID_INS = $1";
        $result = pg_query_params($conn, $sql_comprobante, array($id_inscripcion));
        
        if (pg_num_rows($result) > 0): ?>
            <img src="ver_comprobante.php?id_inscripcion=<?= $id_inscripcion ?>" 
                 class="comprobante-img"
                 alt="Comprobante de pago">
            <p>
                <a href="ver_comprobante.php?id_inscripcion=<?= $id_inscripcion ?>" 
                   download="comprobante_<?= $id_inscripcion ?>.jpg">
                   Descargar comprobante
                </a>
            </p>
        <?php else: ?>
            <p>No hay comprobante de pago adjunto</p>
        <?php endif; ?>
    </div>
    
    <p><a href="javascript:history.back()">Volver atrás</a></p>
</body>
</html>
<?php
pg_close($conn);
?>