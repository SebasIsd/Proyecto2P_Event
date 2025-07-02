<?php
require_once "../includes/conexion1.php";
$conexion = new Conexion();
$conn = $conexion->getConexion();

$hoy = date('Y-m-d');
$buscar = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
$por_pagina = 6;
$offset = ($pagina - 1) * $por_pagina;

$condiciones = [];
$parametros = [];
$idx = 1;

if ($buscar !== '') {
    $condiciones[] = "LOWER(tit_eve_cur) LIKE '%' || LOWER($".$idx++ . ") || '%'";
    $parametros[] = $buscar;
}

$where_sql = count($condiciones) > 0 ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$sql_count = "SELECT COUNT(*) FROM eventos_cursos $where_sql";
$result_count = pg_query_params($conn, $sql_count, $parametros);
$total_eventos = pg_fetch_result($result_count, 0, 0);
$total_paginas = ceil($total_eventos / $por_pagina);

$sql = "SELECT * FROM eventos_cursos $where_sql ORDER BY fec_ini_eve_cur ASC LIMIT $".$idx++." OFFSET $".$idx++;
$parametros[] = $por_pagina;
$parametros[] = $offset;

$result = pg_query_params($conn, $sql, $parametros);

function url_con_params($params) {
    $query = array_merge($_GET, $params);
    return '?' . http_build_query($query);
}
?>

<form method="get" class="row g-3 mb-4">
  <div class="col-md-10">
    <input type="text" name="buscar" value="<?= htmlspecialchars($buscar) ?>" placeholder="Buscar por título..." class="form-control">
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary w-100">Buscar</button>
  </div>
</form>

<div class="row g-4">
<?php 
$estado_colors = [
    'Próximo' => 'success',
    'En curso' => 'info',
    'Finalizado' => 'secondary',
];

while ($row = pg_fetch_assoc($result)) {
    $inicio = $row['fec_ini_eve_cur'];
    $fin = $row['fec_fin_eve_cur'];

    if ($hoy < $inicio) {
        $estado = "Próximo";
    } elseif ($hoy >= $inicio && $hoy <= $fin) {
        $estado = "En curso";
    } else {
        $estado = "Finalizado";
    }

    $color = $estado_colors[$estado] ?? 'dark';
?>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="course-box h-100 border rounded shadow-sm p-3 bg-white">
      <div class="course-details">
        <h5><?= htmlspecialchars($row['tit_eve_cur']) ?></h5>
        <p><strong>Estado:</strong> <span class="badge bg-<?= $color ?>"><?= $estado ?></span></p>
        <p><?= nl2br(htmlspecialchars($row['des_eve_cur'])) ?></p>
        <p><strong>Inicio:</strong> <?= date('d/m/Y', strtotime($inicio)) ?></p>
        <p><strong>Fin:</strong> <?= date('d/m/Y', strtotime($fin)) ?></p>
        <p><strong>Modalidad:</strong> <?= htmlspecialchars($row['mod_eve_cur']) ?></p>
        <p><strong>Costo:</strong> $<?= number_format($row['cos_eve_cur'], 2) ?></p>
        <a href="formulario_inscripcion.php?id_evento=<?= $row['id_eve_cur'] ?>" class="btn btn-inscribirse mt-2 w-100">Inscribirme</a>
      </div>
    </div>
  </div>
<?php } ?>
</div>

<?php if ($total_paginas > 1): ?>
<nav aria-label="Paginación eventos" class="mt-4">
  <ul class="pagination justify-content-center">
    <li class="page-item <?= ($pagina <= 1) ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= url_con_params(['pagina' => $pagina - 1]) ?>" aria-label="Anterior">&laquo;</a>
    </li>
    <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
      <li class="page-item <?= ($i === $pagina) ? 'active' : '' ?>">
        <a class="page-link" href="<?= url_con_params(['pagina' => $i]) ?>"><?= $i ?></a>
      </li>
    <?php endfor; ?>
    <li class="page-item <?= ($pagina >= $total_paginas) ? 'disabled' : '' ?>">
      <a class="page-link" href="<?= url_con_params(['pagina' => $pagina + 1]) ?>" aria-label="Siguiente">&raquo;</a>
    </li>
  </ul>
</nav>
<?php endif; ?>
