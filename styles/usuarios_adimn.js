document.addEventListener('DOMContentLoaded', () => {
    cargarUsuarios();

    document.getElementById('formAgregarUsuario').addEventListener('submit', function (e) {
        e.preventDefault();
        agregarUsuario();
    });

    document.getElementById('formEliminarUsuario').addEventListener('submit', function (e) {
        e.preventDefault();
        eliminarUsuario();
    });
});

function cargarUsuarios() {
    fetch('../conexion/mostrar_usuario.php') // ✅ Asegúrate de que la ruta es correcta
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(usuarios => {
            const cuerpoTabla = document.getElementById('cuerpoTablaUsuarios');
            cuerpoTabla.innerHTML = '';
            usuarios.forEach(usuario => {
                const fila = document.createElement('tr');
                const nombres = `${usuario.nom_pri_usu} ${usuario.nom_seg_usu ?? ''}`.trim();
                const apellidos = `${usuario.ape_pri_usu} ${usuario.ape_seg_usu ?? ''}`.trim();
                const telefono = usuario.tel_usu ?? '';
               const carrera = usuario.car_usu ?? 'No definida';
const cargo = usuario.cargo ?? 'No asignado';

fila.innerHTML = `
    <td>${usuario.ced_usu}</td>
    <td>${nombres}</td>
    <td>${apellidos}</td>
    <td>${usuario.cor_usu}</td>
    <td>${telefono}</td>
    <td>${carrera}</td>
    <td>${cargo}</td>
`;
                cuerpoTabla.appendChild(fila);
            });
        })
        .catch(err => {
            console.error('Error al cargar usuarios:', err);
        });
}

function agregarUsuario() {
    const form = document.getElementById('formAgregarUsuario');
    const formData = new FormData(form);

    fetch('../conexion/agregar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.text())
    .then(mensaje => {
        alert(mensaje);
        form.reset();
        cargarUsuarios();
    })
    .catch(error => console.error('Error al agregar:', error));
}

function eliminarUsuario() {
    const form = document.getElementById('formEliminarUsuario');
    const formData = new FormData(form);

    fetch('../conexion/eliminar_usuario.php', {
        method: 'POST',
        body: formData
    })
    .then(resp => resp.text())
    .then(mensaje => {
        alert(mensaje);
        form.reset();
        cargarUsuarios();
    })
    .catch(error => console.error('Error al eliminar:', error));
}
