document.addEventListener('DOMContentLoaded', function() {
    // Cargar eventos desde la API (simulado)
    cargarEventos();
    
    // Mostrar/ocultar sección de pago según estado
    const estadoPago = document.getElementById('estado_pago');
    estadoPago.addEventListener('change', function() {
        const seccionPago = document.getElementById('seccion_pago');
        seccionPago.style.display = this.value === 'Pagado' ? 'block' : 'none';
    });
    
    // Manejar envío del formulario
    const form = document.getElementById('formInscripcion');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const data = {
            cedula: document.getElementById('cedula').value,
            id_evento: document.getElementById('evento').value,
            fecha_inscripcion: document.getElementById('fecha_inscripcion').value,
            fecha_cierre: document.getElementById('fecha_cierre').value,
            estado_pago: document.getElementById('estado_pago').value
        };
        
        if (data.estado_pago === 'Pagado') {
            data.pago = {
                fecha: document.getElementById('fecha_pago').value,
                monto: document.getElementById('monto_pago').value,
                metodo: document.getElementById('metodo_pago').value
            };
        }
        
        enviarInscripcion(data);
    });
});

function cargarEventos() {
    // Simular carga de eventos desde la API
    const eventos = [
        { id: 1, nombre: 'Taller de Programación', fecha_ini: '2023-10-15', fecha_fin: '2023-10-20', costo: 50.00, modalidad: 'Pagado' },
        { id: 2, nombre: 'Seminario de Inteligencia Artificial', fecha_ini: '2023-11-05', fecha_fin: '2023-11-07', costo: 0.00, modalidad: 'Gratis' },
        { id: 3, nombre: 'Curso de Base de Datos', fecha_ini: '2023-11-10', fecha_fin: '2023-11-30', costo: 75.00, modalidad: 'Pagado' }
    ];
    
    const selectEvento = document.getElementById('evento');
    
    eventos.forEach(evento => {
        const option = document.createElement('option');
        option.value = evento.id;
        option.textContent = `${evento.nombre} (${evento.modalidad}) - ${evento.fecha_ini} al ${evento.fecha_fin}`;
        selectEvento.appendChild(option);
    });
}

function enviarInscripcion(data) {
    // Simular envío a la API
    console.log('Datos a enviar:', data);
    
    fetch('inscripcion.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Inscripción registrada con éxito. ID: ' + data.id_inscripcion);
            document.getElementById('formInscripcion').reset();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la inscripción');
    });
}