<?php
require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

$sql = "SELECT * FROM eventos_cursos ORDER BY fec_ini_eve_cur ASC";
$result = pg_query($conn, $sql);

if (!$result) {
    echo "<p>Error en la consulta: " . pg_last_error($conn) . "</p>";
    exit;
}

while ($row = pg_fetch_assoc($result)): ?>
  <div class="event-card">
    <div class="event-header">
        <h3><?= htmlspecialchars($row['tit_eve_cur']) ?></h3>
    </div>
    <div class="event-body">
        <p><strong>Inicio:</strong> <?= date('d/m/Y', strtotime($row['fec_ini_eve_cur'])) ?></p>
        <p><strong>Fin:</strong> <?= date('d/m/Y', strtotime($row['fec_fin_eve_cur'])) ?></p>
        <p><strong>Modalidad:</strong> <?= htmlspecialchars($row['mod_eve_cur']) ?></p>
        <p><?= htmlspecialchars($row['des_eve_cur']) ?></p>
        <p><strong>Costo:</strong> $<?= number_format($row['cos_eve_cur'], 2) ?></p>
        <a href="#" class="btn btn-inscribirse mt-2">Inscribirse</a>
    </div>
  </div>
<?php endwhile; ?>
