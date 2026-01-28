<?php

require_once __DIR__ . '/bootstrap.php';

class PerformanceTest
{
    private $baseUrl = 'http://localhost:8000';
    private $token = null;
    private $timeoutSeconds = 8;
    
    public function __construct()
    {
        $this->authenticate();
    }
    
    private function authenticate()
    {
        $ch = curl_init($this->baseUrl . '/api/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => 'admin',
            'password' => '1234567890'
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            if (isset($data['data']['token'])) {
                $this->token = $data['data']['token'];
                echo "Autenticación exitosa\n";
                return true;
            }
        }
        
        echo "Error en autenticación. Asegúrate de que el servidor esté corriendo.\n";
        return false;
    }
    
    private function makeRequest($url, $method = 'GET', $data = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->token
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeoutSeconds);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeoutSeconds);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $startTime = microtime(true);
        $response = curl_exec($ch);
        $endTime = microtime(true);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $totalTime = ($endTime - $startTime) * 1000;
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'response_time' => $totalTime,
            'response' => $response
        ];
    }
    
    public function testConcurrentRequests($numRequests = 50, $concurrency = 10)
    {
        echo "\n=== Prueba de Solicitudes Concurrentes ===\n";
        echo "Total de solicitudes: $numRequests\n";
        echo "Concurrencia: $concurrency\n\n";
        
        $results = [];
        $startTime = microtime(true);
        
        $chunks = array_chunk(range(1, $numRequests), $concurrency);
        
        foreach ($chunks as $chunk) {
            $mh = curl_multi_init();
            $handles = [];
            
            foreach ($chunk as $i) {
                $ch = curl_init($this->baseUrl . '/api/pacientes');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $this->token
                ]);
                curl_multi_add_handle($mh, $ch);
                $handles[] = $ch;
            }
            
            $running = null;
            do {
                curl_multi_exec($mh, $running);
                curl_multi_select($mh);
            } while ($running > 0);
            
            foreach ($handles as $ch) {
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000;
                $results[] = [
                    'http_code' => $httpCode,
                    'response_time' => $totalTime
                ];
                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
            }
            
            curl_multi_close($mh);
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        
        $this->analyzeResults($results, $totalTime);
    }
    
    public function testSequentialRequests($numRequests = 50)
    {
        echo "\n=== Prueba de Solicitudes Secuenciales ===\n";
        echo "Total de solicitudes: $numRequests\n\n";
        
        $results = [];
        $startTime = microtime(true);
        
        for ($i = 1; $i <= $numRequests; $i++) {
            $result = $this->makeRequest($this->baseUrl . '/api/pacientes', 'GET');
            $results[] = $result;
            
            if ($i % 10 === 0) {
                echo "Procesadas: $i/$numRequests\n";
            }
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        
        $this->analyzeResults($results, $totalTime);
    }
    
    public function testMixedOperations($numOperations = 30)
    {
        echo "\n=== Prueba de Operaciones Mixtas (GET pacientes, GET catálogos) ===\n";
        echo "Total de operaciones: $numOperations\n\n";
        
        $results = [];
        $startTime = microtime(true);
        
        for ($i = 1; $i <= $numOperations; $i++) {
            $operation = $i % 2;
            
            if ($operation === 0) {
                $result = $this->makeRequest($this->baseUrl . '/api/pacientes', 'GET');
            } else {
                $result = $this->makeRequest($this->baseUrl . '/api/catalogos/departamentos', 'GET');
            }
            
            $results[] = $result;
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;
        
        $this->analyzeResults($results, $totalTime);
    }
    
    private function analyzeResults($results, $totalTime)
    {
        $successful = 0;
        $failed = 0;
        $responseTimes = [];
        
        foreach ($results as $result) {
            if ($result['http_code'] === 200) {
                $successful++;
            } else {
                $failed++;
            }
            $responseTimes[] = $result['response_time'];
        }
        
        if (empty($responseTimes)) {
            echo "No se obtuvieron resultados\n\n";
            return;
        }
        
        sort($responseTimes);
        $count = count($responseTimes);
        
        $min = $responseTimes[0];
        $max = $responseTimes[$count - 1];
        $avg = array_sum($responseTimes) / $count;
        $median = $count % 2 === 0 
            ? ($responseTimes[$count/2 - 1] + $responseTimes[$count/2]) / 2
            : $responseTimes[($count - 1) / 2];
        $p95Index = (int)($count * 0.95);
        $p99Index = (int)($count * 0.99);
        $p95 = $responseTimes[min($p95Index, $count - 1)];
        $p99 = $responseTimes[min($p99Index, $count - 1)];
        
        echo "--- Resultados ---\n";
        echo "Tiempo total: " . number_format($totalTime, 2) . " ms\n";
        echo "Solicitudes exitosas: $successful\n";
        echo "Solicitudes fallidas: $failed\n";
        echo "Tasa de éxito: " . number_format(($successful / $count) * 100, 2) . "%\n";
        echo "\n--- Tiempos de Respuesta (ms) ---\n";
        echo "Mínimo: " . number_format($min, 2) . " ms\n";
        echo "Máximo: " . number_format($max, 2) . " ms\n";
        echo "Promedio: " . number_format($avg, 2) . " ms\n";
        echo "Mediana: " . number_format($median, 2) . " ms\n";
        echo "Percentil 95: " . number_format($p95, 2) . " ms\n";
        echo "Percentil 99: " . number_format($p99, 2) . " ms\n";
        if ($totalTime > 0) {
            echo "Solicitudes por segundo: " . number_format(($count / ($totalTime / 1000)), 2) . "\n";
        }
        echo "\n";
    }
    
    public function testDatabaseConnectionPool()
    {
        echo "\n=== Prueba de Pool de Conexiones ===\n";
        
        $startTime = microtime(true);
        $connections = [];
        
        for ($i = 0; $i < 20; $i++) {
            $db = Database::getInstance();
            $connections[] = $db;
        }
        
        $endTime = microtime(true);
        $time = ($endTime - $startTime) * 1000;
        
        echo "Conexiones creadas: " . count($connections) . "\n";
        echo "Tiempo total: " . number_format($time, 2) . " ms\n";
        echo "Tiempo por conexión: " . number_format($time / count($connections), 2) . " ms\n";
        
        $db1 = Database::getInstance();
        $db2 = Database::getInstance();
        
        if ($db1 === $db2) {
            echo "Singleton funcionando correctamente: misma instancia reutilizada\n";
        } else {
            echo "ERROR: Singleton no funciona correctamente\n";
        }
        echo "\n";
    }
}

if (php_sapi_name() === 'cli' && isset($argv[0]) && realpath($argv[0]) === __FILE__) {
    $test = new PerformanceTest();
    
    echo "========================================\n";
    echo "PRUEBAS DE DESEMPEÑO Y OPTIMIZACIÓN\n";
    echo "========================================\n";
    
    $test->testDatabaseConnectionPool();
    $test->testSequentialRequests(50);
    $test->testConcurrentRequests(50, 10);
    $test->testMixedOperations(30);
    
    echo "========================================\n";
    echo "PRUEBAS COMPLETADAS\n";
    echo "========================================\n";
}
