document.addEventListener('DOMContentLoaded', function () {
    // Elementos del DOM
    const buscarBtn = document.getElementById('buscarBtn');
    const cedulaInput = document.getElementById('ced_usu');
    const userDataSection = document.getElementById('userData');
    const loadingIndicator = document.getElementById('loading');
    const errorMsg = document.getElementById('errorMsg');
    const btnEditar = document.getElementById('btnEditar');
    const btnGuardar = document.getElementById('btnGuardar');

    // Event Listeners
    buscarBtn.addEventListener('click', buscarUsuario);
    btnEditar.addEventListener('click', habilitarEdicion);
    btnGuardar.addEventListener('click', actualizarUsuario);

    cedulaInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            buscarUsuario();
        }
    });

    async function buscarUsuario() {
        const cedula = cedulaInput.value.trim();
        errorMsg.textContent = '';
        userDataSection.style.display = 'none';

        if (!cedula) {
            errorMsg.textContent = 'Por favor ingrese una cédula';
            return;
        }

        try {
            mostrarCargando(true);

            const response = await fetch('../conexion/buscar_usuario.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `accion=buscar&ced_usu=${encodeURIComponent(cedula)}`
            });

            const data = await response.json();

            if (!response.ok || data.error) {
                throw new Error(data.error || 'Error en la respuesta del servidor');
            }

            llenarFormulario(data);
            userDataSection.style.display = 'block';
            btnEditar.style.display = 'inline-block';
            btnGuardar.style.display = 'none';

        } catch (error) {
            console.error('Error:', error);
            errorMsg.textContent = error.message;
        } finally {
            mostrarCargando(false);
        }
    }

    function llenarFormulario(usuario) {
        document.getElementById('ced_usu_hidden').value = usuario.ced_usu || '';
        document.getElementById('nom_pri_usu').value = usuario.nom_pri_usu || '';
        document.getElementById('nom_seg_usu').value = usuario.nom_seg_usu || '';
        document.getElementById('ape_pri_usu').value = usuario.ape_pri_usu || '';
        document.getElementById('ape_seg_usu').value = usuario.ape_seg_usu || '';
        document.getElementById('cor_usu').value = usuario.cor_usu || '';
        document.getElementById('pas_usu').value = usuario.pas_usu || '';
        document.getElementById('tel_usu').value = usuario.tel_usu || '';
        document.getElementById('dir_usu').value = usuario.dir_usu || '';
        document.getElementById('fec_nac_usu').value = usuario.fec_nac_usu || '';

        // Deshabilitar los campos por defecto
        deshabilitarCampos();
    }

    function habilitarEdicion() {
        // Habilitar todos los campos excepto el hidden
        const campos = document.querySelectorAll('#formUsuario input:not([type="hidden"])');
        campos.forEach(campo => {
            campo.disabled = false;
        });
        
        // Cambiar visibilidad de botones
        btnEditar.style.display = 'none';
        btnGuardar.style.display = 'inline-block';
    }

    function deshabilitarCampos() {
        const campos = document.querySelectorAll('#formUsuario input:not([type="hidden"])');
        campos.forEach(campo => {
            campo.disabled = true;
        });
    }

    async function actualizarUsuario() {
        errorMsg.textContent = '';

        try {
            mostrarCargando(true);

            const formData = new FormData();
            formData.append('accion', 'actualizar');
            formData.append('ced_usu', document.getElementById('ced_usu_hidden').value);
            formData.append('nom_pri_usu', document.getElementById('nom_pri_usu').value);
            formData.append('nom_seg_usu', document.getElementById('nom_seg_usu').value);
            formData.append('ape_pri_usu', document.getElementById('ape_pri_usu').value);
            formData.append('ape_seg_usu', document.getElementById('ape_seg_usu').value);
            formData.append('cor_usu', document.getElementById('cor_usu').value);
            formData.append('pas_usu', document.getElementById('pas_usu').value);
            formData.append('tel_usu', document.getElementById('tel_usu').value);
            formData.append('dir_usu', document.getElementById('dir_usu').value);
            formData.append('fec_nac_usu', document.getElementById('fec_nac_usu').value);

            const response = await fetch('../conexion/buscar_usuario.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (!response.ok || data.error || !data.success) {
                throw new Error(data.error || 'Error al actualizar los datos');
            }

            alert(data.mensaje || 'Datos actualizados correctamente');

            // Volver a bloquear los campos
            deshabilitarCampos();

            btnEditar.style.display = 'inline-block';
            btnGuardar.style.display = 'none';

        } catch (error) {
            console.error('Error:', error);
            errorMsg.textContent = error.message;
        } finally {
            mostrarCargando(false);
        }
    }

    function mostrarCargando(mostrar) {
        loadingIndicator.style.display = mostrar ? 'inline-block' : 'none';
    }
});