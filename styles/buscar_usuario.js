document.addEventListener('DOMContentLoaded', () => {
  const buscarBtn = document.getElementById('buscarBtn');
  const cedulaInput = document.getElementById('cedula');
  const infoDiv = document.getElementById('infoUsuarioDatos');

  buscarBtn.addEventListener('click', async () => {
    const cedula = cedulaInput.value.trim();
    if (!cedula) {
      infoDiv.innerHTML = `<p class="vp-error">Por favor, ingrese una cédula.</p>`;
      return;
    }

    try {
      const formData = new FormData();
      formData.append('accion', 'buscar');
      formData.append('ced_usu', cedula);

      const res = await fetch('../conexion/buscar_usuario.php', {
        method: 'POST',
        body: formData
      });
 
      const data = await res.json();

      if (!data || data.success === false) {
        throw new Error(data.error || 'Usuario no encontrado');
      }

      mostrarInfoUsuario(data);
    } catch (err) {
      infoDiv.innerHTML = `<p class="vp-error">❌ ${err.message}</p>`;
    }
  });

  function mostrarInfoUsuario(usuario) {
    const nombreCompleto = [usuario.nom_pri_usu, usuario.nom_seg_usu, usuario.ape_pri_usu, usuario.ape_seg_usu]
      .filter(Boolean).join(' ');

    infoDiv.innerHTML = `
      <h3>Información del Usuario</h3>
      <p><strong>Nombre:</strong> ${nombreCompleto}</p>
      <p><strong>Cédula:</strong> ${usuario.ced_usu}</p>
      <p><strong>Correo:</strong> ${usuario.cor_usu}</p>
      <p><strong>Teléfono:</strong> ${usuario.tel_usu}</p>
      <p><strong>Dirección:</strong> ${usuario.dir_usu}</p>
      <p><strong>Fecha Nacimiento:</strong> ${usuario.fec_nac_usu}</p>
    `;
  }
});
