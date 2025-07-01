<?php
require_once '../conexion/conexion.php';

$conn = CConexion::ConexionBD();

// Obtener catálogos
$tiposEvento = $conn->query("SELECT * FROM TIPOS_EVENTO")->fetchAll(PDO::FETCH_ASSOC);
$carreras = $conn->query("SELECT * FROM CARRERAS")->fetchAll(PDO::FETCH_ASSOC);
$requisitos = $conn->query("SELECT * FROM REQUISITOS")->fetchAll(PDO::FETCH_ASSOC);

// Obtener ID del evento desde la URL
$idEvento = $_GET['id'] ?? null;
$evento = null;
$eventoCarreras = [];
$eventoRequisitos = [];

if ($idEvento) {
    $stmt = $conn->prepare("SELECT * FROM EVENTOS_CURSOS WHERE ID_EVE_CUR = ?");
    $stmt->execute([$idEvento]);
    $evento = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmtCar = $conn->prepare("SELECT ID_CAR FROM EVENTOS_CARRERAS WHERE ID_EVE_CUR = ?");
    $stmtCar->execute([$idEvento]);
    $eventoCarreras = $stmtCar->fetchAll(PDO::FETCH_COLUMN);

    $stmtReq = $conn->prepare("SELECT ID_REQ, VALOR_REQ FROM EVENTOS_REQUISITOS WHERE ID_EVE_CUR = ?");
    $stmtReq->execute([$idEvento]);
    $eventoRequisitos = $stmtReq->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Editar Evento</title>
    <link rel="stylesheet" href="../styles/css/style.css" />
  <link rel="stylesheet" href="../styles/css/estilosEventos.css" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    body {
      font-family: 'Montserrat', sans-serif;
      background: #f5f5f5;
    }

    .formulario-container {
      max-width: 900px;
      margin: 20px auto;
      background: #fff;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    fieldset {
      border: 1px solid #ccc;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 8px;
    }

    legend {
      font-weight: bold;
      color: #6c1313;
      padding: 0 10px;
    }

    label {
      display: block;
      margin-top: 12px;
      font-weight: bold;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 6px;
    }

    .checkbox-group {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .boton-container {
      text-align: center;
      margin-top: 20px;
    }

    .boton-container button {
      background-color: #6c1313;
      color: #fff;
      padding: 10px 24px;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
    }

    .boton-container button:hover {
      background-color: #a52a2a;
    }
  </style>
</head>

<body>
  <header>
    <div class="container">
      <h1 style="text-align: center; padding: 20px;">Edicion <span>Eventos</span></h1>
      </div>
      <nav>
        <ul>
          <li><a href="../admin/admin.html"><i class="fas fa-home"></i> Inicio</a></li>
          <li><a href="../admin/indexEvento.html" class="active"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
          <li><a href="../admin/verEventos.html"><i class="fas fa-eye"></i> Ver Eventos</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <div class="formulario-container">
    <h2>Editar Evento Académico</h2>
    <form id="formEditarEvento" method="post" action="guardarEdicion.php">
      <input type="hidden" name="id_eve_cur" value="<?= $evento['id_eve_cur'] ?? '' ?>">

      <fieldset>
        <legend>Información General</legend>
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($evento['tit_eve_cur'] ?? '') ?>" required>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" id="descripcion" rows="3" required><?= htmlspecialchars($evento['des_eve_cur'] ?? '') ?></textarea>

        <label for="modalidad">Modalidad:</label>
        <select name="modalidad" required>
          <option value="">Selecciona</option>
          <option value="Gratis" <?= ($evento['mod_eve_cur'] ?? '') == 'Gratis' ? 'selected' : '' ?>>Gratis</option>
          <option value="Pagado" <?= ($evento['mod_eve_cur'] ?? '') == 'Pagado' ? 'selected' : '' ?>>Pagado</option>
        </select>

        <label for="costo">Costo:</label>
        <input type="number" name="costo" value="<?= $evento['cos_eve_cur'] ?? '0.00' ?>" required min="0">
      </fieldset>

      <fieldset>
        <legend>Fechas</legend>
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input type="date" name="fecha_inicio" value="<?= $evento['fec_ini_eve_cur'] ?? '' ?>" required>

        <label for="fecha_fin">Fecha de Fin:</label>
        <input type="date" name="fecha_fin" value="<?= $evento['fec_fin_eve_cur'] ?? '' ?>" required>
      </fieldset>

      <fieldset>
        <legend>Tipo de Evento</legend>
        <select name="id_tipo_evento" required>
          <option value="">Selecciona un tipo</option>
          <?php foreach ($tiposEvento as $tipo): ?>
            <option value="<?= $tipo['id_tipo_eve'] ?>" <?= ($evento['id_tipo_eve'] ?? '') == $tipo['id_tipo_eve'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($tipo['nom_tipo_eve']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </fieldset>

      <fieldset>
        <legend>Carreras</legend>
        <div class="checkbox-group">
          <?php foreach ($carreras as $car): ?>
            <label>
              <input type="checkbox" name="carreras[]" value="<?= $car['id_car'] ?>"
                <?= in_array($car['id_car'], $eventoCarreras) ? 'checked' : '' ?>>
              <?= htmlspecialchars($car['nom_car']) ?>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset>
        <legend>Requisitos</legend>
        <div class="checkbox-group">
          <?php foreach ($requisitos as $req):
            $valor = '';
            foreach ($eventoRequisitos as $er) {
              if ($er['id_req'] == $req['id_req']) {
                $valor = $er['valor_req'];
                break;
              }
            }
          ?>
            <label>
              <input type="checkbox" name="requisitos[<?= $req['id_req'] ?>]" <?= $valor !== '' ? 'checked' : '' ?>>
              <?= htmlspecialchars($req['nom_req']) ?>
              <input type="text" name="valores[<?= $req['id_req'] ?>]" value="<?= htmlspecialchars($valor) ?>" placeholder="Valor (opcional)">
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <div class="boton-container">
        <button type="submit">Guardar Cambios</button>
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
</body>

</html>
