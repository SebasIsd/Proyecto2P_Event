document.addEventListener('DOMContentLoaded', () => {
    const modalidad = document.getElementById("modalidad");
    const costo = document.getElementById("costo");
    const form = document.getElementById("eventoForm");

    modalidad.addEventListener("change", () => {
        if (modalidad.value === "Gratis") {
            costo.value = "0.00";
            costo.disabled = true;
        } else {
            costo.value = "";
            costo.disabled = false;
        }
    });

    
    form.addEventListener('submit', function(e) {
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

        const datos = new FormData(form);

        fetch("../admin/crearEvento.php", {
            method: "POST",
            body: datos
        })
        .then(response => response.text())
        .then(data => {
            alert(data);
            form.reset();
            costo.disabled = false;
        })
        .catch(error => {
            alert("Error al guardar el evento.");
            console.error(error);
        
        });
    });  
}); 


