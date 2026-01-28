# Pruebas Unitarias con PHPUnit

## Instalación de PHPUnit

### Opción 1: Instalar globalmente con Composer

```bash
composer global require phpunit/phpunit
```

### Opción 2: Instalar localmente en el proyecto

```bash
cd backend
composer require --dev phpunit/phpunit
```

### Opción 3: Descargar PHAR

```bash
wget https://phar.phpunit.de/phpunit.phar
chmod +x phpunit.phar
```

## Ejecutar las Pruebas

### Si instalaste globalmente:

```bash
cd backend/tests
phpunit
```

### Si instalaste localmente:

```bash
cd backend
vendor/bin/phpunit tests
```

### Si usas PHAR:

```bash
cd backend/tests
php phpunit.phar
```

## Estructura de Pruebas

### ValidatorTest.php
Prueba todas las funciones de validación:
- Validación de email
- Campos requeridos
- Longitud de strings
- Sanitización de datos
- Validación de formato de documento

### JWTTest.php
Prueba la funcionalidad de JWT:
- Generación de tokens
- Validación de tokens válidos
- Rechazo de tokens inválidos
- Rechazo de tokens expirados

### PacienteControllerTest.php
Prueba la lógica de validación del controlador:
- Validación de campos requeridos
- Validación de formato de email
- Validación de formato de documento
- Validación de longitud de campos

## Cobertura de Pruebas

Las pruebas unitarias cubren:

1. **Validaciones**: Todas las funciones del Validator
2. **Autenticación**: Generación y validación de JWT
3. **Controladores**: Lógica de validación de datos

## Notas

- Las pruebas no requieren conexión a base de datos real
- Se usan mocks y reflexión para probar métodos privados
- Las pruebas son independientes entre sí
- Cada prueba verifica un aspecto específico de la funcionalidad
