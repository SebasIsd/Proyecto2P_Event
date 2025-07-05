<?php
require_once '../conexion/conexion.php';

$conn = CConexion::ConexionBD();
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "
SELECT 
    i.ID_INS,
    e.ID_EVE_CUR,
    e.TIT_EVE_CUR AS tit_eve_cur,
    e.MOD_EVE_CUR AS mod_eve_cur,
    COALESCE(u.NOM_PRI_USU,'') || ' ' || COALESCE(u.NOM_SEG_USU,'') || ' ' || COALESCE(u.APE_PRI_USU,'') || ' ' || COALESCE(u.APE_SEG_USU,'') AS nombre_completo,
    i.EST_PAG_INS,
    n.NOT_FIN_NOT_ASI,
    n.PORC_ASI_NOT_ASI,
    (
        SELECT COUNT(*)
        FROM EVENTOS_REQUISITOS er
        WHERE er.ID_EVE_CUR = e.ID_EVE_CUR
    ) AS total_req,
    (
        SELECT COUNT(*)
        FROM EVENTOS_REQUISITOS er
        JOIN REQUISITOS r ON r.ID_REQ = er.ID_REQ
        LEFT JOIN EVIDENCIAS_REQUISITOS ev
            ON ev.ID_REQ = er.ID_REQ AND ev.ID_INS = i.ID_INS
        WHERE er.ID_EVE_CUR = e.ID_EVE_CUR
          AND (
              (r.NOM_REQ = 'Nota mínima' AND CAST(er.VALOR_REQ AS DECIMAL) <= COALESCE(n.NOT_FIN_NOT_ASI, 0)) OR
              (r.NOM_REQ = 'Asistencia mínima' AND CAST(er.VALOR_REQ AS INT) <= COALESCE(n.PORC_ASI_NOT_ASI, 0)) OR
              (r.NOM_REQ NOT IN ('Nota mínima', 'Asistencia mínima') AND ev.ESTADO_VALIDACION = 'Aprobado')
          )
    ) AS cumplidos
FROM INSCRIPCIONES i
JOIN USUARIOS u ON u.CED_USU = i.CED_USU
JOIN EVENTOS_CURSOS e ON e.ID_EVE_CUR = i.ID_EVE_CUR
LEFT JOIN NOTAS_ASISTENCIAS n ON n.ID_INS = i.ID_INS
WHERE
    (
        (e.MOD_EVE_CUR = 'Pagado' AND i.EST_PAG_INS = 'Pagado')
        OR e.MOD_EVE_CUR = 'Gratis'
    )
";

$stmt = $conn->query($sql);
$aptos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Emisión de Certificados</title>
  <link rel="stylesheet" href="../styles/css/style.css" />
  <link rel="stylesheet" href="../styles/css/certificados.css" />
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
  />
  <style>

    .btn-generar {
      background-color: #28a745;
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-generar:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>
  <header>
    <h1>Gestión de Certificados</h1>
    <nav>
      <ul>
        <li><a href="admin.html"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="emitirCertificados.php" class="active"><i class="fas fa-certificate"></i> Certificados</a></li>
        <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
        <li><a href="../usuarios/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
      </ul>
    </nav>
  </header>

  <main>
    <h2>Participantes Aptos para Certificación</h2>

    <table>
      <thead>
        <tr>
          <th>Participante</th>
          <th>Evento</th>
          <th>Modalidad</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($aptos as $a):
          $totalReq = (int)($a['total_req'] ?? 0);
          $cumplidos = (int)($a['cumplidos'] ?? 0);
          if ($totalReq === $cumplidos):
            $nombre = htmlspecialchars($a['nombre_completo'] ?? '');
            $evento = htmlspecialchars($a['tit_eve_cur'] ?? '');
            $modalidad = htmlspecialchars($a['mod_eve_cur'] ?? '');
            $idIns = (int)($a['id_ins'] ?? 0);
        ?>
        <tr>
          <td><?= $nombre ?></td>
          <td><?= $evento ?></td>
          <td><?= $modalidad ?></td>
          <td>
            <form method="post" action="generarCertificado.php" target="_blank">
              <input type="hidden" name="id_ins" value="<?= $idIns ?>">
              <button type="submit" class="btn-generar">Generar</button>
            </form>
          </td>
        </tr>
        <?php
          endif;
        endforeach; ?>
      </tbody>
    </table>
  </main>

  <footer>
    <p>&copy; <?= date("Y") ?> Sistema de Inscripciones - Todos los derechos reservados.</p>
  </footer>
</body>
</html>
