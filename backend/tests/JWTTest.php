<?php

use PHPUnit\Framework\TestCase;

if (!defined('JWT_SECRET')) {
    define('JWT_SECRET', 'test_secret_key');
}
if (!defined('JWT_ALGORITHM')) {
    define('JWT_ALGORITHM', 'HS256');
}
if (!defined('JWT_EXPIRATION')) {
    define('JWT_EXPIRATION', 3600);
}

class JWTTest extends TestCase {
    
    public function testGenerateToken() {
        $payload = [
            'user_id' => 1,
            'username' => 'testuser'
        ];
        
        $token = JWT::generate($payload);
        
        $this->assertNotEmpty($token);
        $this->assertIsString($token);
        
        $parts = explode('.', $token);
        $this->assertCount(3, $parts);
    }
    
    public function testValidateValidToken() {
        $payload = [
            'user_id' => 1,
            'username' => 'testuser'
        ];
        
        $token = JWT::generate($payload);
        $decoded = JWT::validate($token);
        
        $this->assertNotFalse($decoded);
        $this->assertEquals(1, $decoded['user_id']);
        $this->assertEquals('testuser', $decoded['username']);
        $this->assertArrayHasKey('exp', $decoded);
    }
    
    public function testValidateInvalidToken() {
        $invalidToken = 'invalid.token.here';
        $result = JWT::validate($invalidToken);
        $this->assertFalse($result);
    }
    
    public function testValidateExpiredToken() {
        $payload = [
            'user_id' => 1,
            'username' => 'testuser',
            'exp' => time() - 3600
        ];
        
        $header = [
            'typ' => 'JWT',
            'alg' => JWT_ALGORITHM
        ];
        
        $headerEncoded = $this->base64UrlEncode(json_encode($header));
        $payloadEncoded = $this->base64UrlEncode(json_encode($payload));
        $signature = hash_hmac('sha256', "$headerEncoded.$payloadEncoded", JWT_SECRET, true);
        $signatureEncoded = $this->base64UrlEncode($signature);
        $token = "$headerEncoded.$payloadEncoded.$signatureEncoded";
        
        $result = JWT::validate($token);
        $this->assertFalse($result);
    }
    
    private function base64UrlEncode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
