document.addEventListener("DOMContentLoaded", () => {
  cargarInscripciones();
});

function cargarInscripciones() {
  fetch('../admin/verInscripciones.php')
    .then(response => response.json())
    .then(data => {
      const contenedor = document.getElementById('contenedor-inscripciones');
      if (!contenedor) {
        console.error('Elemento con id "contenedor-inscripciones" no encontrado.');
        return;
      }

      contenedor.innerHTML = ""; // Limpiar contenido anterior

      data.forEach((inscripcion, index) => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
  <td>${index + 1}</td>
  <td>${inscripcion.nombre_completo}</td>
  <td>${inscripcion.evento}</td>
  <td>${inscripcion.fecha_inicio}</td>
  <td>${inscripcion.fecha_cierre}</td>
  <td>${inscripcion.estado_pago}</td>
  <td>
            <a href="#" onclick="eliminarInscripcion(${inscripcion.id_inscripcion})">Eliminar</a>
          </td>
        `;
        contenedor.appendChild(fila);
      });
    })
    .catch(error => {
      console.error('Error al cargar inscripciones:', error);
    });
}

function editarInscripcion(id) {
  const nuevoEstado = prompt("Nuevo estado de pago (ej. Pendiente, Pagado):");
  if (nuevoEstado) {
    fetch('../admin/editarInscripcion.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}&estado_pago=${encodeURIComponent(nuevoEstado)}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Inscripción actualizada');
        cargarInscripciones();
      } else {
        alert(data.error || 'Error al actualizar inscripción');
      }
    });
  }
}

function eliminarInscripcion(id) {
  if (confirm('¿Estás seguro de eliminar esta inscripción?')) {
    fetch('../admin/eliminarInscripcion.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        alert('Inscripción eliminada');
        cargarInscripciones();
      } else {
        alert(data.error || 'Error al eliminar inscripción');
      }
    });
  }
}

function filtrarInscripciones() {
  const input = document.getElementById('filtroNombre');
  const filtro = input.value.toUpperCase();
  const contenedor = document.getElementById('contenedor-inscripciones');
  
  // Asegurarse de que el contenedor existe
  if (!contenedor) {
    console.error('Elemento con id "contenedor-inscripciones" no encontrado.');
    return;
  }

  // Obtener todas las filas del cuerpo de la tabla
  const filas = contenedor.getElementsByTagName('tr');

  // Iterar sobre las filas
  for (let i = 0; i < filas.length; i++) {
    const celdas = filas[i].getElementsByTagName('td');
    if (celdas.length > 0) {
      const nombreCompleto = celdas[1].textContent || celdas[1].innerText;
      // Mostrar u ocultar la fila según si coincide con el filtro
      filas[i].style.display = nombreCompleto.toUpperCase().includes(filtro) ? '' : 'none';
    }
  }
}
