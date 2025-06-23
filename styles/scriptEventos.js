document.addEventListener("DOMContentLoaded", () => {
    const modalidad = document.getElementById("modalidad");
    const costo = document.getElementById("costo");
    const form = document.getElementById("eventoForm");
    const nuevoTipoEspecifico = document.getElementById("nuevoTipoEspecifico");

    // Manejar modalidad
    modalidad.addEventListener("change", () => {
        if (modalidad.value === "Gratis") {
            costo.value = "0.00";
            costo.disabled = true;
        } else {
            costo.value = "";
            costo.disabled = false;
        }
    });

    // Cargar datos iniciales
    fetch('../admin/cargar_datos_evento.php')
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            console.log("Datos recibidos:", data);

            if (!data) throw new Error('Estructura de datos incorrecta');

            if (data.carreras) {
                cargarCheckboxes(data.carreras, 'carrerasContainer', 'carreras');
            }

            if (data.tipos_evento) {
                cargarRadioButtons(data.tipos_evento, 'tiposEventoContainer', 'tipoEvento');
            }

            if (data.requisitos) {
                cargarCheckboxes(data.requisitos, 'requisitosContainer', 'requisitos', true);
            }
        })
        .catch(err => {
            console.error("Error al cargar datos:", err);
            alert("Error al cargar los datos iniciales.");
        });

    // ✅ AQUÍ estaba el error: faltaba cerrar este bloque completo con una llave antes de las funciones
    form.addEventListener("submit", (e) => {
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

        const formData = new FormData(form);

        const tipoSeleccionado = form.querySelector('input[name="tipoEvento"]:checked');
        if (tipoSeleccionado && tipoSeleccionado.value.toLowerCase() === 'otros' && nuevoTipoEspecifico.value.trim() !== '') {
            formData.append('nuevoTipo', nuevoTipoEspecifico.value.trim());
        }

        fetch('../admin/crearEvento.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) throw new Error('Error en la respuesta del servidor');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert("Evento guardado correctamente");
                form.reset();
                nuevoTipoEspecifico.disabled = true;
                cargarTiposDeEvento();
            } else {
                alert("Error al guardar el evento: " + (data.message || "Error desconocido"));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Hubo un error al guardar el evento.");
        });
    }); 

    // FUNCIONES

    function cargarCheckboxes(items, containerId, name, conInputExtra = false) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        items.forEach(item => {
            if (!item || item.id === undefined || !item.nombre) return;

            const label = document.createElement('label');
            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.name = name + '[]';
            checkbox.value = item.id;

            label.appendChild(checkbox);
            label.appendChild(document.createTextNode(' ' + item.nombre));
            container.appendChild(label);

            if (conInputExtra) {
                if (item.nombre.toLowerCase().includes('nota')) {
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.name = 'notaMinima';
                    input.placeholder = 'Ej. 7';
                    input.min = 0;
                    input.max = 10;
                    input.disabled = true;
                    input.style.marginLeft = '10px';

                    checkbox.addEventListener('change', e => {
                        input.disabled = !e.target.checked;
                        if (!e.target.checked) input.value = '';
                    });

                    container.appendChild(input);
                    container.appendChild(document.createElement('br'));
                }

                if (item.nombre.toLowerCase().includes('asistencia')) {
                    const input = document.createElement('input');
                    input.type = 'number';
                    input.name = 'asistenciaMinima';
                    input.placeholder = 'Ej. 80';
                    input.min = 0;
                    input.max = 100;
                    input.disabled = true;
                    input.style.marginLeft = '10px';

                    checkbox.addEventListener('change', e => {
                        input.disabled = !e.target.checked;
                        if (!e.target.checked) input.value = '';
                    });

                    container.appendChild(input);
                    container.appendChild(document.createElement('br'));
                }
            }
        });
    }

    function cargarRadioButtons(items, containerId, name) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';

        items.forEach(item => {
            if (!item || item.id === undefined || !item.nombre) return;

            const label = document.createElement('label');
            const radio = document.createElement('input');
            radio.type = 'radio';
            radio.name = name;
            radio.value = item.nombre;

            radio.addEventListener('change', () => {
                if (radio.value.toLowerCase() === 'otros') {
                    nuevoTipoEspecifico.disabled = false;
                } else {
                    nuevoTipoEspecifico.disabled = true;
                    nuevoTipoEspecifico.value = '';
                }
            });

            label.appendChild(radio);
            label.appendChild(document.createTextNode(' ' + item.nombre));
            container.appendChild(label);
        });
    }

    function cargarTiposDeEvento() {
        fetch('../admin/cargar_tipos_evento.php')
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar tipos de evento');
                return response.json();
            })
            .then(data => {
                if (data && Array.isArray(data)) {
                    cargarRadioButtons(data, 'tiposEventoContainer', 'tipoEvento');
                } else {
                    console.error('Formato de datos inesperado:', data);
                }
            })
            .catch(error => {
                console.error('Error al cargar tipos de evento:', error);
            });
    }
}); 

