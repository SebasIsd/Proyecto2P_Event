const buscarBtn = document.getElementById('buscarBtn');
const cedulaInput = document.getElementById('cedula');
const resultadosDiv = document.getElementById('resultados');
const infoUsuarioDiv = document.getElementById('infoUsuario');

const nombreUsuario = document.getElementById('nombreUsuario');
const cedulaUsuario = document.getElementById('cedulaUsuario');
const correoUsuario = document.getElementById('correoUsuario');
const telefonoUsuario = document.getElementById('telefonoUsuario');
const carreraUsuario = document.getElementById('carreraUsuario');

buscarBtn.addEventListener('click', async () => {
  const cedula = cedulaInput.value.trim();
  if (!cedula) {
    mostrarError("Por favor, ingrese una cédula.");
    return;
  }

  try {
    mostrarCargando();
    
    const formData = new FormData();
    formData.append('accion', 'buscar');
    formData.append('cedula', cedula);

    const res = await fetch('../conexion/pagos.php', {
      method: 'POST',
      body: formData
    });

    if (!res.ok) {
      throw new Error(`Error HTTP: ${res.status}`);
    }

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.error || "Error desconocido");
    }

    // Mostrar información del usuario
    mostrarInfoUsuario(data.usuario);

    // Mostrar inscripciones con pagos
    mostrarInscripciones(data.inscripciones);

  } catch (error) {
    console.error('Error:', error);
    mostrarError(error.message);
  }
});

function mostrarCargando() {
  resultadosDiv.innerHTML = "<p class='cargando'>Buscando información...</p>";
  infoUsuarioDiv.style.display = 'none';
}

function mostrarError(mensaje) {
  resultadosDiv.innerHTML = `<p class="error">${mensaje}</p>`;
}

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
    resultadosDiv.innerHTML = "<p class='info'>No se encontraron inscripciones para este usuario.</p>";
    return;
  }

  const table = document.createElement('table');
  table.innerHTML = `
    <thead>
      <tr>
        <th>Evento/Curso</th>
        <th>Tipo</th>
        <th>Fechas</th>
        <th>Costo</th>
        <th>Estado Pago</th>
        <th>Cambiar Estado</th>
        <th>Pagos</th>
      </tr>
    </thead>
    <tbody>
      ${inscripciones.map(insc => `
        <tr data-id="${insc.id_inscripcion}">
          <td>${insc.nombre_evento}</td>
          <td>${insc.tipo_evento}</td>
          <td>
            <strong>Inicio:</strong> ${insc.fecha_inicio}<br>
            <strong>Fin:</strong> ${insc.fecha_fin}
          </td>
          <td>$${insc.costo_evento}</td>
          <td class="estado ${insc.estado_pago === 'Pagado' ? 'estado-pagado' : 'estado-pendiente'}">
            ${insc.estado_pago}
          </td>
          <td>
            <select class="estadoSelect">
              <option value="Pagado" ${insc.estado_pago === 'Pagado' ? 'selected' : ''}>Pagado</option>
              <option value="Pendiente" ${insc.estado_pago === 'Pendiente' ? 'selected' : ''}>Pendiente</option>
            </select>
          </td>
          <td>
            ${insc.pagos.length > 0 ? 
              insc.pagos.map(pago => `
                <div class="pago-item">
                  <strong>Fecha:</strong> ${pago.fecha_pago}<br>
                  <strong>Monto:</strong> $${pago.monto_pago}<br>
                  <strong>Método:</strong> ${pago.metodo_pago}
                </div>
              `).join('') : 
              '<div class="sin-pago">Sin pagos registrados</div>'
            }
          </td>
        </tr>
      `).join('')}
    </tbody>
  `;

  resultadosDiv.appendChild(table);

  // Botón Guardar Cambios
  const guardarBtn = document.createElement('button');
  guardarBtn.className = 'guardar-btn';
  guardarBtn.textContent = "Guardar Cambios";
  guardarBtn.addEventListener('click', guardarCambios);
  resultadosDiv.appendChild(guardarBtn);
}

async function guardarCambios() {
  const filas = document.querySelectorAll('tbody tr');
  const cambios = [];
  
  for (let fila of filas) {
    const id = fila.getAttribute('data-id');
    const nuevoEstado = fila.querySelector('.estadoSelect').value;
    const estadoActual = fila.querySelector('.estado').textContent.trim();
    
    if (nuevoEstado !== estadoActual) {
      cambios.push({ id, nuevoEstado });
    }
  }
  
  if (cambios.length === 0) {
    alert("No hay cambios para guardar.");
    return;
  }
  
  try {
    const resultados = await Promise.all(
      cambios.map(async cambio => {
        const formData = new FormData();
        formData.append('accion', 'actualizar');
        formData.append('id_inscripcion', cambio.id);
        formData.append('estado_pago', cambio.nuevoEstado);
        
        const res = await fetch('../conexion/pagos.php', {
          method: 'POST',
          body: formData
        });
        
        return res.json();
      })
    );
    
    const errores = resultados.filter(r => !r.success);
    if (errores.length > 0) {
      throw new Error(`Error al guardar ${errores.length} cambios`);
    }
    
    // Actualizar visualmente los estados
    cambios.forEach(cambio => {
      const fila = document.querySelector(`tr[data-id="${cambio.id}"]`);
      const estadoCelda = fila.querySelector('.estado');
      estadoCelda.textContent = cambio.nuevoEstado;
      estadoCelda.className = `estado ${cambio.nuevoEstado === 'Pagado' ? 'estado-pagado' : 'estado-pendiente'}`;
    });
    
    alert("✅ Todos los cambios se guardaron correctamente.");
  } catch (error) {
    console.error('Error al guardar:', error);
    alert("❌ Ocurrieron errores al guardar algunos cambios. Por favor revise la consola para más detalles.");
  }
}