const buscarBtn = document.getElementById('buscarBtn');
const correoInput = document.getElementById('correo');
const resultadosDiv = document.getElementById('resultados');

buscarBtn.addEventListener('click', async () => {
  const correo = correoInput.value.trim();
  if (!correo) {
    alert("Por favor, ingrese un correo.");
    return;
  }

  const formData = new FormData();
  formData.append('accion', 'buscar');
  formData.append('correo', correo);

  const res = await fetch('conexion.php', {
    method: 'POST',
    body: formData
  });

  const data = await res.json();
  resultadosDiv.innerHTML = "";

  if (data.length === 0) {
    resultadosDiv.innerHTML = "<p>No se encontraron inscripciones para este correo.</p>";
    return;
  }

  const user = data[0];
  const table = document.createElement('table');
  table.innerHTML = `
    <thead>
      <tr><th>Evento</th><th>Estado de Pago</th><th>Cambiar Estado</th></tr>
    </thead>
    <tbody>
      ${data.map(row => `
        <tr data-id="${row.id_inscripcion}">
          <td>${row.nombre_evento}</td>
          <td class="estado">${row.estado_pago}</td>
          <td>
            <select class="estadoSelect">
              <option value="pagado" ${row.estado_pago === 'pagado' ? 'selected' : ''}>Pagado</option>
              <option value="pendiente" ${row.estado_pago === 'pendiente' ? 'selected' : ''}>Pendiente</option>
            </select>
          </td>
        </tr>`).join('')}
    </tbody>
  `;

  const guardarBtn = document.createElement('button');
  guardarBtn.textContent = "Guardar Cambios";
  guardarBtn.addEventListener('click', async () => {
    const filas = table.querySelectorAll('tbody tr');
    for (let fila of filas) {
      const id = fila.getAttribute('data-id');
      const nuevoEstado = fila.querySelector('.estadoSelect').value;

      const formData = new FormData();
      formData.append('accion', 'actualizar');
      formData.append('id_inscripcion', id);
      formData.append('estado_pago', nuevoEstado);

      await fetch('conexion.php', {
        method: 'POST',
        body: formData
      });

      fila.querySelector('.estado').textContent = nuevoEstado;
    }

    alert("✅ Cambios guardados correctamente.");
  });

  resultadosDiv.appendChild(table);
  resultadosDiv.appendChild(guardarBtn);
});

