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

                // Dentro del event listener del cambio de selectEvento
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
                    estadoPago.value = 'Pendiente'; // Estado PENDIENTE para eventos pagados
                } else {
                    comprobante.style.display = 'none';
                    infoGratis.style.display = 'block';
                    estadoPago.value = 'Pagado'; // Estado PAGADO automático para eventos gratis
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

        document.getElementById('fecha_inscripcion').valueAsDate = new Date();

        document.getElementById('formInscripcion').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const evento = document.getElementById('evento').value;
            if (!evento) {
                alert('Por favor seleccione un evento');
                return;
            }
            
            // Recolectar todos los datos del formulario
            const formData = new FormData(this);
            
            // Enviar datos al servidor
            fetch('procesar_inscripcion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    return response.text();
                }
            })
            .then(data => {
                if (data) {
                    // Manejar respuesta si no hubo redirección
                    console.log(data);
                }
            })
            .then(text => {
                try {
                    // Intentar parsear como JSON por si acaso
                    const data = JSON.parse(text);
                    console.log(data);
                } catch {
                    // Si no es JSON, mostrar el texto plano
                    console.log(text);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ocurrió un error al procesar la inscripción');
            });
        });

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