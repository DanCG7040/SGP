# Resumen del Proyecto - Sistema de Gestión de Pacientes

## Descripción General

Sistema CRUD completo para gestión de pacientes desarrollado como prueba técnica. Implementa una API RESTful con PHP en el backend y una interfaz web con HTML, CSS y JavaScript en el frontend.

## Tecnologías Utilizadas

### Backend
- PHP 8.5
- MySQL 8.0
- PDO para acceso a base de datos
- JWT para autenticación
- PHPUnit para pruebas unitarias

### Frontend
- HTML5
- CSS3 con variables CSS
- JavaScript (ES6+)
- Bootstrap 5
- Bootstrap Icons

## Estructura del Proyecto

```
proyecto_crud_pacientes/
├── backend/
│   ├── api/              # Router principal
│   ├── config/           # Configuración
│   ├── controllers/      # Controladores MVC
│   ├── models/           # Modelos de datos
│   ├── middleware/       # Middleware de autenticación
│   ├── utils/            # Utilidades (Database, JWT, Response, Validator)
│   ├── database/         # Migraciones y seeders
│   └── tests/            # Pruebas unitarias
└── frontend/
    ├── css/              # Estilos
    ├── js/               # JavaScript
    └── *.html            # Páginas HTML
```

## Funcionalidades Implementadas

### Backend
- API RESTful completa (GET, POST, PUT, DELETE)
- Autenticación JWT
- Validación de datos
- Protección contra SQL Injection
- Paginación de resultados
- Búsqueda de pacientes
- Manejo de fotos en base64

### Frontend
- Interfaz de login
- Listado de pacientes con paginación
- Búsqueda en tiempo real
- Crear nuevo paciente
- Editar paciente existente
- Eliminar paciente con confirmación
- Validaciones del lado del cliente
- Mensajes dinámicos de éxito/error
- Visualización de fotos

## Base de Datos

- Nombre: sgp_pacientes
- Tablas: departamentos, municipios, tipos_documento, genero, users, paciente
- Relaciones foráneas implementadas correctamente
- Campo foto como LONGTEXT para soportar imágenes

## Seguridad

- Autenticación JWT con expiración
- Prepared statements (PDO)
- Validación de datos en backend y frontend
- Sanitización de inputs
- CORS configurado
- Contraseñas con hash bcrypt

## Instalación Rápida

1. Configurar credenciales en `backend/config/database.php`
2. Ejecutar `php backend/database/setup.php`
3. Iniciar servidor: `php -S localhost:8000` desde `backend/api`
4. Abrir `frontend/login.html` en el navegador
5. Login con usuario: admin, contraseña: 1234567890

## Archivos de Documentación

- README.md: Documentación principal del proyecto
- INSTALACION.md: Guía detallada de instalación
- BACKEND_EXPLICACION.md: Explicación técnica del backend
- CUMPLIMIENTO_REQUISITOS.md: Verificación de cumplimiento de requisitos
- EVALUACION_ASPECTOS.md: Detalle de aspectos de evaluación
