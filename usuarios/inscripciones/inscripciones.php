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
    <style>
        /* Estilos para el Modal */
        .modal {
            display: none; /* Oculto por defecto */
            position: fixed; /* Posición fija para cubrir toda la pantalla */
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto; /* Habilitar scroll si el contenido es grande */
            background-color: rgba(0,0,0,0.4); /* Fondo semi-transparente */
            justify-content: center; /* Centrar horizontalmente */
            align-items: center; /* Centrar verticalmente */
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            width: 90%;
            max-width: 400px;
            text-align: center;
            position: relative;
            animation: fadeIn 0.3s ease-out; /* Animación de aparición */
        }

        /* Animación de entrada */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .close-modal {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-modal:hover,
        .close-modal:focus {
            color: #000;
            text-decoration: none;
        }

        .modal-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }

        .modal-icon.success {
            color: #28a745; /* Color verde para éxito */
        }

        .modal-icon.error {
            color: #dc3545; /* Color rojo para error */
        }

        .modal-content h3 {
            color: #333;
            margin-bottom: 0.8rem;
            font-size: 1.8rem;
        }

        .modal-content p {
            color: #666;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn-modal {
            background-color: var(--primary-color); 
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .btn-modal:hover {
            background-color: #0056b3; 
        }
    </style>
</head>
<body>
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
                    showErrorModal('Por favor seleccione un evento.');
                    return;
                }
                
                // Mostrar indicador de carga
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Procesando...';
                submitBtn.disabled = true;
                
                // Recolectar todos los datos del formulario
                const formData = new FormData(this);
                
                console.log('Enviando datos del formulario...');
                
                // Enviar datos al servidor con headers específicos para AJAX
                fetch('procesar_inscripcion.php', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    
                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json().then(data => {
                            console.log('JSON response:', data);
                            return { isJson: true, data: data, status: response.status };
                        });
                    } else {
                        // Si no es JSON, obtener el texto
                        return response.text().then(text => {
                            console.log('Text response:', text);
                            console.log('Response OK:', response.ok);
                            return { isJson: false, data: text, status: response.status };
                        });
                    }
                })
                .then(result => {
                    console.log('Resultado procesado:', result);
                    
                    // Restaurar botón
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    
                    if (result.isJson) {
                        // Respuesta JSON
                        if (result.data.success) {
                            console.log('Éxito detectado en JSON, mostrando modal');
                            showSuccessModal();
                        } else if (result.data.error) {
                            console.log('Error en JSON:', result.data.error);
                            showErrorModal(result.data.error);
                        } else {
                            console.log('Respuesta JSON desconocida:', result.data);
                            showErrorModal('Respuesta inesperada del servidor.');
                        }
                    } else {
                        // Respuesta de texto
                        if (result.status >= 200 && result.status < 300) { // Considerar 2xx como exitoso
                            console.log('Respuesta exitosa (texto), mostrando modal');
                            showSuccessModal();
                        } else {
                            console.log('Error en respuesta de texto:', result.data);
                            showErrorModal('Error: ' + (result.data || 'Error desconocido al procesar la inscripción.'));
                        }
                    }
                })
                .catch(error => {
                    console.error('Error en fetch:', error);
                    
                    // Restaurar botón
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                    
                    showErrorModal('Ocurrió un error al procesar la inscripción: ' + error.message);
                });
            });

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

        function showSuccessModal() {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-modal" onclick="this.parentElement.parentElement.remove()">&times;</span>
                    <div class="modal-icon success">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>¡Inscripción exitosa!</h3>
                    <p>La inscripción se ha registrado correctamente.</p>
                    <button class="btn-modal" onclick="window.location.href='../inscripciones/inscripciones.php'">Aceptar</button>
                </div>
            `;
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        }

        function showErrorModal(message) {
            const modal = document.createElement('div');
            modal.className = 'modal';
            modal.innerHTML = `
                <div class="modal-content">
                    <span class="close-modal" onclick="this.parentElement.parentElement.remove()">&times;</span>
                    <div class="modal-icon error">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <h3>¡Error!</h3>
                    <p>${message}</p>
                    <button class="btn-modal" onclick="this.parentElement.parentElement.remove()">Cerrar</button>
                </div>
            `;
            document.body.appendChild(modal);
            modal.style.display = 'flex';
        }
    </script>
</body>
</html>