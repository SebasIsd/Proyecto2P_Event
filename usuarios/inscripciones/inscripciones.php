<?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($_GET['success']); ?>
    </div>
<?php endif; ?>
<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

// Obtener la cédula de la sesión
$cedula = $_SESSION['cedula']; 

$sql = "SELECT nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, car_usu 
        FROM usuarios 
        WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, array($cedula));

if ($datos = pg_fetch_assoc($result)) {
    $nombre_usuario = $datos['nom_pri_usu'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Inscripción</title>
    <link rel="stylesheet" href="../../styles/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            max-width: 90%;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }
        
        .modal-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }
        
        .modal-icon.success {
            color: #28a745;
        }
        
        .close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }
        
        .btn-modal {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
            font-weight: 500;
        }
        
        .btn-modal:hover {
            background-color: #218838;
        }
    </style>

    <header>
        <div class="container">
            <div class="logo">
                <h1>Bienvenido, <span><?= htmlspecialchars($nombre_usuario) ?></span></h1>

            </div>
            <nav>
                <ul>
                    <li><a href="../inicio.php"><i class="fas fa-home"></i> Inicio</a></li>
                    <li><a href="../mis_eventos.php"><i class="fas fa-calendar-alt"></i> Eventos</a></li>
                    <li><a href="./inscripciones.php" class="active"><i class="fas fa-edit"></i> Inscripciones</a></li>
                    <li><a href="../SolicitudesCambios/solicitudCambios.html"><i class="fas fa-chart-bar"></i> Solicitudes de Cambios</a></li>
                    <li class="profile-link">
                        <a href="../perfil.php"><i class="fas fa-user-circle"></i> Perfil</a>
                    </li>
                    <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2><i class="fas fa-edit"></i> Nueva Inscripción</h2>
            
            <!-- Cambiar el formulario para que use método POST -->
            <form id="formInscripcion" method="POST" action="procesar_inscripcion.php">
    <div class="form-group">
        <label for="cedula">Cédula del Usuario</label>
        <input type="text" id="cedula" name="cedula" class="form-control" value="<?php echo htmlspecialchars($cedula); ?>" readonly required>
    </div>
    
    <div class="form-group">
        <label for="evento">Evento/Curso</label>
        <select id="evento" name="evento" class="form-control" required>
            <option value="">Seleccione un evento...</option>
        </select>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="fecha_inscripcion">Fecha de Inscripción</label>
            <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control" required>
        </div>
    </div>

    <input type="hidden" id="estado_pago" name="estado_pago" value="PENDIENTE">

    <div id="info_pago" style="display: none;">
        <div class="form-group">
            <label for="tipo_evento">Tipo de Evento</label>
            <input type="text" id="tipo_evento" name="tipo_evento" class="form-control" readonly>
        </div>

        <div class="form-group">
            <label for="costo">Costo</label>
            <input type="text" id="costo" name="costo" class="form-control" readonly>
        </div>

        <div class="form-group" id="comprobante_group" style="display: none;">
            <label for="comprobante">Subir Comprobante de Pago</label>
            <input type="file" id="comprobante" name="comprobante" accept="image/*" class="form-control">
        </div>

        <div class="form-group" id="info_gratis" style="display: none;">
            <p><strong>Este evento es gratuito y no requiere comprobante.</strong></p>
        </div>
    </div>

    
    <button type="submit" class="btn-submit">Registrar Inscripción</button><br>
    <button type="button" id="btnRegresar" class="btn-submit">Regresar</button>
