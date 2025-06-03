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
            <!-- Opciones se llenarán con JavaScript -->
        </select>
    </div>
    
    <div class="form-row">
        <div class="form-group">
            <label for="fecha_inscripcion">Fecha de Inscripción</label>
            <input type="date" id="fecha_inscripcion" name="fecha_inscripcion" class="form-control" required>
        </div>
    </div>
    
    <div class="form-group">
        <label for="estado_pago">Estado de Pago</label>
        <select id="estado_pago" name="estado_pago" class="form-control" required>
            <option value="">Seleccione...</option>
            <option value="Pendiente">Pendiente</option>
            <option value="Pagado">Pagado</option>
        </select>
    </div>
    
    <div id="seccion_pago" style="display: none;">
        <h3>Información de Pago</h3>
        <div class="form-row">
            <div class="form-group">
                <label for="fecha_pago">Fecha de Pago</label>
                <input type="date" id="fecha_pago" name="fecha_pago" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="monto_pago">Monto</label>
                <input type="number" step="0.01" id="monto_pago" name="monto_pago" class="form-control">
            </div>
        </div>
        
        <div class="form-group">
            <label for="metodo_pago">Método de Pago</label>
            <input type="text" id="metodo_pago" name="metodo_pago" class="form-control">
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
                if (data.error) {
                    throw new Error(data.error);
                }
                
                const selectEvento = document.getElementById('evento');
                selectEvento.innerHTML = '<option value="">Seleccione un evento...</option>';
                
                if (data.length === 0) {
                    const option = document.createElement('option');
                    option.textContent = 'No hay eventos disponibles actualmente';
                    option.disabled = true;
                    selectEvento.appendChild(option);
                } else {
                    data.forEach(evento => {
                        const fechaInicio = formatearFecha(evento.fechainicio);
                        const fechaFin = evento.fechafin ? formatearFecha(evento.fechafin) : null;
                        const fechaTexto = (fechaFin && fechaFin !== fechaInicio) ? `${fechaInicio} - ${fechaFin}` : fechaInicio;
                        const option = document.createElement('option');
                        option.value = evento.codigo;
                        // Usar la función formatearFecha para mostrar las fechas
                        option.textContent = `${evento.titulo} (${fechaTexto})`;
                        selectEvento.appendChild(option);
                    });
                }
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

        // Resto del código permanece igual...
        document.getElementById('estado_pago').addEventListener('change', function() {
            const seccionPago = document.getElementById('seccion_pago');
            seccionPago.style.display = this.value === 'Pagado' ? 'block' : 'none';
            
            if (this.value === 'Pagado') {
                document.getElementById('fecha_pago').valueAsDate = new Date();
            }
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