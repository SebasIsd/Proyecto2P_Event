const buscarBtn = document.getElementById('buscarBtn');
const cedulaInput = document.getElementById('cedula');
const resultadosDiv = document.getElementById('resultados') || document.createElement('div');
resultadosDiv.id = 'resultados';
document.body.appendChild(resultadosDiv);

const infoUsuarioDiv = document.getElementById('infoUsuario');
const nombreUsuario = document.getElementById('nombreUsuario');
const cedulaUsuario = document.getElementById('cedulaUsuario');
const correoUsuario = document.getElementById('correoUsuario');
const telefonoUsuario = document.getElementById('telefonoUsuario');
const carreraUsuario = document.getElementById('carreraUsuario');

buscarBtn.addEventListener('click', async () => {
  const cedula = cedulaInput.value.trim();
  if (!cedula) {
    alert("Por favor, ingrese una cédula.");
    return;
  }

  const formData = new FormData();
  formData.append('accion', 'buscar');
  formData.append('cedula', cedula);

  try {
    const res = await fetch('conexion/pagos.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    
    if (data.error) {
      alert(data.error);
      return;
    }

    // Mostrar información del usuario
    mostrarInfoUsuario(data.usuario);
    
    // Mostrar inscripciones
    mostrarInscripciones(data.inscripciones);
    
  } catch (error) {
    console.error('Error:', error);
    alert("Ocurrió un error al buscar la información.");
  }
});

function mostrarInfoUsuario(usuario) {
  infoUsuarioDiv.style.display = 'block';
  nombreUsuario.textContent = `${usuario.NOM_PRI_USU} ${usuario.NOM_SEG_USU} ${usuario.APE_PRI_USU} ${usuario.APE_SEG_USU}`;
  cedulaUsuario.textContent = usuario.CED_USU;
  correoUsuario.textContent = usuario.COR_USU;
  telefonoUsuario.textContent = usuario.TEL_USU;
  carreraUsuario.textContent = usuario.CAR_USU;
}

function mostrarInscripciones(inscripciones) {
  resultadosDiv.innerHTML = "";

  if (inscripciones.length === 0) {
    resultadosDiv.innerHTML = "<p>No se encontraron inscripciones para este usuario.</p>";
    return;
  }

  const table = document.createElement('table');
  table.innerHTML = `
    <thead>
      <tr>
        <th>Evento/Curso</th>
        <th>Tipo</th>
        <th>Fecha Inicio</th>
        <th>Fecha Fin</th>
        <th>Costo</th>
        <th>Estado de Pago</th>
        <th>Cambiar Estado</th>
        <th>Información de Pago</th>
      </tr>
    </thead>
    <tbody>
      ${inscripciones.map(row => `
        <tr data-id="${row.id_inscripcion}">
          <td>${row.nombre_evento}</td>
          <td>${row.tipo_evento}</td>
          <td>${row.fecha_inicio}</td>
          <td>${row.fecha_fin}</td>
          <td>$${row.costo_evento}</td>
          <td class="estado ${row.estado_pago === 'Pagado' ? 'estado-pagado' : 'estado-pendiente'}">
            ${row.estado_pago}
          </td>
          <td>
            <select class="estadoSelect">
              <option value="Pagado" ${row.estado_pago === 'Pagado' ? 'selected' : ''}>Pagado</option>
              <option value="Pendiente" ${row.estado_pago === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
            </select>
          </td>
          <td>
            ${row.fecha_pago ? `
              <strong>Fecha:</strong> ${row.fecha_pago}<br>
              <strong>Monto:</strong> $${row.monto_pago}<br>
              <strong>Método:</strong> ${row.metodo_pago}
            ` : 'Sin registro de pago'}
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

      try {
        const res = await fetch('conexion/pagos.php', {
          method: 'POST',
          body: formData
        });
        
        const data = await res.json();
        
        if (data.success) {
          const estadoCell = fila.querySelector('.estado');
          estadoCell.textContent = nuevoEstado;
          estadoCell.className = `estado ${nuevoEstado === 'Pagado' ? 'estado-pagado' : 'estado-pendiente'}`;
        }
      } catch (error) {
        console.error('Error al actualizar:', error);
      }
    }

    alert("✅ Cambios guardados correctamente.");
  });

  resultadosDiv.appendChild(table);
  resultadosDiv.appendChild(guardarBtn);
}

