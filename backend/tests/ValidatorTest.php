<?php

use PHPUnit\Framework\TestCase;

class ValidatorTest extends TestCase {
    
    public function testEmailValid() {
        $this->assertTrue(Validator::email('test@example.com'));
        $this->assertTrue(Validator::email('user.name@domain.co.uk'));
    }
    
    public function testEmailInvalid() {
        $this->assertFalse(Validator::email('invalid-email'));
        $this->assertFalse(Validator::email('test@'));
        $this->assertFalse(Validator::email('@example.com'));
        $this->assertFalse(Validator::email(''));
    }
    
    public function testRequiredFieldsPresent() {
        $data = [
            'username' => 'test',
            'password' => '123456'
        ];
        $result = Validator::required($data, ['username', 'password']);
        $this->assertTrue($result);
    }
    
    public function testRequiredFieldsMissing() {
        $data = [
            'username' => 'test'
        ];
        $result = Validator::required($data, ['username', 'password']);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('password', $result);
    }
    
    public function testLengthValid() {
        $this->assertTrue(Validator::length('test', 2, 10));
        $this->assertTrue(Validator::length('abc', 2));
        $this->assertTrue(Validator::length('abc', null, 10));
    }
    
    public function testLengthInvalid() {
        $this->assertFalse(Validator::length('a', 2, 10));
        $this->assertFalse(Validator::length('verylongstring', 2, 10));
    }
    
    public function testSanitize() {
        $input = '<script>alert("xss")</script>  test  ';
        $result = Validator::sanitize($input);
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('test', $result);
        $this->assertNotEquals($input, $result);
    }
    
    public function testDocumentoValid() {
        $this->assertEquals(1, Validator::documento('1234567890'));
        $this->assertEquals(1, Validator::documento('ABC123'));
        $this->assertEquals(1, Validator::documento('123ABC456'));
    }
    
    public function testDocumentoInvalid() {
        $this->assertEquals(0, Validator::documento('123-456'));
        $this->assertEquals(0, Validator::documento('123 456'));
        $this->assertEquals(0, Validator::documento(''));
    }
}
