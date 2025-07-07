<?php
require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id_ins = $data['id_ins'];
    $id_req = $data['id_req'];
    $estado = $data['estado'];
    $observacion = $data['observacion'] ?? '';

    $stmt = $conn->prepare("UPDATE EVIDENCIAS_REQUISITOS SET ESTADO_VALIDACION = :estado, OBSERVACION = :obs WHERE ID_INS = :id_ins AND ID_REQ = :id_req");
    $stmt->execute([
        ':estado' => $estado,
        ':obs' => $observacion,
        ':id_ins' => $id_ins,
        ':id_req' => $id_req
    ]);

    echo json_encode(['success' => true]);
    exit;
}

$eventos = $conn->query("SELECT ID_EVE_CUR, TIT_EVE_CUR FROM EVENTOS_CURSOS ORDER BY FEC_INI_EVE_CUR DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Validar Requisitos</title>
  <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="stylesheet" href="../styles/css/estilosNotas.css">
  <link rel="stylesheet" href="../styles/css/componente.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <style>
    .btn-ver {
      background-color: #2980b9;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      cursor: pointer;
    }
    .btn-ver:hover {
      background-color: #1f618d;
    }
  </style>
</head>
<body>
<?php include '../includes/headeradmin.php'?>

<main class="contenido">
  <select id="evento">
    <option value="">-- Seleccione un evento--</option>
    <?php foreach ($eventos as $e): ?>
      <option value="<?= $e['id_eve_cur'] ?>"><?= htmlspecialchars($e['tit_eve_cur']) ?></option>
    <?php endforeach; ?>
  </select>

  <div id="tabla-container">
    <table>
      <thead>
        <tr>
          <th>Participante</th>
          <th>Requisito</th>
          <th>Ver Evidencia</th>
          <th>Estado</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody id="tabla-cuerpo">
      </tbody>
    </table>
  </div>
</main>
<?php include '../includes/footeradmin.php'?>
<script>
let rechazo_id_ins = null;
let rechazo_id_req = null;

document.getElementById('evento').addEventListener('change', async function () {
  const eventoId = this.value;
  const cuerpo = document.getElementById('tabla-cuerpo');
  cuerpo.innerHTML = '';

  if (!eventoId) return;

  const res = await fetch(`../admin/evidenciasReq.php?idEvento=${eventoId}`);
  const datos = await res.json();

  if (!datos || datos.length === 0) {
    cuerpo.innerHTML = `<tr><td colspan="5"><b>No hay requisitos por validar</b></td></tr>`;
    return;
  }

  datos.forEach(item => {
    const fila = document.createElement('tr');
    fila.innerHTML = `
      <td>${item.nombre_completo}</td>
      <td>${item.nom_req}</td>
      <td>
      <button class="btn-ver" onclick="verPDF(${item.id_ins}, ${item.id_req})">Ver PDF</button>
      </td>
      <td>${item.estado_validacion ?? 'Pendiente'}</td>
      <td>
        <button onclick="validar(${item.id_ins}, ${item.id_req})" class="btn btn-success btn-sm">Aceptar</button>
        <button onclick="rechazar(${item.id_ins}, ${item.id_req})" class="btn btn-danger btn-sm">Rechazar</button>
      </td>
    `;
    cuerpo.appendChild(fila);
  });
});

function verPDF(id_ins, id_req) {
  window.open(`verPDF.php?id_ins=${id_ins}&id_req=${id_req}`, '_blank');
}


async function validar(id_ins, id_req) {
  const res = await fetch('', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id_ins, id_req, estado: 'Aceptado' })
  });

  const result = await res.json();
  if (result.success) {
    alert('Requisito aceptado');
    document.getElementById('evento').dispatchEvent(new Event('change'));
  } else {
    alert('Error al guardar');
  }
}

function rechazar(id_ins, id_req) {
  rechazo_id_ins = id_ins;
  rechazo_id_req = id_req;
  document.getElementById('observacion-input').value = '';

  // Modal se inicializa justo aquí
  const modal = new bootstrap.Modal(document.getElementById('modalRechazo'));
  modal.show();
}

async function confirmarRechazo() {
  const obs = document.getElementById('observacion-input').value.trim();
  if (!obs) {
    alert("La observación es obligatoria.");
    return;
  }

  const res = await fetch('', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
      id_ins: rechazo_id_ins,
      id_req: rechazo_id_req,
      estado: 'Rechazado',
      observacion: obs
    })
  });

  const result = await res.json();
  if (result.success) {
    const modalElement = bootstrap.Modal.getInstance(document.getElementById('modalRechazo'));
    modalElement.hide();
    alert('Requisito rechazado');
    document.getElementById('evento').dispatchEvent(new Event('change'));
  } else {
    alert('Error al guardar el rechazo');
  }
}
</script>

<!-- Modal Bootstrap -->
<div class="modal fade" id="modalRechazo" tabindex="-1" aria-labelledby="modalRechazoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="modalRechazoLabel">Observación del Rechazo</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <textarea id="observacion-input" class="form-control" rows="4" placeholder="Escribe la observación..."></textarea>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-danger" onclick="confirmarRechazo()">Rechazar</button>
      </div>
    </div>
  </div>
</div>
 
</body>
</html>