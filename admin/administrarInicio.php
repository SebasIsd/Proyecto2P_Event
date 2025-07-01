<?php
session_start();
require_once '../includes/conexion1.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header('Location: ./usuarios/login.php');
    exit;
}

$db = new Conexion();
$conn = $db->getConexion();

// Traer slides carrusel
$result = pg_query($conn, "SELECT * FROM carrusel ORDER BY id");
$slides = pg_fetch_all($result);

// Traer autoridades
$result2 = pg_query($conn, "SELECT * FROM autoridades ORDER BY id");
$autoridades = pg_fetch_all($result2);

// Traer contacto (asumimos solo 1 fila)
$result3 = pg_query($conn, "SELECT * FROM contacto LIMIT 1");
$contacto = pg_fetch_assoc($result3);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --primary: #8B0000;
            --secondary: #D32F2F;
            --accent: #FF5722;
            --light: #F5F5F5;
            --dark: #212121;
            --gray: #757575;
            --card-shadow: 0 4px 8px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0; padding: 0;
            background-color: var(--light);
            color: var(--dark);
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 1rem 2rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            margin-bottom: 1rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto 3rem auto;
            padding: 2rem;
            background: white;
            box-shadow: var(--card-shadow);
            border-radius: 8px;
        }

        h1 {
            color: var(--primary);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        form {
            margin-bottom: 2rem;
            border: 1px solid #ddd;
            padding: 1.5rem;
            border-radius: 8px;
            background-color: var(--light);
            box-shadow: var(--card-shadow);
        }

        label {
            font-weight: 500;
            display: block;
            margin-top: 0.8rem;
        }

        input[type="text"], input[type="email"], textarea {
            width: 100%;
            padding: 0.5rem;
            margin-top: 0.3rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-family: inherit;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        .btn {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 1rem;
        }

        .btn:hover {
            background-color: #E64A19;
            transform: translateY(-2px);
        }

        /* Navegación simple */
        .nav-tabs {
            display: flex;
            justify-content: center;
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
            border-bottom: 2px solid var(--primary);
        }

        .nav-tabs li {
            margin: 0 1rem;
        }

        .nav-tabs a {
            text-decoration: none;
            font-weight: 600;
            color: var(--primary);
            padding-bottom: 0.3rem;
            display: inline-block;
            border-bottom: 3px solid transparent;
            transition: border-color 0.3s ease;
        }

        .nav-tabs a.active, .nav-tabs a:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .seccion-admin {
            display: none;
        }

        .seccion-admin.active {
            display: block;
        }

        img.preview-img {
            max-width: 50%;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>

<header class="admin-header">
    <h2>Panel de Administración</h2>
</header>

<div class="container">

    <ul class="nav-tabs" id="tabs">
        <li><a href="#" data-target="carrusel" class="active">Carrusel</a></li>
        <li><a href="#" data-target="autoridades">Autoridades</a></li>
        <li><a href="#" data-target="contacto">Contacto</a></li>
    </ul>

    <!-- CARRUSEL -->
    <div id="carrusel" class="seccion-admin active">
        <h1>Editar Slides del Carrusel</h1>
        <?php if ($slides): ?>
            <?php foreach ($slides as $slide): ?>
                <form method="POST" action="actualizar_slide.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($slide['id']) ?>">
                    <label>Título</label>
                    <input type="text" name="titulo" value="<?= htmlspecialchars($slide['titulo']) ?>" required>

                    <label>Descripción</label>
                    <textarea name="descripcion" rows="3" required><?= htmlspecialchars($slide['descripcion']) ?></textarea>

                    <label>Enlace</label>
                    <input type="text" name="imagen_url" value="<?= htmlspecialchars($slide['imagen_url']) ?>">

                    <label>URL Imagen (opcional si subes imagen)</label>
                    <input type="text" name="link_url" value="<?= htmlspecialchars($slide['link_url']) ?>">

                    <label>Subir Imagen (JPG/PNG)</label>
                    <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewSlide<?= $slide['id'] ?>'); limpiarURL(this)">
                    <br><br>
                    <img id="previewSlide<?= $slide['id'] ?>" class="preview-img" src="<?= htmlspecialchars($slide['link_url']) ?>" alt="Vista previa">
                    <br><br>
                    <button type="submit" class="btn">Guardar Cambios</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron slides.</p>
        <?php endif; ?>
    </div>

    <!-- AUTORIDADES -->
    <div id="autoridades" class="seccion-admin">
        <h1>Editar Autoridades</h1>
        <?php if ($autoridades): ?>
            <?php foreach ($autoridades as $auth): ?>
                <form method="POST" action="actualizar_autoridades.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($auth['id']) ?>">
                    <label>Nombre</label>
                    <input type="text" name="nombre" value="<?= htmlspecialchars($auth['nombre']) ?>" required>

                    <label>Cargo</label>
                    <input type="text" name="cargo" value="<?= htmlspecialchars($auth['cargo']) ?>" required>

                    <label>Dependencia</label>
                    <input type="text" name="dependencia" value="<?= htmlspecialchars($auth['dependencia']) ?>" required>

                    <label>URL Imagen (opcional si subes imagen)</label>
                    <input type="text" name="imagen_url" value="<?= htmlspecialchars($auth['imagen_url']) ?>">

                    <label>Subir Imagen (JPG/PNG)</label>
                    <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewAuth<?= $auth['id'] ?>'); limpiarURL(this)">
                    <br><br>
                    <img id="previewAuth<?= $auth['id'] ?>" class="preview-img" src="<?= htmlspecialchars($auth['imagen_url']) ?>" alt="Vista previa">
                    <br><br>
                    <button type="submit" class="btn">Guardar Cambios</button>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No se encontraron autoridades.</p>
        <?php endif; ?>
    </div>

    <!-- CONTACTO -->
    <div id="contacto" class="seccion-admin">
        <h1>Editar Información de Contacto</h1>
        <?php if ($contacto): ?>
            <form method="POST" action="actualizar_contacto.php">
                <input type="hidden" name="id" value="<?= htmlspecialchars($contacto['id']) ?>">

                <label>Dirección</label>
                <textarea name="direccion" rows="2" required><?= htmlspecialchars($contacto['direccion']) ?></textarea>

                <label>Teléfono</label>
                <input type="text" name="telefono" value="<?= htmlspecialchars($contacto['telefono']) ?>" required>

                <label>Correo Electrónico</label>
                <input type="email" name="correo" value="<?= htmlspecialchars($contacto['correo']) ?>" required>
                    <br><br>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        <?php else: ?>
            <p>No se encontró información de contacto.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    // Cambio de pestañas
    const tabs = document.querySelectorAll('#tabs a');
    const sections = document.querySelectorAll('.seccion-admin');

    tabs.forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            sections.forEach(sec => sec.classList.remove('active'));
            const target = tab.getAttribute('data-target');
            document.getElementById(target).classList.add('active');
        });
    });

    // Vista previa para imágenes
    function mostrarVistaPrevia(input, idImagen) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(idImagen).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Limpiar campo URL si se sube archivo
    function limpiarURL(input) {
        const urlField = input.parentElement.querySelector('input[name="imagen_url"]');
        if (urlField) {
            urlField.value = '';
        }
    }
</script>

</body>
</html>
