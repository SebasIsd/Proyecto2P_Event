<?php
require_once '../conexion/conexion.php'; // tu archivo de conexión
 $conn = CConexion::ConexionBD();

 
if (!$conn) {
    die("No se pudo establecer la conexión a la base de datos.");
}
// Guardar evento si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $modalidad = $_POST['modalidad'];
    $costo = floatval($_POST['costo']);
    $fechaInicio = $_POST['fechaInicio'];
    $fechaFin = $_POST['fechaFin'];
    $nuevoTipo = $_POST['nuevoTipo'] ?? null;
    $idTipo = $_POST['idTipo'] ?? null;
    $carreras = $_POST['carreras'] ?? [];
    $requisitos = $_POST['requisitos'] ?? [];

    try {
    
        
        // Insertar nuevo tipo si es "Otro"
        if (!empty($nuevoTipo)) {
            $stmt = $conn->prepare("INSERT INTO TIPOS_EVENTO (NOM_TIPO_EVE) VALUES (:nombre) RETURNING ID_TIPO_EVE");
            $stmt->execute([':nombre' => $nuevoTipo]);
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
            $conn->prepare("INSERT INTO EVENTOS_CARRERAS (ID_EVE_CUR, ID_CAR) VALUES (:e, :c)")
                 ->execute([':e' => $idEvento, ':c' => $idCar]);
        }

        // Insertar requisitos
        foreach ($requisitos as $valor) {
            list($idReq, $valTexto) = explode('|', $valor);
            $conn->prepare("INSERT INTO EVENTOS_REQUISITOS (ID_EVE_CUR, ID_REQ, VALOR_REQ) 
                           VALUES (:e, :r, :v)")
                 ->execute([':e' => $idEvento, ':r' => $idReq, ':v' => $valTexto]);
        }

        $conn->commit();
        echo "<script>alert('Evento guardado correctamente'); window.location.href='crear_evento.php';</script>";
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        echo "<script>alert('Error al guardar: " . $e->getMessage() . "');</script>";
    }
}

// Obtener datos para mostrar
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
  <meta charset="UTF-8">
  <title>Crear Evento Académico</title>
  <style>
    body { font-family: Arial; background: #f4f4f4; margin: 0; padding: 20px; }
    .formulario-container { background: #fff; padding: 25px; border-radius: 10px; max-width: 800px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    fieldset { border: 1px solid #ccc; margin-bottom: 20px; padding: 20px; border-radius: 8px; }
    legend { font-weight: bold; color: #8b0000; font-size: 1.1em; }
    label { margin-top: 10px; display: block; font-weight: bold; }
    input[type="text"], input[type="date"], select, textarea {
      width: 100%; padding: 8px; border-radius: 5px; border: 1px solid #ccc; margin-top: 5px;
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
  <div class="formulario-container">
    <form method="POST" id="eventoForm">
      <fieldset>
        <legend>Información General</legend>
        <label>Título</label>
        <input type="text" name="titulo" required>
        <label>Descripción</label>
        <textarea name="descripcion" rows="3" required></textarea>
        <label>Modalidad</label>
        <select name="modalidad" id="modalidad" required>
          <option value="">Seleccione</option>
          <option value="Gratis">Gratis</option>
          <option value="Pagado">Pagado</option>
        </select>
        <label>Costo</label>
        <input type="text" name="costo" id="costo" required>
      </fieldset>

      <fieldset>
        <legend>Tipo de Evento</legend>
        <div class="checkbox-group">
          <?php foreach ($tipos as $tipo): ?>
            <label><input type="radio" name="tipoEvento" value="<?= strtolower($tipo['nombre']) ?>" data-id="<?= $tipo['id'] ?>"> <?= htmlspecialchars($tipo['nombre']) ?></label>
          <?php endforeach; ?>
          <label><input type="radio" name="tipoEvento" value="otros"> Otro</label>
        </div>
        <input type="text" name="nuevoTipo" id="nuevoTipoEspecifico" placeholder="Especifique otro tipo" disabled>
      </fieldset>

      <fieldset>
        <legend>Fechas</legend>
        <label>Fecha Inicio</label>
        <input type="date" name="fechaInicio" id="fechaInicio" required>
        <label>Fecha Fin</label>
        <input type="date" name="fechaFin" id="fechaFin" required>
      </fieldset>

      <fieldset>
        <legend>Carreras Participantes</legend>
        <div class="checkbox-group">
          <?php foreach ($carreras as $car): ?>
            <label><input type="checkbox" name="carreras[]" value="<?= $car['id'] ?>"> <?= htmlspecialchars($car['nombre']) ?></label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset>
        <legend>Requisitos</legend>
        <div class="checkbox-group">
          <?php foreach ($requisitos as $req): ?>
            <label><input type="checkbox" name="requisitos[]" value="<?= $req['id'] ?>|Requerido"> <?= htmlspecialchars($req['nombre']) ?></label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="boton-container">
        <button type="submit">GUARDAR</button>
      </div>
    </form>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const modalidad = document.getElementById("modalidad");
      const costo = document.getElementById("costo");
      const nuevoTipo = document.getElementById("nuevoTipoEspecifico");
      const radios = document.querySelectorAll('input[name="tipoEvento"]');

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

      document.getElementById("eventoForm").addEventListener("submit", (e) => {
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
        } else if (tipoSel.value === 'otros' && nuevoTipo.value.trim() === '') {
          alert("Debe especificar el tipo de evento.");
          e.preventDefault();
        } else if (tipoSel.dataset.id) {
          const hidden = document.createElement("input");
          hidden.type = "hidden";
          hidden.name = "idTipo";
          hidden.value = tipoSel.dataset.id;
          document.getElementById("eventoForm").appendChild(hidden);
        }
      });
    });
  </script>
</body>
</html>
