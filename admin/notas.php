<?php
require_once '../conexion/conexion.php';
$conn = CConexion::ConexionBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // FINALIZAR MASIVO
    if (isset($input['finalizar']) && isset($input['idEvento'])) {
        $idEvento = $input['idEvento'];
        try {
            $stmt = $conn->prepare("
                UPDATE NOTAS_ASISTENCIAS 
                SET FINALIZADO = TRUE 
                WHERE ID_INS IN (
                    SELECT ID_INS FROM INSCRIPCIONES WHERE ID_EVE_CUR = :idEvento
                )
            ");
            $stmt->execute([':idEvento' => $idEvento]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // GUARDAR NOTA Y ASISTENCIA
    $id_ins = $input['id_ins'];
    $nota = $input['nota'] ?? null;
    $porcentaje = $input['porcentaje'] ?? null;

    try {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM NOTAS_ASISTENCIAS WHERE ID_INS = :id");
        $stmt->execute([':id' => $id_ins]);
        $existe = $stmt->fetchColumn();

        if ($existe) {
            $stmt = $conn->prepare("UPDATE NOTAS_ASISTENCIAS SET NOT_FIN_NOT_ASI = :nota, PORC_ASI_NOT_ASI = :porc WHERE ID_INS = :id");
        } else {
            $stmt = $conn->prepare("INSERT INTO NOTAS_ASISTENCIAS (ID_INS, NOT_FIN_NOT_ASI, PORC_ASI_NOT_ASI) VALUES (:id, :nota, :porc)");
        }

        $stmt->execute([
            ':id' => $id_ins,
            ':nota' => $nota,
            ':porc' => $porcentaje
        ]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;

}

// Obtener eventos
$eventos = $conn->query("SELECT ID_EVE_CUR, TIT_EVE_CUR FROM EVENTOS_CURSOS ORDER BY FEC_INI_EVE_CUR DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Notas y Asistencias</title>
  <link rel="stylesheet" href="../styles/css/style.css">
  <link rel="stylesheet" href="../styles/css/estilosNotas.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    main.contenido { position: relative; padding-top: 50px; }
    #btnFinalizar {
      padding: 10px 20px;
      background-color: #6c1313;
      border: none;
      border-radius: 5px;
      color: white;
      font-weight: 600;
      cursor: pointer;
    }
    #btnFinalizar:hover {
      background-color: #a86e2e;
    }
  </style>
</head>
<body>
<header>
  <div class="container">
    <div class="logo"><h1>Notas y <span>Asistencias</span></h1></div>
    <nav>
      <ul>
        <li><a href="../admin/admin.html"><i class="fas fa-home"></i> Inicio</a></li>
        <li><a href="../admin/notasAsistencia.html" class="active"><i class="fas fa-pen-alt"></i> Notas y asistencia</a></li>
        <li><a href="perfil.php"><i class="fas fa-user-circle"></i> Perfil</a></li>
        <li><a href="../usuarios/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
      </ul>
    </nav>
  </div>
</header>

<main class="contenido">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <div>
      <h2>Selecciona un Evento</h2>
      <select id="evento">
        <option value="">-- Seleccione --</option>
        <?php foreach ($eventos as $ev): ?>
          <option value="<?= $ev['id_eve_cur'] ?>"><?= htmlspecialchars($ev['tit_eve_cur']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <button id="btnFinalizar">Finalizar</button>
  </div>

  <div id="tabla-container">
    <table>
      <thead>
        <tr>
          <th>Participante</th>
          <th id="col-nota">Nota Final</th>
          <th id="col-asistencia">Asistencia</th>
          <th>Acción</th>
        </tr>
      </thead>
      <tbody id="tabla-cuerpo"></tbody>
    </table>
  </div>
</main>

<footer>
  <div class="container">
    <div class="footer-content">
      <div class="footer-section">
        <h3><i class="fas fa-info-circle"></i> Sobre el Sistema</h3>
        <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
      </div>
      <div class="footer-section">
        <h3><i class="fas fa-envelope"></i> Contacto</h3>
        <p><i class="fas fa-map-marker-alt"></i> Av. Principal 123, Ciudad</p>
        <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p>
        <p><i class="fas fa-phone"></i> +123 456 7890</p>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2023 Sistema de Inscripciones. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<script>
  const eventoSelect = document.getElementById("evento");
  const cuerpo = document.getElementById("tabla-cuerpo");
  const colNota = document.getElementById("col-nota");
  const colAsistencia = document.getElementById("col-asistencia");
  let eventoIdSeleccionado = null;

  async function cargarParticipantes(eventoId) {
    eventoIdSeleccionado = eventoId;

    const res1 = await fetch(`../admin/obtenerInscritosPorEvento.php?idEvento=${eventoId}`);
    const participantes = await res1.json();

    const res2 = await fetch(`../admin/obtenerRequisitosPorEvento.php?idEvento=${eventoId}`);
    const requisitos = await res2.json();

    const mostrarNota = requisitos.some(r => r.nom_req.toLowerCase().includes("nota"));
    const mostrarAsistencia = requisitos.some(r => r.nom_req.toLowerCase().includes("asistencia"));

    colNota.style.display = "";
    colAsistencia.style.display = "";

    cuerpo.innerHTML = "";
    if (participantes.length === 0) {
      cuerpo.innerHTML = "<tr><td colspan='4'><b>No hay participantes</b></td></tr>";
      return;
    }

    participantes.forEach(p => {
      const fila = document.createElement("tr");
      fila.innerHTML = `
        <td>${p.nombre_completo}</td>
        <td>
          ${mostrarNota
            ? `<input type="number" min="1" max="10" step="0.1" value="${p.not_fin_not_asi ?? ""}" data-id="${p.id_ins}" class="nota">`
            : `<span style="color: gray;">No requiere nota</span>`}
        </td>
        <td>
          ${mostrarAsistencia
            ? `<input type="number" min="1" max="100" step="1" value="${p.porc_asi_not_asi ?? ""}" data-id="${p.id_ins}" class="porc">`
            : `<span style="color: gray;">No requiere asistencia</span>`}
        </td>
        <td><button onclick="guardar(${p.id_ins}, ${mostrarNota}, ${mostrarAsistencia}, this)">Guardar</button></td>
      `;
      cuerpo.appendChild(fila);
    });
  }

  async function guardar(id, requiereNota, requiereAsistencia, btn) {
    btn.disabled = true;
    const notaInput = document.querySelector(`input.nota[data-id='${id}']`);
    const porcInput = document.querySelector(`input.porc[data-id='${id}']`);
    const nota = notaInput ? parseFloat(notaInput.value) : null;
    const porcentaje = porcInput ? parseInt(porcInput.value) : null;

    if (requiereNota && (nota === null || isNaN(nota) || nota < 1 || nota > 10)) {
      alert("La nota debe estar entre 1 y 10.");
      btn.disabled = false;
      return;
    }

    if (requiereAsistencia && (porcentaje === null || isNaN(porcentaje) || porcentaje < 1 || porcentaje > 100)) {
      alert("La asistencia debe estar entre 1% y 100%.");
      btn.disabled = false;
      return;
    }

    try {
      const res = await fetch("", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ id_ins: id, nota, porcentaje })
      });
      const result = await res.json();
      alert(result.success ? "Guardado correctamente" : "Error: " + result.error);
    } catch (err) {
      alert("Error inesperado al guardar.");
    }
    btn.disabled = false;
  }

  eventoSelect.addEventListener("change", () => {
    const id = eventoSelect.value;
    if (id) cargarParticipantes(id);
  });


  document.getElementById("btnFinalizar").addEventListener("click", async () => {
  const eventoId = eventoSelect.value;
  if (!eventoId) {
    alert("Seleccione un evento.");
    return;
  }

  const confirmacion = confirm("¿Estás seguro de finalizar? Esto bloqueará el ingreso de notas y asistencia.");
  if (!confirmacion) return;

  try {
    const res = await fetch("", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ finalizar: true, idEvento: eventoId })
    });

    const data = await res.json();
    if (data.success) {
      alert("Evento finalizado correctamente.");
      cuerpo.innerHTML = "<tr><td colspan='4'><b>No hay participantes</b></td></tr>";
    } else {
      alert("Error al finalizar: " + data.error);
    }
  } catch (e) {
    console.error("Error:", e);
    alert("Error al finalizar el evento.");
  }
});

</script>
</body>
</html>
