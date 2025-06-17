document.addEventListener("DOMContentLoaded", () => {
    const modalidad = document.getElementById("modalidad");
    const costo = document.getElementById("costo");
    const form = document.getElementById("eventoForm");

    // Manejar la modalidad para costo
    modalidad.addEventListener("change", () => {
        if (modalidad.value === "Gratis") {
            costo.value = "0.00";
            costo.disabled = true;
        } else {
            costo.value = "";
            costo.disabled = false;
        }
    });

    // Cargar datos automáticamente al cargar la página
    fetch('../admin/cargar_datos_evento.php')
        .then(response => response.json())
        .then(data => {
            console.log("Datos recibidos:", data);
            cargarCheckboxes(data.carreras, 'carrerasContainer', 'carreras[]');
            cargarCheckboxes(data.tipos_evento, 'tiposEventoContainer', 'tipoEvento[]');
            cargarCheckboxes(data.requisitos, 'requisitosContainer', 'requisitos[]', true);
        })
        .catch(err => {
            console.error("Error al cargar datos:", err);
        });

    // Validación y envío del formulario
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const fechaInicio = new Date(document.getElementById("fechaInicio").value);
        const fechaFin = new Date(document.getElementById("fechaFin").value);
        const hoy = new Date();

        hoy.setHours(0, 0, 0, 0);
        fechaInicio.setHours(0, 0, 0, 0);
        fechaFin.setHours(0, 0, 0, 0);

        if (fechaInicio < hoy) {
            alert("La fecha de inicio no puede ser anterior al día actual.");
            return;
        }

        if (fechaFin < fechaInicio) {
            alert("La fecha de fin no puede ser anterior a la fecha de inicio.");
            return;
        }

        // Enviar datos con FormData
        const formData = new FormData(form);

        // Agregar tipos seleccionados
        document.querySelectorAll("input[name='tipoEvento[]']:checked").forEach(input => {
            formData.append("tiposEvento[]", input.value);
        });

        // Agregar carreras seleccionadas
        document.querySelectorAll("input[name='carreras[]']:checked").forEach(input => {
            formData.append("carreras[]", input.value);
        });

        // Agregar requisitos seleccionados con valores
        document.querySelectorAll("input[name='requisitos[]']:checked").forEach(input => {
            formData.append("requisitos[]", input.value);
        });

        const notaMinima = document.querySelector("input[name='notaMinima']");
        const asistenciaMinima = document.querySelector("input[name='asistenciaMinima']");

        if (notaMinima && !notaMinima.disabled) {
            formData.append("valor_nota", notaMinima.value);
        }

        if (asistenciaMinima && !asistenciaMinima.disabled) {
            formData.append("valor_asistencia", asistenciaMinima.value);
        }

        // Enviar los datos
        fetch('../admin/crearEvento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Evento guardado correctamente');
                form.reset();
            } else {
                alert('Error al guardar el evento: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error al enviar los datos:', error);
            alert('Hubo un error al guardar el evento.');
        });
    });
});

// Función para cargar checkboxes dinámicamente
function cargarCheckboxes(items, containerId, name, conInputExtra = false) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';

    items.forEach(item => {
        const label = document.createElement('label');
        label.innerHTML = `<input type="checkbox" name="${name}" value="${item.id}"> ${item.nombre}`;
        container.appendChild(label);

        // Si es requisito de nota
        if (conInputExtra && item.nombre.toLowerCase().includes('nota')) {
            const input = document.createElement('input');
            input.type = 'number';
            input.name = 'notaMinima';
            input.placeholder = 'Ej. 7';
            input.min = 0;
            input.max = 10;
            input.disabled = true;

            label.querySelector('input[type=checkbox]').addEventListener('change', e => {
                input.disabled = !e.target.checked;
            });

            container.appendChild(input);
        }

        // Si es requisito de asistencia
        if (conInputExtra && item.nombre.toLowerCase().includes('asistencia')) {
            const input = document.createElement('input');
            input.type = 'number';
            input.name = 'asistenciaMinima';
            input.placeholder = 'Ej. 80';
            input.min = 0;
            input.max = 100;
            input.disabled = true;

            label.querySelector('input[type=checkbox]').addEventListener('change', e => {
                input.disabled = !e.target.checked;
            });

            container.appendChild(input);
        }
    });
}
