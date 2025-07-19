# 📚 Sistema de Gestión de Eventos Académicos

## 🌟 Descripción del Proyecto

**Sistema de Gestión de Eventos Académicos** es una plataforma web completa para administrar eventos, cursos, inscripciones y certificaciones en instituciones educativas. El sistema ofrece funcionalidades diferenciadas para administradores y estudiantes, con un enfoque en la facilidad de uso y automatización de procesos.

## 🛠️ Tecnologías Utilizadas

- **Frontend**: HTML5, CSS, JavaScript
- **Backend**: PHP
- **Base de Datos**: PostgreSQL
- **Generación de PDF**: DomPDF (via Composer)
- **Control de Versiones**: Git

## ✨ Características Principales

### 👨‍💻 Panel de Administración
- **Gestión de Eventos/Cursos**
  - Creación, edición y eliminación de eventos
  - Configuración de fechas, horarios
- **Gestión de Usuarios**
  - Registro y administración de cuentas
  - Asignación de roles (admin, estudiante)
- **Control de Asistencias**
  - Registro de asistencia a eventos
  - Sistema de calificaciones para cursos
- **Certificaciones**
  - Generación automática de certificados en PDF
  - Almacenamiento seguro de documentos
  - Visualización de certificados generados

### 🎓 Panel de Estudiante
- **Registro e Inscripción**
  - Creación de cuenta personal
  - Inscripción a eventos/cursos disponibles
- **Consulta de Información**
  - Visualización de eventos activos

## 🚀 Instalación y Configuración

### Requisitos del Sistema
- Servidor Web (Apache) o cualquiera de su preferencia
- PHP 7.4 o superior
- PostgreSQL 17
- Composer (para dependencias PHP)

### Pasos de Instalación

1. **Clonar el repositorio**
   ```bash
   git clone https://github.com/SebasIsd/Proyecto2P_Event.git
   cd Proyecto2P_Event
   ```

2. **Configurar base de datos**
   - Importar el archivo SQL (`sql/BaseDatos_Proyecto.sql`)
   - Ejemplo para configurar credenciales en `includes/conexion1.php`

3. **Instalar dependencias**
   ```bash
   composer init
   composer requiere dompdf/dompdf
   ```

## 📂 Estructura del Proyecto

```
Proyecto2P_Event/
├── admin/                # Panel de administración
├── certificados/         # Certificados generados
├── conexion/             # PHP para consultas SQL
├── includes/             # Conexion para BD
├── SQL/                  # Contiene el .sql
├── styles/               # Hojas de estilo CSS junto JS
├── usuarios/             # Panel de Usuario(Estudiante)
├── .gitignore            # Configuración para ignorar archivos
├── composer.json         # Configuración de Composer
├── composer.lock         # Configuración de Composer, control versiones
├── index.html            # Punto de entrada principal
└── README.md             # Documentación
```

## 🤝 Contribución

1. Haz fork del proyecto
2. Crea una rama para tu feature (`git checkout -b feature/awesome-feature`)
3. Haz commit de tus cambios (`git commit -m 'Add awesome feature'`)
4. Haz push a la rama (`git push origin feature/awesome-feature`)
5. Abre un Pull Request

## 📧 Contacto

- **Desarrolladores**:
  - [Sebastián]  - SebasIsd
  - [viviana]    - maribelsailema
  - [Alex]       - alexJonarey
  - [Anthony]    - zamukay
- **GitHub**: [https://github.com/SebasIsd](https://github.com/SebasIsd)
- **Issues**: [Reportar problemas](https://github.com/SebasIsd/Proyecto2P_Event/issues)
