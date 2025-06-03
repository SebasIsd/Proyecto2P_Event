document.addEventListener("DOMContentLoaded", () => {
  const tabla = document.getElementById("tabla-certificados");

  fetch("../admin/obtenerCertificables.php")
    .then(res => res.json())
    .then(data => {
      tabla.innerHTML = "";

      data.forEach(p => {
        const fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${p.nombre_completo}</td>
          <td>${p.titulo_evento}</td>
          <td>${p.nota}</td>
          <td>${p.porcentaje_asistencia}%</td>
          <td>${p.modalidad}</td>
          <td>
            <button class="generar-btn" data-id="${p.id_ins}">Generar</button>
            <button class="ver-btn" data-id="${p.id_ins}">Ver</button>
          </td>
        `;

        tabla.appendChild(fila);
      });

      document.querySelectorAll(".generar-btn").forEach(btn => {
        btn.addEventListener("click", () => {
          const id = btn.dataset.id;
          fetch("../admin/generarCertificado.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id_ins: id })
          })
            .then(res => res.text()) 
            .then(texto => {
              console.log("Respuesta cruda del servidor:", texto); 

              try {
                const resp = JSON.parse(texto); 
                if (resp.success) {
                  alert("Certificado generado correctamente.");
                  btn.disabled = true;
                  btn.textContent = "Generado";
                } else {
                  alert("Error al generar certificado: " + (resp.error || "Respuesta inválida."));
                }
              } catch (e) {
                console.error("No se pudo parsear JSON:", e);
                alert("La respuesta del servidor no es válida:\n\n" + texto);
              }
            })
             .catch(err => {
              console.error("Error al generar el certificado:", err);
              alert("Ocurrió un error al generar el certificado.");
            });
        });
      });

          document.querySelectorAll(".ver-btn").forEach(btn => {
            btn.addEventListener("click", () => {
              const id = btn.dataset.id;
              window.open(`../admin/verCertificado.php?id_ins=${id}`, "_blank");
            });
          });
        })
          .catch(err => {
            console.error("Error al cargar los certificables:", err);
            tabla.innerHTML = "<tr><td colspan='6'>No se pudo cargar la información.</td></tr>";
          });
      });
