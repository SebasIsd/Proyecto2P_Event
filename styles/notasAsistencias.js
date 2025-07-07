document.addEventListener("DOMContentLoaded", () => {
  const eventoSelect = document.getElementById("evento");
  const tablaCuerpo = document.getElementById("tabla-cuerpo");

  // Cargar eventos
  fetch("../admin/obtenerEventos.php")
    .then(res => res.json())
    .then(eventos => {
      eventos.forEach(e => {
        const option = document.createElement("option");
        option.value = e.id_eve_cur;
        option.textContent = e.tit_eve_cur;
        eventoSelect.appendChild(option);
      });
    });

  // Función para cargar inscritos por evento
  function cargarInscritos(idEvento) {
    if (!idEvento) return;

    fetch(`../admin/obtenerInscritosPorEvento.php?idEvento=${idEvento}`)
      .then(res => res.json())
      .then(data => {
        console.log("Datos recibidos:", data);
        tablaCuerpo.innerHTML = "";
        if (data.length === 0) {
    const fila = document.createElement("tr");
    fila.innerHTML = `
      <td colspan="5" style="text-align: center; padding: 10px; font-weight: bold;">
        No hay usuarios con notas y asistencia pendientes.
      </td>
    `;
    tablaCuerpo.appendChild(fila);
    return;
  }
        data.forEach(p => {
          const fila = document.createElement("tr");
          fila.innerHTML = `
            <td>${p.nombre_completo}</td>
            <td>
              <input type="number" step="0.01" min="0" max="100" value="${p.not_fin_not_asi || ""}" data-id="${p.id_ins}" class="nota-input" placeholder="Nota final">
            </td>
            <td>
              <input type="number" step="1" min="0" max="100" value="${p.porc_asi_not_asi || ""}" data-id="${p.id_ins}" class="porcentaje-input" placeholder="% asistencia">
            </td>
            <td>
              <button data-id="${p.id_ins}" class="guardar-btn">Guardar</button>
            </td>
          `;

          tablaCuerpo.appendChild(fila);
        });

        // Agregar eventos a los botones de guardar
        document.querySelectorAll(".guardar-btn").forEach(btn => {
          btn.addEventListener("click", () => {
            const id = btn.dataset.id;
            const nota = document.querySelector(`.nota-input[data-id="${id}"]`).value;
            const asistencia = document.querySelector(`.asistencia-select[data-id="${id}"]`).value;
            const porcentaje = document.querySelector(`.porcentaje-input[data-id="${id}"]`).value;

            fetch("../admin/guardarNotaAsistencia.php", {
              method: "POST",
              headers: { "Content-Type": "application/json" },
              body: JSON.stringify({ id_ins: id, nota, asistencia, porcentaje })
            })
              .then(res => res.json())
              .then(resp => {
                if (resp.success) {
                  alert("Guardado correctamente.");
                  // Volver a cargar los inscritos para mostrar los cambios
                  cargarInscritos(eventoSelect.value);
                } else {
                  alert("Error: " + resp.error);
                }
              })
              .catch(err => {
                alert("Error al guardar.");
                console.error(err);
              });
          });
        });
      });
  }

  // Evento al cambiar selección
  eventoSelect.addEventListener("change", () => {
    cargarInscritos(eventoSelect.value);
  });
    tablaCuerpo.innerHTML = `<tr><td colspan="5" style="text-align:center; font-weight:bold;">Seleccione un evento</td></tr>`;

});