<?php
require_once '../conexion/conexion.php';
require_once '../fpdf186/fpdf.php';

$conn = CConexion::ConexionBD();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// --- GENERAR CERTIFICADO ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_ins'])) {
    $id_ins = (int) $_POST['id_ins'];

    $stmt = $conn->prepare("SELECT ID_CER FROM CERTIFICADOS WHERE ID_INS = :id");
    $stmt->execute([':id' => $id_ins]);
    if ($stmt->fetchColumn()) {
        exit("Este certificado ya fue generado.");
    }

    // Obtener datos para el certificado
    $stmt = $conn->prepare("
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
    ");
    $stmt->execute([':id_ins' => $id_ins]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$data) exit("Inscripción no encontrada.");

    $pdf = new FPDF('L','mm','A4');
    $pdf->AddPage();

    $logoPath = '../images/logo.jpg';
    if (file_exists($logoPath)) $pdf->Image($logoPath, 20, 15, 30);

    $pdf->SetXY(0, 20); // Establece posición Y para el título
    $pdf->SetTextColor(31, 97, 141);
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->Cell(0, 20, utf8_decode('UNIVERDIDAD TÉCNICA DE AMBATO'), 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetDrawColor(31, 97, 141);
    $pdf->SetLineWidth(1);
    $pdf->Line(50, $pdf->GetY(), 250, $pdf->GetY());
    $pdf->Ln(15);

    $pdf->SetTextColor(0);
    $pdf->SetFont('Arial', '', 18);
    $pdf->MultiCell(0, 10, utf8_decode("Por medio del presente se certifica que:"), 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 26);
    $pdf->Cell(0, 12, utf8_decode($data['nombre_completo']), 0, 1, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', '', 18);
    $pdf->MultiCell(0, 10, utf8_decode("Ha participado satisfactoriamente en el evento:"), 0, 'C');
    $pdf->Ln(5);

    $pdf->SetFont('Arial', 'B', 22);
    $pdf->Cell(0, 12, utf8_decode($data['tit_eve_cur']), 0, 1, 'C');
    $pdf->Ln(5);

    $fechaFin = date('d/m/Y', strtotime($data['fec_fin_eve_cur']));
    $pdf->SetFont('Arial', '', 16);
    $pdf->Cell(0, 10,  utf8_decode("Fecha de finalización: $fechaFin"), 0, 1, 'C');

    $pdf->Ln(30);
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(120, 0, '', 0, 0);
    $pdf->Cell(50, 10, '_________________________', 0, 1, 'C');
    $pdf->Cell(120, 0, '', 0, 0);
    $pdf->Cell(50, 10, utf8_decode("Director Académico"), 0, 1, 'C');

    $dirCertificados = '../certificados/';
    if (!is_dir($dirCertificados)) mkdir($dirCertificados, 0777, true);
    $filename = "certificado_" . $id_ins . "_" . time() . ".pdf";
    $rutaRelativa = "certificados/" . $filename;
    $rutaCompleta = "../" . $rutaRelativa;

    $pdf->Output('F', $rutaCompleta);

    $conn->prepare("
        INSERT INTO CERTIFICADOS (ID_INS, FEC_EMI_CER, HTML_GENERADO)
        VALUES (:id_ins, CURRENT_DATE, :ruta)
    ")->execute([
        ':id_ins' => $id_ins,
        ':ruta' => $rutaRelativa
    ]);
    header("Content-type: application/pdf");
    readfile($rutaCompleta);
    exit;
}

// Obtener eventos
$eventos = $conn->query("SELECT ID_EVE_CUR, TIT_EVE_CUR FROM EVENTOS_CURSOS ORDER BY FEC_INI_EVE_CUR DESC")->fetchAll(PDO::FETCH_ASSOC);
$eventoSeleccionado = $_GET['evento'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Certificados</title>
    <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="stylesheet" href="../styles/css/estilosNotas.css">
  <link rel="stylesheet" href="../styles/css/componente.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  
<style>
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    table-layout: fixed;
  margin-bottom: 60px; /* Añade separación entre tabla y footer */

  }

  th, td {
    padding: 12px;
    border: 1px solid #ccc;
    text-align: center;
    width: 33.33%;
    word-wrap: break-word;
  }

  th {
    background-color: #6c1313;
    color: white;
    font-weight: bold;
  }

  .btn, button[type="submit"] {
    background-color:rgb(113, 176, 187);
    color: white;
    border: none;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
  }

  .btn:hover, button[type="submit"]:hover {
    background-color:rgb(142, 203, 211);
  }
</style>

</head>
<body>
<header class="main-header">
  
  <div class="logo-nombre">
    <h1>Bienvenido, Administrador 👋</h1>
  </div>
  <nav>
    <ul>
      <li><a href="admin.php"><i class="fas fa-home"></i> Inicio</a></li>
      <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
    </ul>
  </nav>
</header>
 
  <form method="get">
</br>
    <select name="evento" id="evento"  onchange="this.form.submit()">
      <option value="" <?= $eventoSeleccionado === '' ? 'selected' : '' ?>>-- Seleccione un evento --</option>
      <?php foreach ($eventos as $e): ?>
        <option value="<?= $e['id_eve_cur'] ?>" <?= $eventoSeleccionado == $e['id_eve_cur'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($e['tit_eve_cur']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </form>
</br>
<?php
if ($eventoSeleccionado !== '') {
    $stmt = $conn->prepare("
        SELECT 
            i.ID_INS,
            u.NOM_PRI_USU || ' ' || u.NOM_SEG_USU || ' ' || u.APE_PRI_USU || ' ' || u.APE_SEG_USU AS nombre,
            e.TIT_EVE_CUR,
            (
                SELECT COUNT(*) FROM EVENTOS_REQUISITOS er WHERE er.ID_EVE_CUR = e.ID_EVE_CUR
            ) AS total_req,
            (
                SELECT COUNT(*) FROM EVENTOS_REQUISITOS er
                JOIN REQUISITOS r ON r.ID_REQ = er.ID_REQ
                LEFT JOIN EVIDENCIAS_REQUISITOS ev ON ev.ID_REQ = er.ID_REQ AND ev.ID_INS = i.ID_INS
                LEFT JOIN NOTAS_ASISTENCIAS n ON n.ID_INS = i.ID_INS
                WHERE er.ID_EVE_CUR = e.ID_EVE_CUR
                  AND (
                      (r.NOM_REQ = 'Nota mínima' AND CAST(er.VALOR_REQ AS DECIMAL) <= COALESCE(n.NOT_FIN_NOT_ASI, 0)) OR
                      (r.NOM_REQ = 'Asistencia mínima' AND CAST(er.VALOR_REQ AS INT) <= COALESCE(n.PORC_ASI_NOT_ASI, 0)) OR
                      (r.NOM_REQ NOT IN ('Nota mínima', 'Asistencia mínima') AND ev.ESTADO_VALIDACION = 'Aprobado')
                  )
            ) AS cumplidos,
            (
                SELECT c.ID_CER FROM CERTIFICADOS c WHERE c.ID_INS = i.ID_INS
            ) AS ya_tiene
        FROM INSCRIPCIONES i
        JOIN USUARIOS u ON u.CED_USU = i.CED_USU
        JOIN EVENTOS_CURSOS e ON e.ID_EVE_CUR = i.ID_EVE_CUR
        WHERE e.ID_EVE_CUR = :id
          AND ((e.MOD_EVE_CUR = 'Pagado' AND i.EST_PAG_INS = 'Pagado') OR e.MOD_EVE_CUR = 'Gratis')
    ");
    $stmt->execute([':id' => $eventoSeleccionado]);
    $participantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Participantes aptos</h2>";

    $hayAptos = false;
    foreach ($participantes as $p) {
        if ($p['total_req'] == $p['cumplidos']) {
            $hayAptos = true;
            break;
        }
    }

    if (!$hayAptos) {
        echo "<p><strong>No hay participantes aptos.</strong></p>";
    } else {
        echo "<table><tr><th>Nombre</th><th>Evento</th><th>Acción</th></tr>";
        foreach ($participantes as $p) {
            if ($p['total_req'] == $p['cumplidos']) {
                echo "<tr>
                    <td>" . htmlspecialchars($p['nombre']) . "</td>
                    <td>" . htmlspecialchars($p['tit_eve_cur']) . "</td>
                    <td>";
                if ($p['ya_tiene']) {
$stmtRuta = $conn->prepare("SELECT HTML_GENERADO FROM CERTIFICADOS WHERE ID_INS = :id_ins");
$stmtRuta->execute([':id_ins' => $p['id_ins']]);
$ruta = $stmtRuta->fetchColumn();

if ($ruta) {
    echo "<a href='../$ruta' target='_blank'><button class='btn'>Ver PDF</button></a>";
}                } else {
                    echo "<form method='POST' style='display:inline'>
                            <input type='hidden' name='id_ins' value='{$p['id_ins']}'>
                            <button type='submit'>Generar</button>
                          </form>";
                }
                echo "</td></tr>";
            }
        }
        echo "</table>";
    }
}
?>
 <?php include '../includes/footeradmin.php'?>


</body>
</html>