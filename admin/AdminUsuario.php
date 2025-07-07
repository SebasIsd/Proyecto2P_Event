<?php
session_start();
require_once('../includes/conexion1.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administrar Usuarios</title>
   <link rel="stylesheet" href="../styles/css/style.css"/>
  <link rel="stylesheet" href="../styles/css/estiloAdminusuario.css"/>
   <link rel="stylesheet" href="../styles/css/pagoestilo.css">
   <link rel="stylesheet" href="../styles/css/componente.css">
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"/>

  <style>
    .loading { display: none; color: #ff0000; font-weight: bold; }
    .error { color: #dc3545; }
    .form-container { display: none; margin-top: 15px; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; }
    .btn-toggle { margin-top: 20px; cursor: pointer; background-color: #6c1313; color: #fff; border: none; padding: 10px; border-radius: 5px; }
    .btn-toggle:hover { background-color: #0056b3; }
    #modificar {
    display: none;
  }

  .loading { 
    display: none; 
    color: #6c1313; 
    font-weight: bold; 
  }

  .error { 
    color: #dc3545; 
  }

  .form-container {
    display: none;
    margin-top: 15px;
    padding: 15px;
    border: 2px solid #333; /* Borde más visible */
    background: #6c1313;
    border-radius: 8px;       /* Bordes redondeados */
    box-shadow: 0 2px 6px rgba(0,0,0,0.1); /* Sombra suave */
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
  }

  #userData form {
    padding: 15px;
    border: 2px solid #333;
    border-radius: 8px;
    background-color: #6c1313;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);

  }
  


  .btn-toggle {
    margin-top: 20px;
    cursor: pointer;
    background-color: #6c1313;
    color: #fff;
    border: none;
    padding: 10px;
    border-radius: 5px;
  }

  .btn-toggle:hover {
    background-color: #0056b3;
  }

  #modificar {
    display: none;
  }
  
  #formAgregarUsuario input {
    padding: 3px 5px;
    height: 25px;  /* o menos si quieres */
    font-size: 14px;
  }

  #formUsuario input {
    padding: 3px 5px;
    height: 25px;  /* o menos si quieres */
    font-size: 14px;
  }
  #formEliminarUsuario input {
    padding: 3px 5px;
    height: 25px;  /* o menos si quieres */
    font-size: 14px;
  }
  #ced_usu {
    padding: 3px 5px;
    height: 25px;  /* o menos si quieres */
    font-size: 14px;
  }
</style>


  
</head>
<body>

<?php include '../includes/headeradmin.php'?>

<main class="container">
  
    <h2>Gestión de Usuarios</h2>
    <button class="btn-toggle" onclick="toggleForm('formAgregar')"><i class="fas fa-user-plus"></i> Añadir Usuario</button>
    <button class="btn-toggle" onclick="toggleForm('modificar')"><i class="fas fa-user-plus"></i> Modificar Usuario</button>
    <button class="btn-toggle" onclick="toggleForm('formEliminar')"><i class="fas fa-user-times"></i> Eliminar Usuario</button>
