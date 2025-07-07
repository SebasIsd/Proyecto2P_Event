<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: usuarios/login.php");
    exit();
}

require_once "../includes/conexion1.php";

$conexion = new Conexion();
$conn = $conexion->getConexion();

$cedula = $_SESSION['cedula'];

// Obtener más datos del usuario para el perfil completo
$sql = "SELECT nom_pri_usu, nom_seg_usu, ape_pri_usu, ape_seg_usu, car_usu,
               cor_usu, tel_usu, dir_usu, fec_nac_usu
        FROM usuarios
        WHERE ced_usu = $1";
$result = pg_query_params($conn, $sql, array($cedula));

if ($usuario = pg_fetch_assoc($result)) {
    $nombreCompleto = $usuario['nom_pri_usu'] . ' ' . $usuario['nom_seg_usu'] . ' ' . $usuario['ape_pri_usu'] . ' ' . $usuario['ape_seg_usu'];
    $carrera = $usuario['car_usu'];
    $fechaNacimiento = !empty($usuario['fec_nac_usu']) ? date('d/m/Y', strtotime($usuario['fec_nac_usu'])) : 'No especificada';
} else {
    $nombreCompleto = "Usuario desconocido";
    $carrera = "Carrera no disponible";
    $fechaNacimiento = 'No disponible';
}

// --- NEW CODE FOR CERTIFICATES ---
$eventosConCertificado = [];
$sqlCertificados = "
SELECT
    e.tit_eve_cur, -- Event name
    c.html_generado AS ruta_certificado -- Path to the generated PDF
FROM
    inscripciones i
JOIN
    eventos_cursos e ON i.id_eve_cur = e.id_eve_cur
JOIN
    certificados c ON i.id_ins = c.id_ins -- Join on id_ins from inscriptions to certificates
WHERE
    i.ced_usu = $1 AND c.html_generado IS NOT NULL
ORDER BY
    e.tit_eve_cur";
$resultCertificados = pg_query_params($conn, $sqlCertificados, array($cedula));

if ($resultCertificados) {
    while ($row = pg_fetch_assoc($resultCertificados)) {
        $eventosConCertificado[] = $row;
    }
}
// --- END NEW CODE FOR CERTIFICATES ---

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Mi Perfil - Sistema de Inscripciones</title>
    <link rel="stylesheet" href="../styles/css/perfil.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <link rel="stylesheet" href="../styles/css/componente.css">
    <style>
        /* Estilos para el modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.7); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 90%;
            max-width: 800px;
            height: 90%;
            display: flex;
            flex-direction: column;
            border-radius: 8px;
            position: relative;
        }

        .close-button {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: black;
            text-decoration: none;
        }

        .modal-body {
            flex-grow: 1;
            overflow: hidden; /* Hide overflow from iframe */
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .modal-body iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Existing styles for certificate list */
        .certificate-list ul {
            list-style: none;
            padding: 0;
            margin: 0; /* Remove default margin */
        }

        .certificate-list li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .certificate-list li span {
            font-weight: 600;
            color: #333;
        }

        .certificate-list li a {
            background-color: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9em;
            transition: background-color 0.3s ease;
        }

        .certificate-list li a:hover {
            background-color: #0056b3;
        }

        /* NEW: Styles for scrollable certificate list */
        .certificate-list-container {
            max-height: 300px; /* Set a maximum height for the container */
            overflow-y: auto; /* Enable vertical scrolling */
            padding-right: 10px; /* Add some padding for the scrollbar */
        }
    </style>

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
                <h1 class="profile-name"><?= htmlspecialchars($nombreCompleto) ?></h1>
                <p class="profile-title"><?= htmlspecialchars($carrera) ?></p>
            </div>
            <a href="editar_perfil.php" class="profile-edit-btn">
                <i class="fas fa-user-edit"></i> Editar Perfil
            </a>
        </div>

        <div class="profile-details">
            <div class="profile-detail-section">
                <h3><i class="fas fa-id-card"></i> Información Personal</h3>
                <p><strong>Cédula:</strong> <?= htmlspecialchars($cedula) ?></p>
                <p><strong>Fecha de Nacimiento:</strong> <?= htmlspecialchars($fechaNacimiento) ?></p>
            </div>

            <div class="profile-detail-section">
                <h3><i class="fas fa-graduation-cap"></i> Información Académica</h3>
                <p><strong>Carrera:</strong> <?= htmlspecialchars($carrera) ?></p>
            </div>

            <div class="profile-detail-section">
                <h3><i class="fas fa-address-book"></i> Contacto</h3>
                <p><strong>Email:</strong> <?= !empty($usuario['cor_usu']) ? htmlspecialchars($usuario['cor_usu']) : 'No especificado' ?></p>
                <p><strong>Teléfono:</strong> <?= !empty($usuario['tel_usu']) ? htmlspecialchars($usuario['tel_usu']) : 'No especificado' ?></p>
                <p><strong>Dirección:</strong> <?= !empty($usuario['dir_usu']) ? htmlspecialchars($usuario['dir_usu']) : 'No especificada' ?></p>
            </div>

            <div class="profile-detail-section certificate-list">
                <h3><i class="fas fa-certificate"></i> Eventos con Certificado</h3>
                <div class="certificate-list-container"> <?php if (!empty($eventosConCertificado)): ?>
                        <ul>
                            <?php foreach ($eventosConCertificado as $evento): ?>
                                <li>
                                    <span><?= htmlspecialchars($evento['tit_eve_cur']) ?></span>
                                    <a href="#" class="view-certificate-btn" data-pdf-url="<?= htmlspecialchars($evento['ruta_certificado']) ?>">Ver Certificado</a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No tienes certificados disponibles en este momento.</p>
                    <?php endif; ?>
                </div>
            </div>
            </div>
    </div>
</section>

    </main>

   <?php include "../includes/footer.php"; ?>

    <div id="certificateModal" class="modal">
      <div class="modal-content">
        <span class="close-button">&times;</span>
        <div class="modal-body">
          <iframe id="pdfViewer" src="" frameborder="0"></iframe>
        </div>
      </div>
    </div>

    <script src="/styles/script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('certificateModal');
            const closeButton = document.querySelector('.close-button');
            const pdfViewer = document.getElementById('pdfViewer');
            const viewCertificateButtons = document.querySelectorAll('.view-certificate-btn');

            viewCertificateButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const pdfUrl = this.dataset.pdfUrl;
                    if (pdfUrl) {
                        pdfViewer.src = pdfUrl;
                        modal.style.display = 'flex'; // Use flex to center the modal
                    } else {
                        alert('La ruta del certificado no está disponible.');
                    }
                });
            });

            closeButton.addEventListener('click', function() {
                modal.style.display = 'none';
                pdfViewer.src = ''; // Clear the iframe src when closing
            });

            // Close the modal if the user clicks anywhere outside of the modal content
            window.addEventListener('click', function(event) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                    pdfViewer.src = ''; // Clear the iframe src when closing
                }
            });
        });
    </script>
</body>
</html>