</form>
        </div>
    </main>

    <!-- Modal de éxito -->
    <div id="successModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div class="modal-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3>¡Inscripción Exitosa!</h3>
            <p>Tu inscripción se ha procesado correctamente.</p>
            <button id="modalCloseBtn" class="btn-modal">Aceptar</button>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Sobre el Sistema</h3>
                    <p>Sistema de gestión de inscripciones para eventos y cursos académicos.</p>
                </div>
                <div class="footer-section">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-envelope"></i> contacto@institucion.edu</p> 
                    <p><i class="fas fa-phone"></i> +123 456 7890</p>
                </div>
                <div class="footer-section">
                    <h3>Enlaces Rápidos</h3>
                    <ul>
                        <li><a href="../inicio.php">Inicio</a></li>
                        <li><a href="../mis_eventos.php">Eventos</a></li>
                        <li><a href="#">Políticas</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Sistema de Inscripciones. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para formatear fechas
    function formatearFecha(fecha) {
        if (!fecha) return 'Sin fecha';
        try {
            const fechaObj = new Date(fecha);
            return fechaObj.toLocaleDateString('es-ES', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch {
            return fecha;
        }
    }

    // Cargar eventos disponibles
    fetch('./get_eventos_disponibles.php')
        .then(response => {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    throw new Error(`Respuesta no es JSON: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            const selectEvento = document.getElementById('evento');
            selectEvento.innerHTML = '<option value="">Seleccione un evento...</option>';

            data.forEach(evento => {
                const option = document.createElement('option');
                option.value = evento.codigo;
                option.textContent = `${evento.titulo} (${formatearFecha(evento.fechainicio)})`;
                option.setAttribute('data-tipo', evento.tipo_evento.toLowerCase());
                option.setAttribute('data-costo', evento.costo);
                selectEvento.appendChild(option);
            });

            // Event listener para el cambio de evento
            selectEvento.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const tipo = selected.getAttribute('data-tipo');
                const costo = selected.getAttribute('data-costo');
                const infoPago = document.getElementById('info_pago');
                const inputTipo = document.getElementById('tipo_evento');
                const inputCosto = document.getElementById('costo');
                const estadoPago = document.getElementById('estado_pago');
                const comprobante = document.getElementById('comprobante_group');
                const infoGratis = document.getElementById('info_gratis');

                if (!tipo) {
                    infoPago.style.display = 'none';
                    return;
                }

                infoPago.style.display = 'block';
                inputTipo.value = tipo;
                inputCosto.value = costo;

                if (tipo === 'pagado') {
                    comprobante.style.display = 'block';
                    infoGratis.style.display = 'none';
                    estadoPago.value = 'Pendiente';
                } else {
                    comprobante.style.display = 'none';
                    infoGratis.style.display = 'block';
                    estadoPago.value = 'Pagado';
                }
            });
        })
        .catch(error => {
            console.error('Error al cargar eventos:', error);
            const selectEvento = document.getElementById('evento');
            selectEvento.innerHTML = '<option value="">Error al cargar eventos</option>';
            
            const option = document.createElement('option');
            option.textContent = 'Error: ' + error.message;
            option.disabled = true;
            selectEvento.appendChild(option);
        });

    // Establecer fecha actual
    document.getElementById('fecha_inscripcion').valueAsDate = new Date();

    // Manejar envío del formulario
    document.getElementById('formInscripcion').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const evento = document.getElementById('evento').value;
        if (!evento) {
            alert('Por favor seleccione un evento');
            return;
        }
        
        // Mostrar indicador de carga (opcional)
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Procesando...';
        submitBtn.disabled = true;
        
        // Recolectar todos los datos del formulario
        const formData = new FormData(this);
        
        // Enviar datos al servidor con headers específicos para AJAX
        fetch('procesar_inscripcion.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // Si no es JSON, puede ser una redirección o HTML
                return response.text().then(text => {
                    // Si contiene éxito en el texto, asumimos que fue exitoso
                    if (response.ok && (text.includes('success') || response.status === 200)) {
                        return { success: true, message: 'Inscripción realizada exitosamente' };
                        showSuccessModal();
                    } else {
                        throw new Error(text || 'Error en el servidor');
                    }
                });
            }
        })
        .then(data => {
            // Restaurar botón
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            if (data.success) {
                showSuccessModal();
            } else {
                throw new Error(data.error || 'Error desconocido');
            }
        })
        .catch(error => {
            // Restaurar botón
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
            
            console.error('Error:', error);
            alert('Ocurrió un error al procesar la inscripción: ' + error.message);
        });
    });

    // Función para mostrar el modal de éxito
    function showSuccessModal() {
        const modal = document.getElementById('successModal');
        modal.style.display = 'flex';
        
        // Cerrar modal al hacer clic en la X
        document.querySelector('.close-modal').onclick = function() {
            modal.style.display = 'none';
            window.location.reload();
        };
        
        // Cerrar modal al hacer clic en el botón Aceptar
        document.getElementById('modalCloseBtn').onclick = function() {
            modal.style.display = 'none';
            window.location.reload();
        };
        
        // Cerrar modal al hacer clic fuera del contenido
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
                window.location.reload();
            }
        };
    }

    // Botón regresar
    document.getElementById('btnRegresar').addEventListener('click', function() {
        const form = document.getElementById('formInscripcion');
        let isModified = false;

        Array.from(form.elements).forEach(el => {
            if ((el.tagName === 'INPUT' || el.tagName === 'SELECT') && 
                el.type !== 'submit' && 
                el.type !== 'button' && 
                el.id !== 'cedula') {
                if (el.value.trim() !== '') {
                    isModified = true;
                }
            }
        });

        if (isModified) {
            if (confirm("¿Estás seguro? Tus cambios se perderán.")) {
                window.location.href = '../inicio.php';
            }
        } else {
            window.location.href = '../inicio.php';
        }
    });
});
    </script>
</body>
</html>