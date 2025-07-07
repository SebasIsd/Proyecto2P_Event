<?php
session_start();
require_once '../includes/conexion1.php';

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

$sobreNosotros = pg_query($conn, "SELECT * FROM sobre_nosotros ORDER BY id DESC LIMIT 1");
$dataSobre = pg_fetch_assoc($sobreNosotros);

// Traer desarrolladores
$resultDesarrolladores = pg_query($conn, "SELECT * FROM desarrolladores ORDER BY id");
$desarrolladores = pg_fetch_all($resultDesarrolladores);
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
            --primary: #7c2020;
            --secondary: #5e1818;
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

        /* Estilos generales para botones */
        .btn {
            background-color: var(--accent);
            color: white;
            border: none;
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease; /* Transición para todos los cambios */
            margin-top: 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1); /* Sombra para efecto 3D */
            background-image: linear-gradient(to bottom right, var(--accent), #E64A19); /* Degradado */
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 10px rgba(0,0,0,0.2); /* Sombra más pronunciada al pasar el ratón */
            background-image: linear-gradient(to bottom right, #E64A19, var(--accent)); /* Degradado invertido o diferente */
        }

        .btn:active {
            transform: translateY(0); /* Vuelve a la posición original al hacer click */
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Sombra más pequeña al hacer click */
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

        /* Estilos para la tabla de desarrolladores */
        .developer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
        }

        .developer-table th, .developer-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .developer-table th {
            background-color: var(--primary);
            color: white;
        }

        .developer-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .developer-table .actions {
            display: flex;
            gap: 10px; /* Aumentado el espacio entre botones */
            justify-content: center; /* Centrar los botones en la celda */
            align-items: center;
        }

        .developer-table .actions .btn {
            padding: 8px 15px; /* Aumentado el padding para hacerlos más grandes */
            font-size: 0.9rem; /* Ajustado el tamaño de la fuente */
            margin-top: 0; /* Eliminar el margen superior extra */
            border-radius: 20px; /* Bordes más redondeados */
            box-shadow: 0 3px 5px rgba(0,0,0,0.2); /* Sombra para los botones de acción */
            text-transform: uppercase; /* Texto en mayúsculas */
            letter-spacing: 0.5px; /* Espaciado entre letras */
        }

        /* Estilo específico para el botón de Editar */
        .developer-table .actions .btn-edit {
            background-image: linear-gradient(to bottom right, #4CAF50, #66BB6A); /* Degradado verde */
        }

        .developer-table .actions .btn-edit:hover {
            background-image: linear-gradient(to bottom right, #66BB6A, #4CAF50);
        }

        /* Estilo específico para el botón de Eliminar */
        .developer-table .actions .btn-delete {
            background-image: linear-gradient(to bottom right, #f44336, #ef5350); /* Degradado rojo */
        }

        .developer-table .actions .btn-delete:hover {
            background-image: linear-gradient(to bottom right, #ef5350, #f44336);
        }

        .developer-image-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
        }

        /* Estilos para los modales */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1000; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgba(0,0,0,0.6); /* Black w/ opacity */
            justify-content: center;
            align-items: center;
            padding-top: 50px;
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border: 1px solid #888;
            width: 90%;
            max-width: 700px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
            animation: slideIn 0.3s ease-out;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .close-button:hover,
        .close-button:focus {
            color: var(--primary);
            text-decoration: none;
            cursor: pointer;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Estilos para el botón de "Agregar Nuevo" en cada sección */
        .add-new-btn-container {
            text-align: right;
            margin-bottom: 20px;
        }
        .add-new-btn {
            background-color: #007bff; /* Azul para agregar */
            background-image: linear-gradient(to bottom right, #007bff, #0056b3);
            border-radius: 20px;
            padding: 10px 20px;
            font-size: 1rem;
            box-shadow: 0 3px 5px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .add-new-btn:hover {
            background-image: linear-gradient(to bottom right, #0056b3, #007bff);
            transform: translateY(-2px);
            box-shadow: 0 5px 10px rgba(0,0,0,0.3);
        }
    .btn-back:hover {
    background-color:rgb(226, 168, 168);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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
    <li><a href="#" data-target="dataSobre">Sobre Nosotros</a></li>
    <li><a href="#" data-target="desarrolladores">Desarrolladores</a></li>
</ul>

    <!-- CARRUSEL -->
    <div id="carrusel" class="seccion-admin active">
        <h1>Editar Slides del Carrusel</h1>
        <div class="add-new-btn-container">
            <button class="btn add-new-btn" onclick="openModal('carrusel-add-modal')"><i class="fas fa-plus-circle"></i> Agregar Nuevo Slide</button>
        </div>
        <?php if ($slides): ?>
            <table class="developer-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slides as $slide): ?>
                        <tr>
                            <td><?= htmlspecialchars($slide['id']) ?></td>
                            <td><?= htmlspecialchars($slide['titulo']) ?></td>
                            <td><?= htmlspecialchars(substr($slide['descripcion'], 0, 50)) . (strlen($slide['descripcion']) > 50 ? '...' : '') ?></td>
                            <td>
                                <?php if (!empty($slide['imagen_url'])): ?>
                                    <img src="<?= htmlspecialchars($slide['imagen_url']) ?>" alt="Imagen de Slide" class="developer-image-thumb">
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <button class="btn btn-edit" onclick="openEditModal('carrusel-edit-modal-<?= $slide['id'] ?>', <?= htmlspecialchars(json_encode($slide)) ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST" action="acciones_carrusel.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este slide?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($slide['id']) ?>">
                                    <button type="submit" class="btn btn-delete">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron slides.</p>
        <?php endif; ?>
    </div>

    <!-- Modal para agregar Carrusel -->
    <div id="carrusel-add-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('carrusel-add-modal')">&times;</span>
            <h2>Agregar Nuevo Slide de Carrusel</h2>
            <form method="POST" action="acciones_carrusel.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label>Título</label>
                <input type="text" name="titulo" id="add-carrusel-titulo" required>
                <label>Descripción</label>
                <textarea name="descripcion" id="add-carrusel-descripcion" rows="3" required></textarea>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="add-carrusel-imagen_url">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewAddCarrusel'); limpiarURL(this, 'add-carrusel-imagen_url')">
                <br><br>
                <img id="previewAddCarrusel" class="preview-img" src="" alt="Vista previa">
                <br><br>
                <label>Enlace</label>
                <input type="text" name="link_url" id="add-carrusel-link_url">
                <button type="submit" class="btn">Agregar Slide</button>
            </form>
        </div>
    </div>

    <!-- Modales para editar Carrusel (uno por cada slide) -->
    <?php foreach ($slides as $slide): ?>
    <div id="carrusel-edit-modal-<?= $slide['id'] ?>" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('carrusel-edit-modal-<?= $slide['id'] ?>')">&times;</span>
            <h2>Editar Slide de Carrusel</h2>
            <form method="POST" action="acciones_carrusel.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($slide['id']) ?>">
                <label>Título</label>
                <input type="text" name="titulo" id="edit-carrusel-titulo-<?= $slide['id'] ?>" value="<?= htmlspecialchars($slide['titulo']) ?>" required>
                <label>Descripción</label>
                <textarea name="descripcion" id="edit-carrusel-descripcion-<?= $slide['id'] ?>" rows="3" required><?= htmlspecialchars($slide['descripcion']) ?></textarea>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="edit-carrusel-imagen_url-<?= $slide['id'] ?>" value="<?= htmlspecialchars($slide['imagen_url']) ?>">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewEditCarrusel-<?= $slide['id'] ?>'); limpiarURL(this, 'edit-carrusel-imagen_url-<?= $slide['id'] ?>')">
                <br><br>
                <img id="previewEditCarrusel-<?= $slide['id'] ?>" class="preview-img" src="<?= htmlspecialchars($slide['imagen_url']) ?>" alt="Vista previa">
                <br><br>
                <label>Enlace</label>
                <input type="text" name="link_url" id="edit-carrusel-link_url-<?= $slide['id'] ?>" value="<?= htmlspecialchars($slide['link_url']) ?>">
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>


    <!-- AUTORIDADES -->
    <div id="autoridades" class="seccion-admin">
        <h1>Editar Autoridades</h1>
        <div class="add-new-btn-container">
            <button class="btn add-new-btn" onclick="openModal('autoridades-add-modal')"><i class="fas fa-plus-circle"></i> Agregar Nueva Autoridad</button>
        </div>
        <?php if ($autoridades): ?>
            <table class="developer-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Dependencia</th>
                        <th>Imagen</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($autoridades as $auth): ?>
                        <tr>
                            <td><?= htmlspecialchars($auth['id']) ?></td>
                            <td><?= htmlspecialchars($auth['nombre']) ?></td>
                            <td><?= htmlspecialchars($auth['cargo']) ?></td>
                            <td><?= htmlspecialchars($auth['dependencia']) ?></td>
                            <td>
                                <?php if (!empty($auth['imagen_url'])): ?>
                                    <img src="<?= htmlspecialchars($auth['imagen_url']) ?>" alt="Imagen de Autoridad" class="developer-image-thumb">
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <button class="btn btn-edit" onclick="openEditModal('autoridades-edit-modal-<?= $auth['id'] ?>', <?= htmlspecialchars(json_encode($auth)) ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST" action="acciones_autoridades.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar esta autoridad?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($auth['id']) ?>">
                                    <button type="submit" class="btn btn-delete">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron autoridades.</p>
        <?php endif; ?>
    </div>

    <!-- Modal para agregar Autoridad -->
    <div id="autoridades-add-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('autoridades-add-modal')">&times;</span>
            <h2>Agregar Nueva Autoridad</h2>
            <form method="POST" action="acciones_autoridades.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label>Nombre</label>
                <input type="text" name="nombre" id="add-autoridades-nombre" required>
                <label>Cargo</label>
                <input type="text" name="cargo" id="add-autoridades-cargo" required>
                <label>Dependencia</label>
                <input type="text" name="dependencia" id="add-autoridades-dependencia" required>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="add-autoridades-imagen_url">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewAddAutoridad'); limpiarURL(this, 'add-autoridades-imagen_url')">
                <br><br>
                <img id="previewAddAutoridad" class="preview-img" src="" alt="Vista previa">
                <br><br>
                <button type="submit" class="btn">Agregar Autoridad</button>
            </form>
        </div>
    </div>

    <!-- Modales para editar Autoridad (uno por cada autoridad) -->
    <?php foreach ($autoridades as $auth): ?>
    <div id="autoridades-edit-modal-<?= $auth['id'] ?>" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('autoridades-edit-modal-<?= $auth['id'] ?>')">&times;</span>
            <h2>Editar Autoridad</h2>
            <form method="POST" action="acciones_autoridades.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($auth['id']) ?>">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit-autoridades-nombre-<?= $auth['id'] ?>" value="<?= htmlspecialchars($auth['nombre']) ?>" required>
                <label>Cargo</label>
                <input type="text" name="cargo" id="edit-autoridades-cargo-<?= $auth['id'] ?>" value="<?= htmlspecialchars($auth['cargo']) ?>" required>
                <label>Dependencia</label>
                <input type="text" name="dependencia" id="edit-autoridades-dependencia-<?= $auth['id'] ?>" value="<?= htmlspecialchars($auth['dependencia']) ?>" required>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="edit-autoridades-imagen_url-<?= $auth['id'] ?>" value="<?= htmlspecialchars($auth['imagen_url']) ?>">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewEditAutoridad-<?= $auth['id'] ?>'); limpiarURL(this, 'edit-autoridades-imagen_url-<?= $auth['id'] ?>')">
                <br><br>
                <img id="previewEditAutoridad-<?= $auth['id'] ?>" class="preview-img" src="<?= htmlspecialchars($auth['imagen_url']) ?>" alt="Vista previa">
                <br><br>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>


    <!-- CONTACTO -->
    <div id="contacto" class="seccion-admin">
        <h1>Editar Información de Contacto</h1>
        <?php if ($contacto): ?>
            <button class="btn add-new-btn" onclick="openEditModal('contacto-edit-modal', <?= htmlspecialchars(json_encode($contacto)) ?>)"><i class="fas fa-edit"></i> Editar Contacto</button>
        <?php else: ?>
            <p>No se encontró información de contacto.</p>
        <?php endif; ?>
    </div>

    <!-- Modal para editar Contacto -->
    <?php if ($contacto): ?>
    <div id="contacto-edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('contacto-edit-modal')">&times;</span>
            <h2>Editar Información de Contacto</h2>
            <form method="POST" action="acciones_contacto.php">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($contacto['id']) ?>">
                <label>Dirección</label>
                <textarea name="direccion" id="edit-contacto-direccion" rows="2" required><?= htmlspecialchars($contacto['direccion']) ?></textarea>
                <label>Teléfono</label>
                <input type="text" name="telefono" id="edit-contacto-telefono" value="<?= htmlspecialchars($contacto['telefono']) ?>" required>
                <label>Correo Electrónico</label>
                <input type="email" name="correo" id="edit-contacto-correo" value="<?= htmlspecialchars($contacto['correo']) ?>" required>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <?php endif; ?>


    <!-- SOBRE NOSOTROS -->
    <div id="dataSobre" class="seccion-admin">
        <h1>Editar Sección "Sobre Nosotros"</h1>
        <?php if (isset($dataSobre)): ?>
            <button class="btn add-new-btn" onclick="openEditModal('sobre-edit-modal', <?= htmlspecialchars(json_encode($dataSobre)) ?>)"><i class="fas fa-edit"></i> Editar Sobre Nosotros</button>
        <?php else: ?>
            <p>No se encontró información de "Sobre Nosotros".</p>
        <?php endif; ?>
    </div>

    <!-- Modal para editar Sobre Nosotros -->
    <?php if (isset($dataSobre)): ?>
    <div id="sobre-edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('sobre-edit-modal')">&times;</span>
            <h2>Editar Sección "Sobre Nosotros"</h2>
            <form method="POST" action="acciones_sobre.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= htmlspecialchars($dataSobre['id'] ?? '') ?>">
                <label>Título</label>
                <input type="text" name="titulo" id="edit-sobre-titulo" value="<?= htmlspecialchars($dataSobre['titulo'] ?? '') ?>" required>
                <label>Descripción</label>
                <textarea name="descripcion" id="edit-sobre-descripcion" rows="5" required><?= htmlspecialchars($dataSobre['descripcion'] ?? '') ?></textarea>
                <label>URL de Imagen (opcional si subes una)</label>
                <input type="text" name="imagen_url" id="edit-sobre-imagen_url" value="<?= htmlspecialchars($dataSobre['imagen_url'] ?? '') ?>">
                <label>Subir Imagen</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewEditSobre'); limpiarURL(this, 'edit-sobre-imagen_url')">
                <div style="margin-top: 10px;">
                    <img id="previewEditSobre" class="preview-img" src="<?= htmlspecialchars($dataSobre['imagen_url'] ?? '') ?>" alt="Vista previa">
                </div>
                <button type="submit" class="btn">Guardar Cambios</button>
            </form>
        </div>
    </div>
    <?php endif; ?>


    <!-- DESARROLLADORES -->
    <div id="desarrolladores" class="seccion-admin">
        <h1>Gestionar Desarrolladores</h1>

        <div class="add-new-btn-container">
            <button class="btn add-new-btn" onclick="openModal('developer-add-modal')"><i class="fas fa-plus-circle"></i> Agregar Nuevo Desarrollador</button>
        </div>

        <!-- Lista de desarrolladores existentes -->
        <h2>Desarrolladores Existentes</h2>
        <?php if ($desarrolladores): ?>
            <table class="developer-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Imagen</th>
                        <th>Nombre</th>
                        <th>Cargo</th>
                        <th>Habilidades</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($desarrolladores as $dev): ?>
                        <tr>
                            <td><?= htmlspecialchars($dev['id']) ?></td>
                            <td>
                                <?php if (!empty($dev['imagen_url'])): ?>
                                    <img src="<?= htmlspecialchars($dev['imagen_url']) ?>" alt="Imagen de <?= htmlspecialchars($dev['nombre']) ?>" class="developer-image-thumb">
                                <?php else: ?>
                                                                    <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($dev['nombre']) ?></td>
                            <td><?= htmlspecialchars($dev['cargo']) ?></td>
                            <td><?= htmlspecialchars($dev['habilidades']) ?></td>
                            <td class="actions">
                                <button class="btn btn-edit" onclick="openEditModal('developer-edit-modal', <?= htmlspecialchars(json_encode($dev)) ?>)">
                                    <i class="fas fa-edit"></i> Editar
                                </button>
                                <form method="POST" action="acciones_desarrolladores.php" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a este desarrollador?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($dev['id']) ?>">
                                    <button type="submit" class="btn btn-delete">
                                        <i class="fas fa-trash-alt"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No se encontraron desarrolladores.</p>
        <?php endif; ?>
    </div>

    <!-- Modal para agregar Desarrollador -->
    <div id="developer-add-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('developer-add-modal')">&times;</span>
            <h2>Agregar Nuevo Desarrollador</h2>
            <form method="POST" action="acciones_desarrolladores.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add">
                <label>Nombre</label>
                <input type="text" name="nombre" id="add-developer-nombre" required>
                <label>Cargo</label>
                <input type="text" name="cargo" id="add-developer-cargo" required>
                <label>Descripción</label>
                <textarea name="descripcion" id="add-developer-descripcion" rows="3" required></textarea>
                <label>Habilidades (separadas por coma)</label>
                <input type="text" name="habilidades" id="add-developer-habilidades" required>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="add-developer-imagen_url">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewAddDeveloper'); limpiarURL(this, 'add-developer-imagen_url')">
                <br><br>
                <img id="previewAddDeveloper" class="preview-img" src="" alt="Vista previa">
                <br><br>
                <label>URL GitHub (opcional)</label>
                <input type="text" name="github_url" id="add-developer-github_url">
                <label>URL WhatsApp (opcional)</label>
                <input type="text" name="linkedin_url" id="add-developer-linkedin_url">
                <label>Email (opcional)</label>
                <input type="email" name="email" id="add-developer-email">
                <button type="submit" class="btn">Agregar Desarrollador</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar Desarrollador -->
    <div id="developer-edit-modal" class="modal">
        <div class="modal-content">
            <span class="close-button" onclick="closeModal('developer-edit-modal')">&times;</span>
            <h2>Editar Desarrollador</h2>
            <form id="editDeveloperForm" method="POST" action="acciones_desarrolladores.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">
                <label>Nombre</label>
                <input type="text" name="nombre" id="edit-nombre" required>
                <label>Cargo</label>
                <input type="text" name="cargo" id="edit-cargo" required>
                <label>Descripción</label>
                <textarea name="descripcion" id="edit-descripcion" rows="3" required></textarea>
                <label>Habilidades (separadas por coma)</label>
                <input type="text" name="habilidades" id="edit-habilidades" required>
                <label>URL Imagen (opcional si subes imagen)</label>
                <input type="text" name="imagen_url" id="edit-imagen_url">
                <label>Subir Imagen (JPG/PNG)</label>
                <input type="file" name="imagen_archivo" accept="image/*" onchange="mostrarVistaPrevia(this, 'previewEditDeveloper'); limpiarURL(this, 'edit-imagen_url')">
                <br><br>
                <img id="previewEditDeveloper" class="preview-img" src="" alt="Vista previa">
                <br><br>
                <label>URL GitHub (opcional)</label>
                <input type="text" name="github_url" id="edit-github_url">
                <label>URL WhatsApp (opcional)</label>
                <input type="text" name="linkedin_url" id="edit-linkedin_url">
                <label>Email (opcional)</label>
                <input type="email" name="email" id="edit-email">
                <button type="submit" class="btn">Guardar Cambios</button>
                <button type="button" class="btn btn-delete" onclick="closeModal('developer-edit-modal')">Cancelar</button>
            </form>
        </div>
    </div>
<div class="container" style="text-align: center; margin: 30px 0;">
<div style="text-align: center; margin: 25px 0;">
    <a href="admin.php" class="btn-back" style="
        display: inline-block;
        padding: 8px 15px;
        color: #8B0000;
        text-decoration: none;
        border: 1px solid #8B0000;
        border-radius: 4px;
        transition: all 0.2s ease;
    ">
        <i class="fas fa-arrow-left" style="margin-right: 5px;"></i>
        Volver al Panel
    </a>
</div>
<script>
    // Cambio de pestañas
    const tabs = document.querySelectorAll('#tabs a');
    const sections = document.querySelectorAll('.seccion-admin');

    // Función para activar la pestaña correcta al cargar la página (si hay un hash en la URL)
    function activateTabFromHash() {
        const hash = window.location.hash.substring(1); // Get hash without '#'
        if (hash) {
            tabs.forEach(t => t.classList.remove('active'));
            sections.forEach(sec => sec.classList.remove('active'));

            const targetTab = document.querySelector(`#tabs a[data-target="${hash}"]`);
            const targetSection = document.getElementById(hash);

            if (targetTab) {
                targetTab.classList.add('active');
            }
            if (targetSection) {
                targetSection.classList.add('active');
            }
        } else {
            // Default to the first tab if no hash
            document.querySelector('#tabs a[data-target="carrusel"]').classList.add('active');
            document.getElementById('carrusel').classList.add('active');
        }
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', e => {
            e.preventDefault();

            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            sections.forEach(sec => sec.classList.remove('active'));
            const target = tab.getAttribute('data-target');
            document.getElementById(target).classList.add('active');

            // Update URL hash
            window.location.hash = target;
        });
    });

    // Call on load to handle initial hash
    window.addEventListener('load', activateTabFromHash);


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
    function limpiarURL(input, urlFieldId = null) {
        let urlField;
        if (urlFieldId) {
            urlField = document.getElementById(urlFieldId);
        } else {
            // Find the closest form and then the image_url input within it
            urlField = input.closest('form').querySelector('input[name="imagen_url"]');
        }
        if (urlField) {
            urlField.value = '';
        }
    }

    // Funciones para abrir y cerrar modales
    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'flex';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    // Función para abrir modal de edición y precargar datos
    function openEditModal(modalId, data) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`Modal element with ID ${modalId} not found.`);
            return;
        }
        const form = modalElement.querySelector('form');
        if (!form) {
            console.error(`Form not found inside modal ${modalId}.`);
            return;
        }

        // Reset form fields before populating
        form.reset();

        // Populate fields based on the modal type
        if (modalId.startsWith('carrusel-edit-modal-')) {
            // Carrusel specific fields
            form.querySelector(`#edit-carrusel-titulo-${data.id}`).value = data.titulo;
            form.querySelector(`#edit-carrusel-descripcion-${data.id}`).value = data.descripcion;
            form.querySelector(`#edit-carrusel-imagen_url-${data.id}`).value = data.imagen_url;
            form.querySelector(`#edit-carrusel-link_url-${data.id}`).value = data.link_url;
            const previewImg = form.querySelector(`#previewEditCarrusel-${data.id}`);
            if (data.imagen_url) {
                previewImg.src = data.imagen_url;
            } else {
                previewImg.src = '';
            }
        } else if (modalId.startsWith('autoridades-edit-modal-')) {
            // Autoridades specific fields
            form.querySelector(`#edit-autoridades-nombre-${data.id}`).value = data.nombre;
            form.querySelector(`#edit-autoridades-cargo-${data.id}`).value = data.cargo;
            form.querySelector(`#edit-autoridades-dependencia-${data.id}`).value = data.dependencia;
            form.querySelector(`#edit-autoridades-imagen_url-${data.id}`).value = data.imagen_url;
            const previewImg = form.querySelector(`#previewEditAutoridad-${data.id}`);
            if (data.imagen_url) {
                previewImg.src = data.imagen_url;
            } else {
                previewImg.src = '';
            }
        } else if (modalId === 'contacto-edit-modal') {
            // Contacto specific fields
            form.querySelector('#edit-contacto-direccion').value = data.direccion;
            form.querySelector('#edit-contacto-telefono').value = data.telefono;
            form.querySelector('#edit-contacto-correo').value = data.correo;
        } else if (modalId === 'sobre-edit-modal') {
            // Sobre Nosotros specific fields
            form.querySelector('#edit-sobre-titulo').value = data.titulo;
            form.querySelector('#edit-sobre-descripcion').value = data.descripcion;
            form.querySelector('#edit-sobre-imagen_url').value = data.imagen_url;
            const previewImg = form.querySelector('#previewEditSobre');
            if (data.imagen_url) {
                previewImg.src = data.imagen_url;
            } else {
                previewImg.src = '';
            }
        } else if (modalId === 'developer-edit-modal') {
            // Desarrolladores specific fields
            form.querySelector('#edit-id').value = data.id;
            form.querySelector('#edit-nombre').value = data.nombre;
            form.querySelector('#edit-cargo').value = data.cargo;
            form.querySelector('#edit-descripcion').value = data.descripcion;
            form.querySelector('#edit-habilidades').value = data.habilidades;
            form.querySelector('#edit-imagen_url').value = data.imagen_url;
            form.querySelector('#edit-github_url').value = data.github_url;
            form.querySelector('#edit-linkedin_url').value = data.linkedin_url; // Assuming linkedin_url is used for WhatsApp
            form.querySelector('#edit-email').value = data.email;
            const previewImg = form.querySelector('#previewEditDeveloper');
            if (data.imagen_url) {
                previewImg.src = data.imagen_url;
            } else {
                previewImg.src = '';
            }
        }

        modalElement.style.display = 'flex';
    }

    // Cerrar modal al hacer clic fuera del contenido
    window.onclick = function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>
