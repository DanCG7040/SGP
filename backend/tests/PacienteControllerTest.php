<?php

use PHPUnit\Framework\TestCase;

class PacienteControllerTest extends TestCase {
    
    private $controller;
    
    protected function setUp(): void {
        $this->controller = new PacienteController();
    }
    
    public function testValidatePacienteRequiredFields() {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePaciente');
        $method->setAccessible(true);
        
        $data = [];
        $errors = $method->invoke($this->controller, $data);
        
        $this->assertIsArray($errors);
        $this->assertNotEmpty($errors);
    }
    
    public function testValidatePacienteEmailInvalid() {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePaciente');
        $method->setAccessible(true);
        
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
        
        $errors = $method->invoke($this->controller, $data);
        
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('correo', $errors);
    }
    
    public function testValidatePacienteEmailValid() {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePaciente');
        $method->setAccessible(true);
        
        $data = [
            'tipo_documento_id' => 1,
            'numero_documento' => '1234567890',
            'nombre1' => 'Juan',
            'apellido1' => 'Pérez',
            'genero_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'correo' => 'juan@email.com'
        ];
        
        $errors = $method->invoke($this->controller, $data);
        
        $this->assertIsArray($errors);
        if (empty($errors)) {
            $this->assertTrue(true, 'No hay errores de validación');
        } else {
            $this->assertArrayNotHasKey('correo', $errors, 'Email válido no debe generar error');
        }
    }
    
    public function testValidatePacienteDocumentoInvalid() {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePaciente');
        $method->setAccessible(true);
        
        $data = [
            'tipo_documento_id' => 1,
            'numero_documento' => '123-456',
            'nombre1' => 'Juan',
            'apellido1' => 'Pérez',
            'genero_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'correo' => 'juan@email.com'
        ];
        
        $errors = $method->invoke($this->controller, $data);
        
        $this->assertIsArray($errors);
        $this->assertArrayHasKey('numero_documento', $errors);
    }
    
    public function testValidatePacienteLengthInvalid() {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validatePaciente');
        $method->setAccessible(true);
        
        $data = [
            'tipo_documento_id' => 1,
            'numero_documento' => '1234567890',
            'nombre1' => 'J',
            'apellido1' => 'P',
            'genero_id' => 1,
            'departamento_id' => 1,
            'municipio_id' => 1,
            'correo' => 'juan@email.com'
        ];
        
        $errors = $method->invoke($this->controller, $data);
        
        $this->assertIsArray($errors);
    }
}