<div style="height: 25px;"></div>
    <div class="form-container" id="formAgregar" style="display: none;  max-width: 350px; margin: auto;">
     

      <h3 style="color: white;">Agregar Usuario</h3>
      <form id="formAgregarUsuario"  >
        <input type="text" name="cedula" placeholder="Cédula" required style="width: 100%;"><br>
        <div style="display: flex; gap: 5px;">
        <input type="text" name="nom_pri_usu" placeholder="Primer Nombre" required  style="width: 100%;"><br>
        <input type="text" name="nom_seg_usu" placeholder="Segundo Nombre" style="width: 100%;"><br>
        </div>
         <div style="display: flex; gap: 5px;">
        <input type="text" name="ape_pri_usu" placeholder="Apellido Paterno" required style="width: 100%;"><br>
        <input type="text" name="ape_seg_usu" placeholder="Apellido Materno" style="width: 100%;"><br>
        </div>
        <input type="email" name="correo" placeholder="Correo" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <input type="text" name="telefono" placeholder="Teléfono"><br>
        <input type="text" name="direccion" placeholder="Dirección"><br>
        <input type="date" name="fec_nac_usu" required><br>
        <select name="id_rol_usu" required>
          <option value="" disabled selected>Seleccione Cargo</option>
          <option value="1">Administrador</option>
          <option value="2">Usuario</option>
          <option value="3">Invitado</option>
        </select><br>
        <input type="text" name="carrera" placeholder="Carrera"><br>
        <button type="submit">Agregar Usuario</button>
      </form>
    </div>
    <div id="modificar" class="search-box" >
     
    <input type="text" id="ced_usu" placeholder="Ingrese la cédula" maxlength="20"/>
    <button id="buscarBtn"><i class="fas fa-search"></i> Buscar</button>
    <span id="loading" class="loading">Buscando...</span>
    <span id="errorMsg" class="error"></span>
  </div>
  <div id="userData" style="display: none;">
    <h3>Datos del Usuario</h3>
    <form id="formUsuario"   style="max-width: 300px; margin: auto; padding-top: 5px; padding-bottom: 5px;">
      <input type="hidden" id="ced_usu_hidden" name="ced_usu"/>
      <label style="color: white;" for="ape_pri_usu">Nombres:</label>
       <div style="display: flex; gap: 2px;">
      <input type="text" id="nom_pri_usu" name="nom_pri_usu" disabled required><br>
      <input type="text" id="nom_seg_usu" name="nom_seg_usu" disabled><br>
        </div>
        <label style="color: white;" for="ape_pri_usu">Apellidos:</label>
         <div style="display: flex; gap: 2px;">  
      <input type="text" id="ape_pri_usu" name="ape_pri_usu" disabled required><br>
      <input type="text" id="ape_seg_usu" name="ape_seg_usu" disabled><br>
      </div>
      <label style="color: white;"  for="cor_usu">Correo:</label>
      <input type="email" id="cor_usu" name="cor_usu" disabled required><br>
      <label style="color: white;"  for="pas_usu">Contraseña:</label>
      <input type="password" id="pas_usu" name="pas_usu" disabled required><br>
      <label style="color: white;" for="tel_usu">Teléfono:</label>
      <input type="text" id="tel_usu" name="tel_usu" disabled><br>
      <label style="color: white;" for="dir_usu">Dirección:</label>
      <input  type="text" id="dir_usu" name="dir_usu" disabled><br>
      <label style="color: white;" for="fec_nac_usu">Fecha de Nacimiento:</label>
      <input type="date" id="fec_nac_usu" name="fec_nac_usu" disabled><br>

      <button type="button" id="btnEditar" onclick="habilitarEdicion()">Editar</button>
      <button type="button" id="btnGuardar" onclick="actualizarUsuario()" style="display:none;">Guardar</button>
      <span id="loading" style="display:none;">Guardando...</span>
    </form>
    <p id="error" class="error"></p>
  </div>

    <div class="form-container" id="formEliminar" style="display: none;">
      <h3 style="color: white;">Eliminar Usuario</h3>
      <form id="formEliminarUsuario">
        <input type="text" name="cedula" placeholder="Cédula" required><br>
        <button type="submit">Eliminar Usuario</button>
      </form>
    </div>
  <!-- Botones de gestión -->
  <aside style="margin-top: 40px;">
    <!-- Listado -->
    <section>
      <h3>Listado de Usuarios</h3>
      <div style="overflow-x:auto;">
        <table border="1" style="width:100%;" id="tablaUsuarios">
          <thead>
            <tr>
              <th>Cédula</th>
              <th>Nombres</th>
              <th>Apellidos</th>
              <th>Correo</th>
              <th>Teléfono</th>
              <th>Carrera</th>
              <th>Cargo</th>
            </tr>
          </thead>
          <tbody id="cuerpoTablaUsuarios"></tbody>
        </table>
      </div>
    </section>

    <!-- Botones para mostrar/ocultar formularios -->
  </aside>
</main>
</br>
 <?php include '../includes/footeradmin.php'?>


<!-- Scripts -->
<script src="../styles/buscarusuario.js"></script>
<script src="../styles/usuarios_adimn.js"></script>
<script>
  function toggleForm(id) {
    const form = document.getElementById(id);
    form.style.display = (form.style.display === 'none' || form.style.display === '') ? 'block' : 'none';
  }
</script>
<script>
  function toggleForm(formId) {
    const formularios = ['formAgregar', 'modificar', 'formEliminar'];

    formularios.forEach(id => {
      const form = document.getElementById(id);
      if (form) {
        form.style.display = (id === formId) ? 'block' : 'none';
      }
    });
  }
</script>

</body>
</html>