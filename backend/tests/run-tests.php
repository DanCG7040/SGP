<?php

require_once __DIR__ . '/bootstrap.php';

echo "=== Ejecutando Pruebas Unitarias ===\n\n";

$testsPassed = 0;
$testsFailed = 0;

function assertTrue($condition, $message) {
    global $testsPassed, $testsFailed;
    if ($condition) {
        echo "✓ PASS: $message\n";
        $testsPassed++;
    } else {
        echo "✗ FAIL: $message\n";
        $testsFailed++;
    }
}

function assertFalse($condition, $message) {
    assertTrue(!$condition, $message);
}

function assertEquals($expected, $actual, $message) {
    assertTrue($expected === $actual, $message . " (Expected: $expected, Got: $actual)");
}

echo "--- Pruebas de Validator ---\n";

assertTrue(Validator::email('test@example.com'), 'Email válido');
assertFalse(Validator::email('invalid-email'), 'Email inválido');
assertFalse(Validator::email(''), 'Email vacío');

$data = ['username' => 'test', 'password' => '123'];
$result = Validator::required($data, ['username', 'password']);
assertTrue($result === true, 'Campos requeridos presentes');

$data = ['username' => 'test'];
$result = Validator::required($data, ['username', 'password']);
assertTrue(is_array($result), 'Campos requeridos faltantes detectados');

assertTrue(Validator::length('test', 2, 10), 'Longitud válida');
assertFalse(Validator::length('a', 2, 10), 'Longitud inválida (muy corta)');

$sanitized = Validator::sanitize('<script>alert("xss")</script>  test  ');
assertTrue(strpos($sanitized, '<script>') === false, 'Sanitización elimina scripts');
assertTrue(trim($sanitized) !== '', 'Sanitización mantiene contenido válido');

assertTrue(Validator::documento('1234567890'), 'Documento válido');
assertFalse(Validator::documento('123-456'), 'Documento inválido');

echo "\n--- Pruebas de JWT ---\n";

$payload = ['user_id' => 1, 'username' => 'testuser'];
$token = JWT::generate($payload);
assertTrue(!empty($token), 'Token generado');
assertTrue(is_string($token), 'Token es string');

$parts = explode('.', $token);
assertEquals(3, count($parts), 'Token tiene 3 partes');

$decoded = JWT::validate($token);
assertTrue($decoded !== false, 'Token válido');
if ($decoded) {
    assertEquals(1, $decoded['user_id'], 'Payload contiene user_id');
    assertEquals('testuser', $decoded['username'], 'Payload contiene username');
}

$invalidToken = 'invalid.token.here';
$result = JWT::validate($invalidToken);
assertFalse($result, 'Token inválido rechazado');

echo "\n--- Pruebas de PacienteController ---\n";

$controller = new PacienteController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('validatePaciente');
$method->setAccessible(true);

$data = [];
$errors = $method->invoke($controller, $data);
assertTrue(is_array($errors), 'Validación detecta campos faltantes');

$data = [
    'tipo_documento_id' => 1,
    'numero_documento' => '1234567890',
    'nombre1' => 'Juan',
    'apellido1' => 'Pérez',
    'genero_id' => 1,
    'departamento_id' => 1,
    'municipio_id' => 1,
    'correo' => 'email-invalido'
];
$errors = $method->invoke($controller, $data);
assertTrue(isset($errors['correo']), 'Email inválido detectado');

$data['correo'] = 'juan@email.com';
$errors = $method->invoke($controller, $data);
if (!empty($errors)) {
    assertFalse(isset($errors['correo']), 'Email válido aceptado');
}

echo "\n=== Resumen ===\n";
echo "Pruebas pasadas: $testsPassed\n";
echo "Pruebas fallidas: $testsFailed\n";
echo "Total: " . ($testsPassed + $testsFailed) . "\n\n";

if ($testsFailed === 0) {
    echo "✓ Todas las pruebas pasaron correctamente!\n";
    exit(0);
} else {
    echo "✗ Algunas pruebas fallaron\n";
    exit(1);
}
