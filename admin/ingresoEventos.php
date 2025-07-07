<?php
require_once '../conexion/conexion.php'; 
$conn = CConexion::ConexionBD();

if (!$conn) {
    die("No se pudo establecer la conexión a la base de datos.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $modalidad = $_POST['modalidad'];
    $costo = floatval($_POST['costo'] ?? 0);
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $nuevoTipo = $_POST['nuevoTipo'] ?? null;
    $idTipo = $_POST['idTipo'] ?? null;
    $carreras = $_POST['carreras'] ?? [];
    $requisitos = $_POST['requisitos'] ?? [];
    $nuevoTipo = $_POST['nuevoTipo'] ?? null;
  $nuevoRequisito = $_POST['nuevoRequisito'] ?? null;
$idRequisitoNuevo = null;

    try {
        $conn->beginTransaction();

        // Insertar nuevo tipo si es "Otro"
if (!empty($nuevoTipo)) {
    $rutaImagenDefault = '../images/1751340251_otro.jpg'; // Puedes cambiar esta ruta según tu proyecto

    $stmt = $conn->prepare("INSERT INTO TIPOS_EVENTO (NOM_TIPO_EVE, IMG_TIPO_EVE) 
                            VALUES (:nombre, :imagen) 
                            RETURNING ID_TIPO_EVE");
    $stmt->execute([
        ':nombre' => $nuevoTipo,
        ':imagen' => $rutaImagenDefault
    ]);
    $idTipo = $stmt->fetchColumn();
}



        // Insertar evento
        $stmt = $conn->prepare("INSERT INTO EVENTOS_CURSOS 
            (TIT_EVE_CUR, DES_EVE_CUR, FEC_INI_EVE_CUR, FEC_FIN_EVE_CUR, COS_EVE_CUR, MOD_EVE_CUR, ID_TIPO_EVE)
            VALUES (:titulo, :descripcion, :fec_ini, :fec_fin, :costo, :modalidad, :id_tipo)
            RETURNING ID_EVE_CUR");
        $stmt->execute([
            ':titulo' => $titulo,
            ':descripcion' => $descripcion,
            ':fec_ini' => $fechaInicio,
            ':fec_fin' => $fechaFin,
            ':costo' => $costo,
            ':modalidad' => $modalidad,
            ':id_tipo' => $idTipo
        ]);
        $idEvento = $stmt->fetchColumn();

        // Insertar carreras
        foreach ($carreras as $idCar) {
            $conn->prepare("INSERT INTO EVENTOS_CARRERAS (ID_EVE_CUR, ID_CAR) VALUES (:e, :c)")->execute([':e' => $idEvento, ':c' => $idCar]);
        }

        // Insertar requisitos
        foreach ($requisitos as $valor) {
            // Valor viene como "id|valor" o solo "id"
            $partes = explode('|', $valor);
            $idReq = $partes[0];
            $valTexto = $partes[1] ?? null;
            $conn->prepare("INSERT INTO EVENTOS_REQUISITOS (ID_EVE_CUR, ID_REQ, VALOR_REQ) VALUES (:e, :r, :v)")
                 ->execute([':e' => $idEvento, ':r' => $idReq, ':v' => $valTexto]);
        }

        

        $conn->commit();
        echo "<script>alert('Evento guardado correctamente'); window.location.href='ingresoEventos.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Error al guardar: " . $e->getMessage() . "');</script>";
    }
}

function getCatalogo($conn, $tabla, $id, $nombre) {
    $stmt = $conn->query("SELECT $id AS id, $nombre AS nombre FROM $tabla ORDER BY nombre");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$tipos = getCatalogo($conn, 'TIPOS_EVENTO', 'ID_TIPO_EVE', 'NOM_TIPO_EVE');
$carreras = getCatalogo($conn, 'CARRERAS', 'ID_CAR', 'NOM_CAR');
$requisitos = getCatalogo($conn, 'REQUISITOS', 'ID_REQ', 'NOM_REQ');
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <title>Crear Evento Académico</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    :root {
    --primary-color: #6c1313;
    --secondary-color: #000000;
    --light-gray: #696969;
    --white: #ffffff;
    --hover-color: #5a0f0f;
    --secondary-btn-color: #4a4a4a;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    --card-hover-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}
header {
    background-color: var(--primary-color);
    color: var(--white);
    padding: 1rem 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.logo h1 {
    font-weight: 700;
    font-size: 1.8rem;
}

.logo span {
    font-weight: 400;
}

nav ul {
    display: flex;
    list-style: none;
    margin-top: 1rem;
    flex-wrap: wrap;
}

nav ul li a {
    color: var(--white);
    text-decoration: none;
    padding: 0.5rem 1rem;
    margin-right: 0.5rem;
    border-radius: 4px;
    transition: background-color 0.3s;
    display: flex;
    align-items: center;
}

nav ul li a i {
    margin-right: 8px;
}

nav ul li a:hover, nav ul li a.active {
    background-color: var(--hover-color);
}

    footer {
    background-color: var(--secondary-color);
    color: var(--white);
    padding: 2rem 0 0;
    margin-top: auto;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-section h3 {
    margin-bottom: 1rem;
    color: var(--white);
    display: flex;
    align-items: center;
}

.footer-section h3 i {
    margin-right: 10px;
    color: var(--primary-color);
}

.footer-section p, .footer-section a {
    color: #cccccc;
    margin-bottom: 0.5rem;
    display: block;
    text-decoration: none;
}

.footer-section a:hover {
    color: var(--white);
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section ul li a {
    display: flex;
    align-items: center;
}

.footer-section ul li a i {
    margin-right: 5px;
    font-size: 0.8rem;
}

.social-icons {
    display: flex;
    margin-top: 1rem;
    gap: 10px;
}

.social-icons a {
    color: #cccccc;
    font-size: 1.2rem;
    transition: color 0.3s;
}

.social-icons a:hover {
    color: var(--primary-color);
}

.footer-bottom {
    text-align: center;
    padding: 1rem 0;
    border-top: 1px solid var(--light-gray);
    color: #cccccc;
    font-size: 0.9rem;
}



    body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
    .formulario-container { background: #fff; padding: 25px; border-radius: 10px; max-width: 800px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    fieldset { border: 1px solid #ccc; margin-bottom: 20px; padding: 20px; border-radius: 8px; }
    legend { font-weight: bold; color: #8b0000; font-size: 1.1em; }
    label { margin-top: 10px; display: block; font-weight: bold; }
    input[type="text"], input[type="date"], input[type="number"],select, textarea {
      width: 100%; padding: 8px; border-radius: 5px; border: 2px solid #ccc; margin-top: 10px;
    }
    .checkbox-group label { display: flex; align-items: center; margin-top: 8px; background: #f8f8f8; padding: 5px; border-radius: 5px; }
    .boton-container { text-align: center; margin-top: 20px; }
    .boton-container button {
      padding: 10px 20px; background: #8b0000; color: white; border: none; border-radius: 5px; cursor: pointer;
    }
    .boton-container button:hover { background: #6b0000; }
  </style>
</head>
<body>
  <header>
    <div class="container">
      <h1 style="text-align: center; padding: 20px;"> <span>Ingreso de eventos</span></h1>
      </div>
      <nav>
        <ul>
          <li><a href="../admin/admin.php"><i class="fas fa-home"></i> Inicio</a></li>
          <li><a href="../admin/ingresoEventos.php" class="active"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
 <li><a href="../admin/eventos.php"><i class="fas fa-eye"></i> Ver Eventos</a></li>
          <li class="profile-link">
                        <a href="..admin/perfil.php" ><i class="fas fa-user-circle"></i> Perfil</a>
                    </li>
                    <li><a href="../../usuarios/logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                
        </ul>
      </nav>
    </div>
  </header>
  <div class="formulario-container">
    <form method="POST" id="eventoForm">
      <fieldset>
        <legend>Información General</legend>
        <label for="titulo">Título</label>
        <input type="text" name="titulo" id="titulo" required />
        <label for="descripcion">Descripción</label>
        <textarea name="descripcion" id="descripcion" rows="3" required></textarea>
        <label for="modalidad">Modalidad</label>
        <select name="modalidad" id="modalidad" required>
          <option value="">Seleccione</option>
          <option value="Gratis">Gratis</option>
          <option value="Pagado">Pagado</option>
        </select>
        <label for="costo">Costo</label>
        <input type="text" name="costo" id="costo" required />
      </fieldset>

      <fieldset>
        <legend>Tipo de Evento</legend>
        <div class="checkbox-group" id="tiposEventoContainer">
          <?php foreach ($tipos as $tipo): ?>
            <label>
              <input type="radio" name="tipoEvento" value="<?= strtolower($tipo['nombre']) ?>" data-id="<?= $tipo['id'] ?>">
              <?= htmlspecialchars($tipo['nombre']) ?>
            </label>
          <?php endforeach; ?>
          <label><input type="radio" name="tipoEvento" value="otros"> Otro</label>
        </div>
        <input type="text" name="nuevoTipo" id="nuevoTipoEspecifico" placeholder="Especifique otro tipo" disabled />
      </fieldset>

      <fieldset>
        <legend>Fechas</legend>
        <label for="fechaInicio">Fecha Inicio</label>
        <input type="date" name="fechaInicio" id="fechaInicio" required />
        <label for="fechaFin">Fecha Fin</label>
        <input type="date" name="fechaFin" id="fechaFin" required />
      </fieldset>

      <fieldset>
        <legend>Carreras Participantes</legend>
        <div class="checkbox-group" id="carrerasContainer">
          <?php foreach ($carreras as $car): ?>
            <label><input type="checkbox" name="carreras[]" value="<?= $car['id'] ?>"> <?= htmlspecialchars($car['nombre']) ?></label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset>
        <legend>Requisitos del Evento</legend>
        <div class="checkbox-group" id="requisitosContainer"></div>
      <label>
  <input type="checkbox" id="checkOtroRequisito" />
  Otro requisito
</label>
<input type="text" id="inputOtroRequisito" name="nuevoRequisito" placeholder="Especifique otro requisito" disabled />

      </fieldset>

      <div class="boton-container">
        <button type="submit">GUARDAR</button>
      </div>
    </form>
  </div>
<footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-section">
          <h3><i class="fas fa-info-circle"></i> Sobre el Sistema</h3>
          <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
        </div>
        <div class="footer-section">
          <h3><i class="fas fa-envelope"></i> Contacto</h3>
          <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p>
          <p><i class="fas fa-phone"></i> +123 456 7890</p>
        </div>
        <div class="footer-section">
          <h3><i class="fas fa-link"></i> Enlaces Rápidos</h3>
          <ul>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Inicio</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Eventos</a></li>
            <li><a href="#"><i class="fas fa-chevron-right"></i> Políticas</a></li>
          </ul>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2023 Sistema de Inscripciones. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>
  <script>
document.addEventListener("DOMContentLoaded", () => {
  const modalidad = document.getElementById("modalidad");
  const costo = document.getElementById("costo");
  const nuevoTipo = document.getElementById("nuevoTipoEspecifico");
  const radios = document.querySelectorAll('input[name="tipoEvento"]');
  const requisitosData = <?php echo json_encode($requisitos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?>;

  function cargarCheckboxes(items, containerId, name, conInputExtra = false) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    items.forEach(item => {
      const label = document.createElement('label');
      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.name = name + '[]';
      checkbox.value = item.id;

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(' ' + item.nombre));
      container.appendChild(label);

      if (conInputExtra) {
        const lower = item.nombre.toLowerCase();
        if (lower.includes('nota')) {
          const input = document.createElement('input');
          input.type = 'number';
          input.name = `notaMinima_${item.id}`;
          input.placeholder = 'Ej. 7';
          input.disabled = true;

          checkbox.addEventListener('change', e => {
            input.disabled = !e.target.checked;
            if (!e.target.checked) input.value = '';
          });

          container.appendChild(input);
        }

        if (lower.includes('asistencia')) {
          const input = document.createElement('input');
          input.type = 'number';
          input.name = `asistenciaMinima_${item.id}`;
          input.placeholder = 'Ej. 80';
          input.disabled = true;

          checkbox.addEventListener('change', e => {
            input.disabled = !e.target.checked;
            if (!e.target.checked) input.value = '';
          });

          container.appendChild(input);
        }

        container.appendChild(document.createElement('br'));
      }
    });
  }

  cargarCheckboxes(requisitosData, 'requisitosContainer', 'requisitos', true);

  modalidad.addEventListener("change", () => {
    if (modalidad.value === "Gratis") {
      costo.value = "0.00";
      costo.disabled = true;
    } else {
      costo.value = "";
      costo.disabled = false;
    }
  });

  radios.forEach(r => {
    r.addEventListener("change", () => {
      nuevoTipo.disabled = r.value !== "otros";
    });
  });

  document.getElementById("eventoForm").addEventListener("submit", e => {
    const inicio = new Date(document.getElementById("fechaInicio").value);
    const fin = new Date(document.getElementById("fechaFin").value);
    const hoy = new Date();
    hoy.setHours(0,0,0,0);
    inicio.setHours(0,0,0,0);
    fin.setHours(0,0,0,0);

    if (inicio < hoy) {
      alert("La fecha de inicio no puede ser anterior a hoy.");
      e.preventDefault();
      return;
    }
    if (fin < inicio) {
      alert("La fecha fin no puede ser antes que la fecha inicio.");
      e.preventDefault();
      return;
    }

    const tipoSel = document.querySelector('input[name="tipoEvento"]:checked');
    if (!tipoSel) {
      alert("Debe seleccionar un tipo de evento.");
      e.preventDefault();
      return;
    }
    if (tipoSel.value === 'otros' && nuevoTipo.value.trim() === '') {
      alert("Debe especificar el tipo de evento.");
      e.preventDefault();
      return;
    }

    // Si no es 'otros', mandar ID como oculto
    if (tipoSel.dataset.id) {
      const hidden = document.createElement("input");
      hidden.type = "hidden";
      hidden.name = "idTipo";
      hidden.value = tipoSel.dataset.id;
      document.getElementById("eventoForm").appendChild(hidden);
    }

    // Concatenar id|valor en requisitos (nota/asistencia)
    const requisitosCheckboxes = document.querySelectorAll('input[name="requisitos[]"]');
requisitosCheckboxes.forEach(chk => {
  if (chk.checked) {
    const id = chk.value;
    const nota = document.querySelector(`input[name="notaMinima_${id}"]`);
    const asis = document.querySelector(`input[name="asistenciaMinima_${id}"]`);

    let extraValor = '';

    if (nota && nota.value.trim() !== '') {
      extraValor = nota.value.trim();
    } else if (asis && asis.value.trim() !== '') {
      extraValor = asis.value.trim();
    } else {
      // No hay input extra: usar valor por defecto "actual"
      extraValor = 'actual';
    }

    chk.value = id + '|' + extraValor;
  }
});
  // Validar nuevo requisito si está marcado
if (checkOtroReq.checked && inputOtroReq.value.trim() === '') {
  alert("Debe especificar el nombre del nuevo requisito.");
  e.preventDefault();
  return;
}

  });
  const checkOtroReq = document.getElementById("checkOtroRequisito");
const inputOtroReq = document.getElementById("inputOtroRequisito");

checkOtroReq.addEventListener("change", () => {
  inputOtroReq.disabled = !checkOtroReq.checked;
  if (!checkOtroReq.checked) inputOtroReq.value = '';
});

});
</script>

</body>
</html>
