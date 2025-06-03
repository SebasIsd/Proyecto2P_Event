document.addEventListener('DOMContentLoaded', function() {
    // Cargar eventos disponibles desde la API
    cargarEventosDisponibles();
    
    // Mostrar/ocultar sección de pago según estado
    const estadoPago = document.getElementById('estado_pago');
    estadoPago.addEventListener('change', function() {
        const seccionPago = document.getElementById('seccion_pago');
        seccionPago.style.display = this.value === 'Pagado' ? 'block' : 'none';
    });
    
    // Establecer fechas por defecto
    const fechaInscripcion = document.getElementById('fecha_inscripcion');
    const fechaCierre = document.getElementById('fecha_cierre');
    fechaInscripcion.valueAsDate = new Date();
    
    // Calcular fecha de cierre (7 días después)
    const cierreDate = new Date();
    cierreDate.setDate(cierreDate.getDate() + 1);
    fechaCierre.valueAsDate = cierreDate;
    
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

async function cargarEventosDisponibles() {
    try {
        const response = await fetch('../.././conexion/dashboard2.php');
        if (!response.ok) {
            throw new Error('Error al obtener eventos');
        }
        const data = await response.json();
        
        const selectEvento = document.getElementById('evento');
        selectEvento.innerHTML = '<option value="">Seleccione un evento...</option>';
        
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (data.proximosEventos && data.proximosEventos.length > 0) {
            // Filtrar eventos que aún no han finalizado
            const eventosDisponibles = data.proximosEventos.filter(evento => {
                const fechaFin = new Date(evento.fechaFin);
                return fechaFin >= hoy;
            });
            
            if (eventosDisponibles.length === 0) {
                selectEvento.innerHTML = '<option value="">No hay eventos disponibles actualmente</option>';
                return;
            }
            
            eventosDisponibles.forEach(evento => {
                const option = document.createElement('option');
                option.value = evento.id;
                
                const fechaInicio = new Date(evento.fechaInicio).toLocaleDateString('es-ES');
                const fechaFin = new Date(evento.fechaFin).toLocaleDateString('es-ES');
                
                option.textContent = `${evento.titulo} (${fechaInicio} - ${fechaFin}) - $${evento.costo}`;
                option.dataset.costo = evento.costo;
                option.dataset.modalidad = evento.modalidad;
                selectEvento.appendChild(option);
            });
        } else {
            selectEvento.innerHTML = '<option value="">No hay eventos disponibles</option>';
        }
    } catch (error) {
        console.error('Error al cargar eventos:', error);
        document.getElementById('evento').innerHTML = '<option value="">Error al cargar eventos</option>';
    }
}

function enviarInscripcion(data) {
    fetch('../usuarios/inscripciones/inscripcion.php', {
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
            // Redirigir a mis eventos
            window.location.href = '../usuarios/mis_eventos.php';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Ocurrió un error al procesar la inscripción');
    });
}