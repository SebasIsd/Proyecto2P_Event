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

  // Crear el modal (se añade solo una vez)
  if (!document.getElementById('modalComprobante')) {
    const modalHTML = `
      <div id="modalComprobante" class="modal" style="display:none;position:fixed;z-index:100;left:0;top:0;width:100%;height:100%;background-color:rgba(0,0,0,0.8)">
        <div class="modal-content" style="margin:5% auto;width:80%;max-width:800px">
          <span class="close" style="color:#fff;float:right;font-size:28px;cursor:pointer">&times;</span>
          <img id="imagenComprobante" style="width:100%">
        </div>
      </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Eventos para cerrar el modal
    document.querySelector('.close').onclick = function() {
      document.getElementById('modalComprobante').style.display = 'none';
    };
    window.onclick = function(event) {
      if (event.target == document.getElementById('modalComprobante')) {
        document.getElementById('modalComprobante').style.display = 'none';
      }
    };
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
        <th>Comprobante</th>
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
            ${insc.comprobante_oid ? 
              `<a href="#" onclick="mostrarComprobante(${insc.id_inscripcion}); return false;">
                Ver Comprobante
              </a>` : 
              'Sin comprobante'}
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

// Función para mostrar el comprobante en el modal
function mostrarComprobante(idInscripcion) {
  const modal = document.getElementById('modalComprobante');
  const img = document.getElementById('imagenComprobante');
  
  img.src = `../usuarios/inscripciones/ver_comprobante.php?id_inscripcion=${idInscripcion}`;
  modal.style.display = 'block';
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

  console.log(await response.text());
  buscarBtn.click(); // recargar tabla
}
