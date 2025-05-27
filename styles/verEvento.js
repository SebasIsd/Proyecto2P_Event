document.addEventListener('DOMContentLoaded', () => {
  const contenedor = document.getElementById('contenedor-eventos');

  fetch('../admin/obtenerEventos.php')
    .then(response => response.json())
    .then(data => {
      contenedor.innerHTML = ''; // Limpiar

      if (data.error) {
        contenedor.innerHTML = `<p style="color:red;">${data.error}</p>`;
        return;
      }

      if (!Array.isArray(data) || data.length === 0) {
        contenedor.innerHTML = '<p>No hay eventos disponibles.</p>';
        return;
      }

      data.forEach(evento => {
        const card = document.createElement('div');
        card.classList.add('evento-card');

        card.innerHTML = `
          <h3>${evento.tit_eve_cur}</h3>
          <p>${evento.des_eve_cur}</p>
          <p><strong>Fecha:</strong> Inicia el ${evento.fec_ini_eve_cur} y finaliza el ${evento.fec_fin_eve_cur}</p>
          <p><strong>Tipo:</strong> ${evento.tip_eve}</p>
          <p><strong>Modalidad:</strong> ${evento.mod_eve_cur} - $${evento.cos_eve_cur}</p>
          <button class="btn-editar" onclick="editarEvento(${evento.id_eve_cur})">Editar</button>
          <button class="btn-eliminar" onclick="eliminarEvento(${evento.id_eve_cur})">Eliminar</button>
        `;
        contenedor.appendChild(card);
      });
    })
    .catch(err => {
      contenedor.innerHTML = '<p>Error al cargar eventos.</p>';
      console.error('Error al obtener eventos:', err);
    });
});

function eliminarEvento(id) {
  if (confirm('¿Estás seguro de que deseas eliminar este evento?')) {
    fetch(`eliminarEvento.php?id=${id}`, { method: 'GET' })
      .then(res => res.json())
      .then(data => {
        alert(data.mensaje);
        location.reload(); // Recargar para actualizar la lista
      })
      .catch(err => {
        console.error('Error eliminando evento:', err);
        alert('Error eliminando el evento');
      });
  }
}

function editarEvento(id) {
  window.location.href = `editarEvento.html?id=${id}`;
}