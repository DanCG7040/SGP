# Sistema CRUD de Pacientes - Prueba Técnica

## Descripción del Proyecto

Sistema completo de gestión de pacientes desarrollado con PHP para backend y HTML/CSS/JavaScript para frontend. Implementa una API RESTful con autenticación JWT, validaciones de seguridad y arquitectura MVC.

## Estructura del Proyecto

```
proyecto_crud_pacientes/
├── backend/
│   ├── api/
│   │   └── index.php (Router principal)
│   ├── config/
│   │   ├── config.php (Configuración general)
│   │   └── database.php (Configuración de BD)
│   ├── controllers/
│   │   ├── AuthController.php (Autenticación de usuarios)
│   │   ├── PacienteController.php (CRUD pacientes)
│   │   └── CatalogoController.php (Catálogos)
│   ├── models/
│   │   ├── User.php (Modelo de usuarios)
│   │   ├── Paciente.php (Modelo de pacientes)
│   │   └── Catalogo.php (Modelo de catálogos)
│   ├── middleware/
│   │   └── AuthMiddleware.php (Middleware de autenticación)
│   ├── utils/
│   │   ├── Database.php (Conexión PDO)
│   │   ├── JWT.php (Manejo de tokens)
│   │   ├── Response.php (Respuestas JSON)
│   │   └── Validator.php (Validaciones)
│   └── database/
│       ├── migrations/
│       │   └── 01_create_tables.sql
│       ├── seeders/
│       │   ├── 02_seed_data.sql
│       │   └── 03_pacientes_adicionales.sql
│       └── setup.php
└── frontend/
    ├── css/
    ├── js/
    └── index.html
```

## Requisitos del Sistema

- PHP 7.4 o superior
- MySQL 5.7 o superior
- Servidor web (Apache/Nginx) o PHP built-in server

## Instalación

### 1. Configurar Base de Datos

Editar el archivo `backend/config/database.php` con las credenciales de MySQL:

```php
return [
    'host' => 'localhost',
    'dbname' => 'sgp_pacientes',  // Nombre de la base de datos
    'username' => 'root',           // Tu usuario de MySQL
    'password' => 'tu_contraseña', // Tu contraseña de MySQL
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];
```

**Nota:** El nombre de la base de datos es `sgp_pacientes`. Este nombre se creará automáticamente al ejecutar el script de setup.

### 2. Crear Base de Datos y Tablas

Ejecutar el script de configuración desde la terminal:

**Windows (PowerShell):**
```powershell
cd backend\database
php setup.php
```

**Linux/Mac:**
```bash
cd backend/database
php setup.php
```

Este script ejecuta automáticamente:
- Creación de la base de datos `sgp_pacientes`
- Creación de todas las tablas (migraciones)
- Inserción de datos iniciales (seeders)
- Creación del usuario administrador

**Salida esperada:**
```
Conectado a MySQL correctamente

Ejecutando migraciones (creando tablas)...
Migraciones ejecutadas correctamente

Ejecutando seeders (insertando datos iniciales)...
Seeders ejecutados correctamente

Base de datos configurada correctamente
Usuario admin creado:
  Usuario: admin
  Contraseña: 1234567890
```

### 3. Iniciar el Servidor

```bash
cd backend/api
php -S localhost:8000
```

El servidor estará disponible en: `http://localhost:8000`

## Credenciales de Acceso

- Usuario: `admin`
- Contraseña: `1234567890`

## Endpoints de la API

### Autenticación

**POST /api/auth/login**
- Descripción: Autenticación de usuario
- Autenticación: No requerida
- Body:
```json
{
  "username": "admin",
  "password": "1234567890"
}
```
- Respuesta:
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "user": {
      "id": 1,
      "username": "admin",
      "email": "admin@sgp.com"
    }
  }
}
```

### Catálogos (No requieren autenticación)

**GET /api/catalogos/departamentos**
- Descripción: Listar todos los departamentos

**GET /api/catalogos/municipios?departamento_id={id}**
- Descripción: Listar municipios por departamento

**GET /api/catalogos/tipos-documento**
- Descripción: Listar tipos de documento

**GET /api/catalogos/generos**
- Descripción: Listar géneros

### Pacientes (Requieren autenticación JWT)

**GET /api/pacientes**
- Descripción: Listar pacientes con paginación
- Parámetros opcionales: `?page=1&limit=10&search=texto`
- Headers: `Authorization: Bearer {token}`

**GET /api/pacientes/{id}**
- Descripción: Obtener un paciente por ID
- Headers: `Authorization: Bearer {token}`

**POST /api/pacientes**
- Descripción: Crear nuevo paciente
- Headers: `Authorization: Bearer {token}`
- Body:
```json
{
  "tipo_documento_id": 1,
  "numero_documento": "1234567890",
  "nombre1": "Juan",
  "nombre2": "Carlos",
  "apellido1": "Pérez",
  "apellido2": "García",
  "genero_id": 1,
  "departamento_id": 1,
  "municipio_id": 1,
  "correo": "juan@email.com"
}
```

**PUT /api/pacientes/{id}**
- Descripción: Actualizar paciente existente
- Headers: `Authorization: Bearer {token}`
- Body: Mismo formato que POST

**DELETE /api/pacientes/{id}**
- Descripción: Eliminar paciente
- Headers: `Authorization: Bearer {token}`

## Estructura de Base de Datos

**Base de datos:** `sgp_pacientes`

### Tablas Principales

- **departamentos**: Almacena departamentos (5 registros iniciales)
- **municipios**: Almacena municipios (2 por departamento = 10 registros)
- **tipos_documento**: Tipos de documento de identidad (2 registros)
- **genero**: Géneros disponibles (3 registros)
- **users**: Usuarios del sistema (1 usuario admin inicial)
- **paciente**: Información de pacientes (5 registros de prueba)

### Relaciones

- `municipios.departamento_id` → `departamentos.id`
- `paciente.tipo_documento_id` → `tipos_documento.id`
- `paciente.genero_id` → `genero.id`
- `paciente.departamento_id` → `departamentos.id`
- `paciente.municipio_id` → `municipios.id`

### Scripts de Base de Datos

Los scripts de migración y seeders se encuentran en:
- **Migraciones:** `backend/database/migrations/01_create_tables.sql`
- **Seeders:** `backend/database/seeders/02_seed_data.sql`
- **Setup automático:** `backend/database/setup.php`

## Características de Seguridad

1. **Autenticación JWT**: Tokens con expiración de 1 hora
2. **Prepared Statements**: Protección contra SQL Injection usando PDO
3. **Validación de Datos**: Validación en backend de todos los inputs
4. **Sanitización**: Limpieza de datos antes de procesarlos
5. **CORS**: Configurado para permitir peticiones desde frontend

## Tecnologías Utilizadas

- PHP 8.5
- MySQL 8.0
- PDO para acceso a base de datos
- JWT para autenticación
- Arquitectura MVC

## Estructura de Archivos de Pruebas

Las pruebas unitarias se encuentran en `backend/tests/`:
- ValidatorTest.php: Pruebas de validación de datos
- JWTTest.php: Pruebas de generación y validación de tokens
- PacienteControllerTest.php: Pruebas de validación de pacientes

Para ejecutar las pruebas, usar PHPUnit desde la carpeta backend:
```bash
cd backend
vendor/bin/phpunit tests/
```

## Autor

Daniel Santiago Cárdenas Gómez

