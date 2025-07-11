<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Detalle del Evento</title>
  <link rel="stylesheet" href="../styles/css/style.css" />
  <link rel="stylesheet" href="../styles/css/estilosEventos.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    .detalle-container {
      max-width: 800px;
      margin: auto;
      background: white;
      border-radius: 10px;
      padding: 20px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .detalle-container img {
      width: 100%;
      max-height: 300px;
      object-fit: cover;
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .detalle-container h1 {
      text-align: center;
      margin-bottom: 10px;
    }

    .detalle-container p {
      font-size: 16px;
      margin: 8px 0;
    }

    .detalle-container .label {
      font-weight: bold;
    }

    .volver-btn {
      display: inline-block;
      margin-top: 20px;
      padding: 10px 18px;
      background-color: #94754d;
      color: white;
      border: none;
      border-radius: 8px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
    }

    .volver-btn:hover {
      background-color: #6c1313;
    }
  </style>
</head>

<body>

  <header>
    <div class="container">
      <div class="logo">
        <h1>Sistema de <span>Eventos</span></h1>
      </div>
      <nav>
        <ul>
          <li><a href="../admin/admin.php"><i class="fas fa-home"></i> Inicio</a></li>
          <li><a href="../admin/ingresoEventos.php" ><i class="fas fa-calendar-alt"></i> Ingresar eventos</a></li>
          <li><a href="../admin/eventos.php"><i class="fas fa-eye" class="active"></i> Ver Eventos</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <div class="detalle-container" id="detalleEvento">
    <p>Cargando evento...</p>
  </div>
 <?php include '../admin/footer.php'?>
  <script>
    const urlParams = new URLSearchParams(window.location.search);
    const idEvento = urlParams.get("id");

    if (!idEvento) {
      document.getElementById("detalleEvento").innerHTML = "<p style='color:red;'>Evento no encontrado.</p>";
    } else {
      fetch(`../admin/obtenerEventoPorId.php?id=${idEvento}`)
        .then(response => response.json())
        .then(evento => {
          if (evento.error) {
            document.getElementById("detalleEvento").innerHTML = `<p style='color:red;'>${evento.error}</p>`;
            return;
          }

          const contenedor = document.getElementById("detalleEvento");
          contenedor.innerHTML = `
            <img src="../images/${evento.img_tipo_eve}" alt="Imagen del tipo de evento">
            <h1>${evento.tit_eve_cur}</h1>
            <p><span class="label">Descripción:</span> ${evento.des_eve_cur}</p>
            <p><span class="label">Fecha Inicio:</span> ${evento.fec_ini_eve_cur}</p>
            <p><span class="label">Fecha Fin:</span> ${evento.fec_fin_eve_cur}</p>
            <p><span class="label">Modalidad:</span> ${evento.mod_eve_cur}</p>
            <p><span class="label">Costo:</span> $${evento.cos_eve_cur}</p>
            <p><span class="label">Tipo de Evento:</span> ${evento.tip_eve}</p>
            <p><span class="label">Carreras Asociadas:</span> ${evento.carreras?.join(", ") ?? 'N/A'}</p>
            <p><span class="label">Requisitos:</span></p>
            <ul>
              ${evento.requisitos?.map(r => `<li><strong>${r.nombre}:</strong> ${r.valor}</li>`).join("") ?? '<li>Ninguno</li>'}
            </ul>
            <a class="volver-btn" href="eventos.php"><i class="fas fa-arrow-left"></i> Volver</a>
          `;
        })
        .catch(err => {
          document.getElementById("detalleEvento").innerHTML = `<p style='color:red;'>Error al cargar el evento.</p>`;
          console.error(err);
        });
    }
  </script>
</body>

</html>
