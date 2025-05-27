document.addEventListener("DOMContentLoaded", function () {
    const buscarBtn = document.getElementById("buscarBtn");
    const editarBtn = document.getElementById("editarBtn");
    const guardarBtn = document.getElementById("guardarBtn");

    buscarBtn.addEventListener("click", function () {
        const cedula = document.getElementById("ced_usu").value;

        if (!cedula) {
            alert("Ingresa una cédula.");
            return;
        }

        fetch("conexion/buscar_usuario.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `ced_usu=${cedula}`
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            // Llenar campos
            for (const campo in data) {
                if (document.getElementById(campo)) {
                    document.getElementById(campo).value = data[campo];
                }
            }

            // Bloquear todos menos cédula
            bloquearCampos(true);

            // Mostrar botón editar
            editarBtn.style.display = "inline-block";
            guardarBtn.style.display = "none";
        })
        .catch(error => {
            console.error("Error al buscar:", error);
        });
    });

    editarBtn.addEventListener("click", function () {
        // Desbloquear campos excepto cedula
        bloquearCampos(false);

        // Mostrar botón guardar
        guardarBtn.style.display = "inline-block";
        editarBtn.style.display = "none";
    });

    guardarBtn.addEventListener("click", function () {
        const formData = new URLSearchParams();
        formData.append("accion", "actualizar");

        const campos = [
            "ced_usu", "nom_pri_usu", "nom_seg_usu", "ape_pri_usu", "ape_seg_usu",
            "cor_usu", "pas_usu", "tel_usu", "dir_usu", "fec_nac_usu"
        ];

        campos.forEach(campo => {
            const valor = document.getElementById(campo).value;
            formData.append(campo, valor);
        });

        fetch("conexion/buscar_usuario.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: formData.toString()
        })
        .then(res => res.json())
        .then(data => {
            alert(data.mensaje || "Actualización exitosa.");
            bloquearCampos(true);
            guardarBtn.style.display = "none";
            editarBtn.style.display = "inline-block";
        })
        .catch(error => {
            console.error("Error al actualizar:", error);
        });
    });

    function bloquearCampos(bloquear) {
        const campos = [
            "nom_pri_usu", "nom_seg_usu", "ape_pri_usu", "ape_seg_usu",
            "cor_usu", "pas_usu", "tel_usu", "dir_usu", "fec_nac_usu"
        ];
        campos.forEach(campo => {
            const input = document.getElementById(campo);
            if (input) input.disabled = bloquear;
        });
        // La cédula siempre debe estar habilitada
        document.getElementById("ced_usu").disabled = false;
    }
});

