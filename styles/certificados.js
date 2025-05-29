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
  <td><button class="generar-btn" data-id="${p.id_ins}">Generar</button></td>
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
          .then(res => res.json())
          .then(resp => {
            if (resp.success) {
              alert("Certificado generado.");
              btn.disabled = true;
              btn.textContent = "Generado";
            } else {
              alert("Error: " + resp.error);
            }
          });
        });
      });
    });
});
