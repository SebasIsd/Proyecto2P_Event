🧾 Módulo de Generación de Certificados

📑 Este módulo permite generar certificados en formato PDF para los participantes de eventos académicos.

🖼️ Se utiliza una plantilla general definida por el administrador, y se insertan dinámicamente los siguientes datos del estudiante:

👤 Nombre completo

🆔 Cédula

🎓 Evento en el que participó

📅 Fecha de finalización del evento

📂 Los certificados generados se almacenan en una carpeta del servidor (/certificados/) y 📌 su ruta queda registrada en la base de datos para ser visualizada posteriormente.

⚙️ Requisitos del Módulo

✅ PHP 7.4 o superior
✅ Composer instalado

📦 Instalación de dependencias PHP (🔧 Solo para este módulo)
Este módulo hace uso del paquete dompdf/dompdf para convertir contenido HTML en archivos PDF.

🚀 Pasos para instalar
📁 Ve a la carpeta raíz del proyecto (donde está el archivo composer.json)

💻 Ejecuta en la terminal:

composer install

✅ Composer descargará las dependencias necesarias en la carpeta /vendor

📚 Librería utilizada
Paquete	        Descripción
dompdf/dompdf	📄 Generador de PDFs a partir de HTML y CSS en PHP

📂 Ubicación de PDFs generados
Los certificados se almacenan automáticamente en la carpeta:

/certificados/
Asegúrate de que el servidor tenga permisos de escritura en esa carpeta (chmod 775 certificados)

📤 Punto de entrada
El archivo responsable de generar certificados es:

admin/generarCertificado.php

Este archivo recibe el ID_INS mediante una solicitud POST en formato JSON, genera el PDF correspondiente y guarda la ruta en la base de datos.

🙈 Archivos/Carteras ignoradas (.gitignore)
Asegúrate de no subir archivos innecesarios o sensibles:

/vendor/          # Librerías instaladas con Composer
/certificados/    # Certificados PDF generados
composer.lock     # Puede ignorarse si se desea no fijar versión de dependencias
*.log             # Archivos de log

🚀 Uso
Selecciona un usuario apto en la interfaz de certificados.

Haz clic en "Generar" para crear y guardar el certificado.

Se habilita el botón "Ver" para abrir el PDF en una nueva pestaña.

