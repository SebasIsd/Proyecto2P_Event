<?php
session_start();
require_once('../includes/conexion1.php');
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <title>Eventos</title>
  <link rel="stylesheet" href="../styles/css/style.css" />
  <link rel="stylesheet" href="../styles/css/componente.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <style>
    body {
      font-family: 'Montserrat', sans-serif;
      background-color: #f4f4f4;
    }

    .contenedor-eventos {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 15px;
      padding: 20px;
    }

    .evento-card {
      background: #fff;
      border: 1px solid #ccc;
      border-radius: 10px;
      width: 300px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      overflow: hidden;
      position: relative;
      margin-bottom: 30px;
    }

    .evento-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .evento-card h3 {
      font-size: 1.3rem;
      text-align: center;
      padding: 10px;
      margin: 0;
    }

    .botones {
      display: flex;
      justify-content: space-around;
      margin: 10px 0;
    }

    .btn-editar,
    .btn-eliminar {
      padding: 8px 14px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      font-size: 13px;
      background-color: #94754d;
      color: white;
      transition: background-color 0.3s;
    }

    .btn-editar:hover,
    .btn-eliminar:hover {
      background-color: #a75151;
    }

    .btn-vermas {
      background-color: #333;
      color: white;
      border: none;
      padding: 8px 12px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      margin: 0 auto 12px auto;
      display: block;
    }

    .btn-vermas:hover {
      background-color: #555;
    }

    .btn-favorito {
      background: none;
      border: none;
      cursor: pointer;
      font-size: 20px;
      color: #aaa;
      position: absolute;
      top: 10px;
      right: 10px;
      z-index: 1;
    }

    .btn-favorito.favorito {
      color: red;
    }

    .filtro-inscripciones {
      margin: 20px auto;
      max-width: 400px;
      display: flex;
      justify-content: center;
    }

    .filtro-inscripciones input[type="text"] {
      width: 100%;
      padding: 12px 16px;
      border: 2px solid #6c1313;
      border-radius: 10px;
      font-size: 16px;
      outline: none;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .filtro-inscripciones input[type="text"]::placeholder {
      color: #999;
    }
  </style>
</head>

<body>

  <?php include '../includes/headeradmin.php'?>

  <div class="filtro-inscripciones">
    <input type="text" id="filtroNombre" placeholder="Filtrar por nombre..." onkeyup="filtrarInscripciones()">
  </div>


  <div class="contenedor-eventos" id="contenedor-eventos"></div>

  <?php include '../includes/footeradmin.php'?>

  <script>
    let idsFavoritos = [];

function esFavorito(idEvento) {
  return idsFavoritos.includes(idEvento);
}
    document.addEventListener('DOMContentLoaded', async () => {
      const contenedor = document.getElementById('contenedor-eventos');

        try {
    // Primero cargamos favoritos desde la base
    const favResponse = await fetch('../admin/obtenerFavoritos.php');
    const favoritosData = await favResponse.json();
    idsFavoritos = favoritosData.map(f => f.id_eve_cur);
  } catch (e) {
    console.warn('No se pudieron cargar los favoritos:', e);
    idsFavoritos = [];
  }
      fetch('../admin/obtenerEventos.php')
        .then(response => {
          if (!response.ok) throw new Error('Error en la respuesta del servidor');
          return response.json();
        })
        .then(data => {
          contenedor.innerHTML = '';

          if (!Array.isArray(data) || data.length === 0) {
            contenedor.innerHTML = '<p>No hay eventos disponibles.</p>';
            return;
          }

              data.sort((a, b) => {
      const aFav = esFavorito(a.id_eve_cur) ? 0 : 1;
      const bFav = esFavorito(b.id_eve_cur) ? 0 : 1;
      return aFav - bFav;
    });
          data.forEach(evento => {
            const card = document.createElement('div');
            card.classList.add('evento-card');

            const esFav = esFavorito(evento.id_eve_cur) ? 'favorito' : '';

            card.innerHTML = `
              <button class="btn-favorito ${esFav}" onclick="toggleFavorito(${evento.id_eve_cur}, this)">
                <i class="fas fa-heart"></i>
              </button>
              <img src="../images/${evento.img_tipo_eve}" alt="Imagen tipo evento">
              <h3>${evento.tit_eve_cur}</h3>
              <div class="botones">
                <button class="btn-editar" onclick="editarEvento(${evento.id_eve_cur})"><i class="fas fa-edit"></i></button>
                <button class="btn-eliminar" onclick="eliminarEvento(${evento.id_eve_cur})"><i class="fas fa-trash"></i></button>
              </div>
              <button class="btn-vermas" onclick="verMas(${evento.id_eve_cur})"><i class="fas fa-eye"></i> Ver más</button>
            `;

            contenedor.appendChild(card);
          });
        })
        .catch(err => {
          console.error('Error al obtener eventos:', err);
          contenedor.innerHTML = '<p style="color:red;">Error al cargar eventos.</p>';
        });
    });

    function eliminarEvento(id) {
      if (confirm('¿Estás seguro de que deseas eliminar este evento?')) {
        fetch(`../admin/eliminarEvento.php?id=${id}`, { method: 'GET' })
          .then(res => res.json())
          .then(data => {
            alert(data.mensaje);
            location.reload();
          })
          .catch(err => {
            console.error('Error eliminando evento:', err);
            alert('Error eliminando el evento');
          });
      }
    }

    function editarEvento(id) {
      window.location.href = `../admin/editarEvento.php?id=${id}`;
    }

    function verMas(id) {
      window.location.href = `verEventos.php?id=${id}`;
    }

    function filtrarInscripciones() {
      const input = document.getElementById('filtroNombre');
      const filtro = input.value.toUpperCase();
      const tarjetas = document.querySelectorAll('.evento-card');

      tarjetas.forEach(card => {
        const titulo = card.querySelector('h3').textContent.toUpperCase();
        card.style.display = titulo.includes(filtro) ? '' : 'none';
      });
    }

function toggleFavorito(idEvento, boton) {
  const esFavorito = boton.classList.contains('favorito');
  const accion = esFavorito ? 'remover' : 'agregar';

  fetch('../admin/favoritoEvento.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ id: idEvento, accion })
  })
  .then(response => response.json())
  .then(data => {
    if (data.ok) {
      boton.classList.toggle('favorito');
    } else {
      alert('Error al actualizar favorito: ' + (data.mensaje || 'Desconocido'));
    }
  })
  .catch(err => {
    console.error('Error en fetch favorito:', err);
    alert('Error al procesar la solicitud');
  });
}

  </script>
</body>

</html>

