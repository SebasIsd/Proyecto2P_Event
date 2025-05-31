<?php
ini_set('display_errors', 0); 
ini_set('log_errors', 1);     
error_reporting(E_ALL);       


require '../vendor/autoload.php';
include_once '../conexion/conexion.php';
use Dompdf\Dompdf;

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);
$idIns = $data['id_ins'] ?? null;

if (!$idIns) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

try {
  $conn = CConexion::ConexionBD();
  

  $plantilla = $conn->query("
  SELECT * FROM PLANTILLAS_CERTIFICADOS where ID_PLAN_CER =1
    ")->fetch(PDO::FETCH_ASSOC);

    if (!$plantilla) {
        echo json_encode(['error' => 'No se encontró ninguna plantilla de certificado']);
        exit;
    }

$encabezado = trim($plantilla['encabezado_cer'] ?? '');
$cuerpo     = trim($plantilla['cuerpo_cer'] ?? '');
$pie        = trim($plantilla['pie_cer'] ?? '');




    if (empty($encabezado) || empty($cuerpo) || empty($pie)) {
        echo json_encode(['error' => 'La plantilla tiene campos vacíos']);
        exit;
    }

    $stmt = $conn->prepare("
    SELECT 
        CONCAT(U.NOM_PRI_USU, ' ', U.NOM_SEG_USU, ' ', U.APE_PRI_USU, ' ', U.APE_SEG_USU) AS nombre_completo,
        U.CED_USU AS cedula,
        E.TIT_EVE_CUR AS titulo_evento,
        E.FEC_FIN_EVE_CUR AS fecha_fin
    FROM INSCRIPCIONES I
    JOIN USUARIOS U ON I.CED_USU = U.CED_USU
    JOIN EVENTOS_CURSOS E ON I.ID_EVE_CUR = E.ID_EVE_CUR
    WHERE I.ID_INS = :id
");

    $stmt->execute([':id' => $idIns]);
    $datos = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$datos) {
        echo json_encode(['error' => 'Datos de inscripción no encontrados']);
        exit;
    }

    $fechaEmision = date('Y-m-d');

    // Generar HTML
  $html = "
<div style='
    width: 100vw;
    height: 100vh;
    box-sizing: border-box;
    padding: 3rem 4rem;
    border: 5px solid #6c1313;
    border-radius: 15px;
    font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    background-color: #fff;
'>
    <h1 style='
        font-size: 3rem;
        font-weight: 700;
        color: #6c1313;
        margin-bottom: 2rem;
        text-transform: uppercase;
        letter-spacing: 2px;
        '>$encabezado</h1>
    <p style='
        font-size: 1.4rem;
        color: #000000;
        line-height: 1.6;
        max-width: 800px;
        '>$cuerpo</p>
    <p style='
        margin-top: 3rem;
        font-style: italic;
        font-size: 1.1rem;
        color: #7f8c8d;
        '>$pie</p>
</div>";

    // Reemplazos
    $html = str_replace(
        ['[NOMBRE_COMPLETO]', '[CEDULA]', '[TITULO_EVENTO]', '[FECHA_FIN]', '[FECHA_EMISION]'],
        [
           $datos['nombre_completo'],
    $datos['cedula'],
    $datos['titulo_evento'],
    $datos['fecha_fin'],
    $fechaEmision
        ],
        $html
    );

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();




    $nombreArchivo = "certificado_{$idIns}.pdf";
     $rutaCarpeta = "../certificados/";
    $rutaCompleta = $rutaCarpeta . $nombreArchivo;

    if (!is_dir($rutaCarpeta)) {
        mkdir($rutaCarpeta, 0777, true);
    }

    file_put_contents($rutaCompleta, $dompdf->output());


    $insert = $conn->prepare("
        INSERT INTO CERTIFICADOS (ID_INS, FEC_EMI_CER, ID_PLAN_CER, HTML_GENERADO)
        VALUES (:id_ins, :fec, :id_plan, :ruta_pdf)
    ");
    $insert->execute([
        ':id_ins' => $idIns,
        ':fec' => $fechaEmision,
        ':id_plan' => $plantilla['ID_PLAN_CER'],
        ':ruta_pdf' => $rutaCompleta
    ]);

    echo json_encode(['success' => true, 'ruta' => $rutaCompleta]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>
