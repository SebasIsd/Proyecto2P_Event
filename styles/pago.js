const buscarBtn = document.getElementById('buscarBtn');
const cedulaInput = document.getElementById('cedula');
const resultadosDiv = document.getElementById('resultados');

buscarBtn.addEventListener('click', async () => {
  const cedula = cedulaInput.value.trim();
  if (!cedula) {
    resultadosDiv.innerHTML = "<p class='error'>Por favor, ingrese una cédula.</p>";
    return;
  }

  resultadosDiv.innerHTML = "<p>Buscando información...</p>";

  try {
    const formData = new FormData();
    formData.append('accion', 'buscar');
    formData.append('cedula', cedula);

    const response = await fetch('../conexion/pagos.php', {
      method: 'POST',
      body: formData
    });

    const data = await response.json();

    if (!data.success) {
      resultadosDiv.innerHTML = `<p class='error'>${data.error}</p>`;
      return;
    }

    mostrarInscripciones(data.inscripciones);
  } catch (error) {
    resultadosDiv.innerHTML = `<p class='error'>Ocurrió un error: ${error.message}</p>`;
  }
});

function mostrarInscripciones(inscripciones) {
  if (inscripciones.length === 0) {
    resultadosDiv.innerHTML = "<p>No se encontraron inscripciones.</p>";
    return;
  }

  const tabla = document.createElement('table');
  tabla.innerHTML = `
    <thead>
      <tr>
        <th>Evento</th>
        <th>Tipo</th>
        <th>Fechas</th>
        <th>Costo</th>
        <th>Estado</th>
        <th>Cambiar Estado</th>
        <th>Pagos</th>
      </tr>
    </thead>
    <tbody>
      ${inscripciones.map(insc => `
        <tr data-id="${insc.id_inscripcion}">
          <td>${insc.nombre_evento}</td>
          <td>${insc.tipo_evento}</td>
          <td>Inicio: ${insc.fecha_inicio}<br>Fin: ${insc.fecha_fin}</td>
          <td>$${insc.costo_evento}</td>
          <td class="estado">${insc.estado_pago}</td>
          <td>
            <select class="estadoSelect">
              <option value="Pagado" ${insc.estado_pago === 'Pagado' ? 'selected' : ''}>Pagado</option>
              <option value="Pendiente" ${insc.estado_pago === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
            </select>
          </td>
          <td>
            ${(insc.pagos || []).map(p => `
              <div><strong>${p.fecha_pago}</strong><br>${p.monto_pago} (${p.metodo_pago})</div>
            `).join('') || 'Sin pagos'}
          </td>
        </tr>`).join('')}
    </tbody>
  `;

  resultadosDiv.innerHTML = '';
  resultadosDiv.appendChild(tabla);

  const guardarBtn = document.createElement('button');
  guardarBtn.textContent = 'Guardar Cambios';
  guardarBtn.onclick = guardarCambios;
  resultadosDiv.appendChild(guardarBtn);
  
}

async function guardarCambios() {
  const filas = document.querySelectorAll('tbody tr');
  const cambios = [];

  filas.forEach(fila => {
    const id = fila.getAttribute('data-id');
    const nuevoEstado = fila.querySelector('.estadoSelect').value;
    const estadoActual = fila.querySelector('.estado').textContent.trim();

    if (nuevoEstado !== estadoActual) {
      cambios.push({ id, nuevoEstado });
    }
  });

  if (cambios.length === 0) {
    alert("No hay cambios para guardar.");
    return;
  }

  for (let cambio of cambios) {
    const formData = new FormData();
    formData.append('accion', 'actualizar');
    formData.append('id_inscripcion', cambio.id);
    formData.append('estado_pago', cambio.nuevoEstado);

    await fetch('../conexion/pagos.php', {
      method: 'POST',
      body: formData
    });
  }

  
  buscarBtn.click(); // recargar tabla
}
