<?php
require_once '../conexion/conexion.php';
require_once '../fpdf186/fpdf.php';  // Ajusta la ruta según tu estructura

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ins'])) {
    $id_ins = (int) $_POST['id_ins'];

    $conn = CConexion::ConexionBD();
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener datos
    $sql = "
    SELECT 
        i.ID_INS,
        u.CED_USU,
        COALESCE(u.NOM_PRI_USU,'') || ' ' || COALESCE(u.NOM_SEG_USU,'') || ' ' || COALESCE(u.APE_PRI_USU,'') || ' ' || COALESCE(u.APE_SEG_USU,'') AS nombre_completo,
        e.TIT_EVE_CUR,
        e.FEC_FIN_EVE_CUR
    FROM INSCRIPCIONES i
    JOIN USUARIOS u ON u.CED_USU = i.CED_USU
    JOIN EVENTOS_CURSOS e ON e.ID_EVE_CUR = i.ID_EVE_CUR
    WHERE i.ID_INS = :id_ins
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':id_ins' => $id_ins]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Inscripción no encontrada.");
    }

    // Crear PDF
    $pdf = new FPDF('P','mm','A4');
    $pdf->AddPage();

    // --- Logo ---
    $logoPath = '../assets/logo.png';  // Ajusta ruta a tu logo
    if (file_exists($logoPath)) {
        $pdf->Image($logoPath, 15, 10, 40); // x=15mm, y=10mm, ancho=40mm
    }

    // --- Título ---
    $pdf->SetTextColor(31, 97, 141); // Azul oscuro
    $pdf->SetFont('Arial', 'B', 28);
    $pdf->Cell(0, 15, utf8_decode('Certificado de Participación'), 0, 1, 'C');
    $pdf->Ln(10);

    // --- Línea decorativa ---
    $pdf->SetDrawColor(31, 97, 141);
    $pdf->SetLineWidth(1);
    $pdf->Line(40, $pdf->GetY(), 170, $pdf->GetY());
    $pdf->Ln(15);

    // --- Cuerpo ---
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFont('Arial', '', 16);
    $pdf->MultiCell(0, 10, utf8_decode("Por medio del presente se certifica que:"), 0, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 12, utf8_decode($data['nombre_completo']), 0, 1, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', '', 16);
    $pdf->MultiCell(0, 10, utf8_decode("Ha participado satisfactoriamente en el evento:"), 0, 'C');
    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 20);
    $pdf->Cell(0, 12, utf8_decode($data['tit_eve_cur']), 0, 1, 'C');
    $pdf->Ln(8);

    $fechaFin = date('d/m/Y', strtotime($data['fec_fin_eve_cur']));
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 10, "Fecha de finalización: $fechaFin", 0, 1, 'C');

    // --- Firma ---
    $pdf->Ln(30);
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(80, 0, '', 0, 0); // Espacio a la izquierda para centrar firma
    $pdf->Cell(50, 10, '_________________________', 0, 1, 'C');
    $pdf->Cell(80, 0, '', 0, 0);
    $pdf->Cell(50, 10, 'Firma del Responsable', 0, 1, 'C');
    $pdf->Cell(80, 0, '', 0, 0);
    $pdf->Cell(50, 10, 'Director Académico', 0, 1, 'C');

    // Guardar PDF en servidor
    $dirCertificados = '../certificados/';
    if (!is_dir($dirCertificados)) {
        mkdir($dirCertificados, 0777, true);
    }
    $filename = "certificado_" . $id_ins . "_" . time() . ".pdf";
    $rutaArchivo = $dirCertificados . $filename;
    $pdf->Output('F', $rutaArchivo);

    // Guardar registro en la BD
    $htmlGenerado = "<h1>Certificado de Participación</h1>
    <p>Participante: {$data['nombre_completo']}</p>
    <p>Evento: {$data['tit_eve_cur']}</p>
    <p>Fecha de finalización: $fechaFin</p>";

    $insertSql = "
    INSERT INTO CERTIFICADOS (ID_INS, FEC_EMI_CER, HTML_GENERADO)
    VALUES (:id_ins, CURRENT_DATE, :html)
    ";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->execute([
        ':id_ins' => $id_ins,
        ':html' => $htmlGenerado
    ]);

    // Mostrar PDF en navegador
    $pdf->Output('I', $filename);

} else {
    die("Solicitud inválida.");
}

?>