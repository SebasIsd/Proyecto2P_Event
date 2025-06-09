document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();
});

function cargarUsuarios() {
    fetch('../conexion/mostrar_usuario.php')
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(usuarios => {
            const cuerpoTabla = document.getElementById('cuerpoTablaUsuarios');
            cuerpoTabla.innerHTML = '';
            
            usuarios.forEach(usuario => {
                const fila = document.createElement('tr');
                const nombres = `${usuario.nom_pri_usu} ${usuario.nom_seg_usu || ''}`.trim();
                const apellidos = `${usuario.ape_pri_usu} ${usuario.ape_seg_usu || ''}`.trim();
                const telefono = usuario.tel_usu || '';
                const carrera = usuario.car_usu || 'No definida';
                const cargo = usuario.cargo || 'No asignado';

                fila.innerHTML = `
                    <td>${usuario.ced_usu}</td>
                    <td>${nombres}</td>
                    <td>${apellidos}</td>
                    <td>${usuario.cor_usu}</td>
                    <td>${telefono}</td>
                    <td>${carrera}</td>
                    <td>${cargo}</td>
                    <td>
                        <button class="action-btn edit-btn" onclick="abrirEditarUsuario(${JSON.stringify(usuario).replace(/"/g, '&quot;')})">
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        <button class="action-btn delete-btn" onclick="abrirEliminarUsuario('${usuario.ced_usu}')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </td>
                `;
                cuerpoTabla.appendChild(fila);
            });
        })
        .catch(err => {
            console.error('Error al cargar usuarios:', err);
            alert('Error al cargar los usuarios: ' + err.message);
        });
}

function agregarUsuario() {
    const form = document.getElementById('formAgregarUsuario');
    const formData = new FormData(form);
    const errorElement = document.getElementById('errorAgregar');

    errorElement.textContent = '';
    
    fetch('../conexion/agregar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        alert(data.mensaje || 'Usuario agregado correctamente');
        cerrarModal('modalAgregar');
        form.reset();
        cargarUsuarios();
    })
    .catch(error => {
        console.error('Error al agregar:', error);
        errorElement.textContent = error.message;
    });
}

function actualizarUsuario() {
    const form = document.getElementById('formEditarUsuario');
    const formData = new FormData(form);
    const errorElement = document.getElementById('errorEditar');

    errorElement.textContent = '';
    
    fetch('actualizar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        alert(data.mensaje || 'Usuario actualizado correctamente');
        cerrarModal('modalEditar');
        cargarUsuarios();
    })
    .catch(error => {
        console.error('Error al actualizar:', error);
        errorElement.textContent = error.message;
    });
}

function eliminarUsuarioConfirmado() {
    const cedula = document.getElementById('delete_ced_usu').value;
    const errorElement = document.getElementById('errorEliminar');

    errorElement.textContent = '';
    
    fetch('../conexion/eliminar_usuario.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cedula=${encodeURIComponent(cedula)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            throw new Error(data.error);
        }
        
        alert(data.mensaje || 'Usuario eliminado correctamente');
        cerrarModal('modalEliminar');
        cargarUsuarios();
    })
    .catch(error => {
        console.error('Error al eliminar:', error);
        errorElement.textContent = error.message;
    });
}