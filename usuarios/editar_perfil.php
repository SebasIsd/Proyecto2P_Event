    <?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

$cedula = $_SESSION['cedula'];

// Obtener datos actuales del usuario
$sql = "SELECT nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, car_usu, 
               cor_usu, tel_usu, dir_usu, fec_nac_usu
        FROM usuarios 
        WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, array($cedula));
$usuario = pg_fetch_assoc($result);

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recoger y sanitizar los datos del formulario
    $nombres = [
        'nom_pri_usu' => pg_escape_string($conn, $_POST['nom_pri_usu']),
        'nom_seg_usu' => pg_escape_string($conn, $_POST['nom_seg_usu']),
        'ape_pri_usu' => pg_escape_string($conn, $_POST['ape_pri_usu']),
        'ape_seg_usu' => pg_escape_string($conn, $_POST['ape_seg_usu'])
    ];
    
    $contacto = [
        'cor_usu' => pg_escape_string($conn, $_POST['cor_usu']),
        'tel_usu' => pg_escape_string($conn, $_POST['tel_usu']),
        'dir_usu' => pg_escape_string($conn, $_POST['dir_usu'])
    ];
    
    $otros = [
        'car_usu' => pg_escape_string($conn, $_POST['car_usu']),
        'fec_nac_usu' => !empty($_POST['fec_nac_usu']) ? pg_escape_string($conn, $_POST['fec_nac_usu']) : null
    ];
    
    // Convertir fecha al formato PostgreSQL (YYYY-MM-DD)
    if ($otros['fec_nac_usu']) {
        $date = DateTime::createFromFormat('d/m/Y', $otros['fec_nac_usu']);
        if ($date) {
            $otros['fec_nac_usu'] = $date->format('Y-m-d');
        } else {
            $otros['fec_nac_usu'] = null;
        }
    }
    
    // Actualizar los datos en la base de datos
    $update_sql = "UPDATE usuarios SET 
                    nom_pri_usu = $1, 
                    nom_seg_usu = $2, 
                    ape_pri_usu = $3, 
                    ape_seg_usu = $4, 
                    car_usu = $5, 
                    cor_usu = $6, 
                    tel_usu = $7, 
                    dir_usu = $8, 
                    fec_nac_usu = $9 
                   WHERE ced_usu = $10";
    
    $params = [
        $nombres['nom_pri_usu'],
        $nombres['nom_seg_usu'],
        $nombres['ape_pri_usu'],
        $nombres['ape_seg_usu'],
        $otros['car_usu'],
        $contacto['cor_usu'],
        $contacto['tel_usu'],
        $contacto['dir_usu'],
        $otros['fec_nac_usu'],
        $cedula
    ];
    
    $result = pg_query_params($conn, $update_sql, $params);
    
    if ($result) {
        $_SESSION['mensaje'] = "Perfil actualizado correctamente";
        header("Location: perfil.php");
        exit();
    } else {
        $error = "Error al actualizar el perfil: " . pg_last_error($conn);
    }
}

// Formatear fecha para mostrarla en el formulario
$fecha_nacimiento_formatted = !empty($usuario['fec_nac_usu']) ? date('d/m/Y', strtotime($usuario['fec_nac_usu'])) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editar Perfil - Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/perfil.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="../styles/css/componente.css">

</head>
<body>
   <?php include "../includes/header.php"; ?>


    <main class="container">
        <section class="profile-section">
            <div class="profile-card">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="profile-info">
                        <h1 class="profile-name">Editar Perfil</h1>
                        <p class="profile-title">Actualiza tu información personal</p>
                    </div>
                    <a href="perfil.php" class="profile-edit-btn">
                        <i class="fas fa-arrow-left"></i> Volver al Perfil
                    </a>
                </div>
                
                <div class="profile-details">
                    <?php if (isset($error)): ?>
                        <div class="error-message" style="color: red; margin-bottom: 20px;">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="editar_perfil.php">
                        <div class="profile-detail-section">
                            <h3><i class="fas fa-id-card"></i> Información Personal</h3>
                            
                            <div class="form-group">
                                <label for="nom_pri_usu">Primer Nombre:</label>
                                <input type="text" id="nom_pri_usu" name="nom_pri_usu" 
                                       value="<?= htmlspecialchars($usuario['nom_pri_usu']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="nom_seg_usu">Segundo Nombre:</label>
                                <input type="text" id="nom_seg_usu" name="nom_seg_usu" 
                                       value="<?= htmlspecialchars($usuario['nom_seg_usu']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="ape_pri_usu">Primer Apellido:</label>
                                <input type="text" id="ape_pri_usu" name="ape_pri_usu" 
                                       value="<?= htmlspecialchars($usuario['ape_pri_usu']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="ape_seg_usu">Segundo Apellido:</label>
                                <input type="text" id="ape_seg_usu" name="ape_seg_usu" 
                                       value="<?= htmlspecialchars($usuario['ape_seg_usu']) ?>">
                            </div>
                            
                           <div class="form-group">
                                <label for="fec_nac_usu">Fecha de Nacimiento:</label>
                                <input type="text" id="fec_nac_usu" name="fec_nac_usu" 
                                     value="<?= htmlspecialchars($fecha_nacimiento_formatted) ?>" 
                                    placeholder="No editable" readonly>
                            </div>
                        </div>
                        
                        <div class="profile-detail-section">
                            <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
                            
                            <div class="form-group">
                                <label for="car_usu">Carrera:</label>
                                <input type="text" id="car_usu" name="car_usu" 
                                       value="<?= htmlspecialchars($usuario['car_usu']) ?>" required>
                            </div>
                        </div>
                        
                        <div class="profile-detail-section">
                            <h3><i class="fas fa-address-book"></i> Contacto</h3>
                            
                            <div class="form-group">
                                <label for="cor_usu">Email:</label>
                                <input type="email" id="cor_usu" name="cor_usu" 
                                       value="<?= htmlspecialchars($usuario['cor_usu']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="tel_usu">Teléfono:</label>
                                <input type="text" id="tel_usu" name="tel_usu" 
                                       value="<?= htmlspecialchars($usuario['tel_usu']) ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="dir_usu">Dirección:</label>
                                <textarea id="dir_usu" name="dir_usu"><?= htmlspecialchars($usuario['dir_usu']) ?></textarea>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="save-btn">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                            <a href="perfil.php" class="cancel-btn">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

   <?php include "../includes/footer.php"; ?>


    <script src="../styles/script.js"></script>
    <script>
        // Validación de fecha de nacimiento
        document.getElementById('fec_nac_usu').addEventListener('blur', function() {
            const fechaInput = this.value;
            if (fechaInput) {
                const regex = /^\d{2}\/\d{2}\/\d{4}$/;
                if (!regex.test(fechaInput)) {
                    alert('Por favor ingrese la fecha en formato dd/mm/aaaa');
                    this.focus();
                }
            }
        });
    </script>
</body>
</html>