<?php

require_once __DIR__ . '/../config/database.php';

$config = require __DIR__ . '/../config/database.php';

try {
    $dsn = "mysql:host={$config['host']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
    
    echo "Conectado a MySQL correctamente\n\n";
    
    $migrationFile = __DIR__ . '/migrations/01_create_tables.sql';
    $seederFile = __DIR__ . '/seeders/02_seed_data.sql';
    
    if (!file_exists($migrationFile)) {
        die("Error: No se encuentra el archivo de migraciones\n");
    }
    
    echo "Ejecutando migraciones (creando tablas)...\n";
    $sql = file_get_contents($migrationFile);
    $pdo->exec($sql);
    echo "Migraciones ejecutadas correctamente\n\n";
    
    if (file_exists($seederFile)) {
        echo "Ejecutando seeders (insertando datos iniciales)...\n";
        $sql = file_get_contents($seederFile);
        $pdo->exec($sql);
        echo "Seeders ejecutados correctamente\n\n";
    }
    
    echo "Base de datos configurada correctamente\n";
    echo "Usuario admin creado:\n";
    echo "  Usuario: admin\n";
    echo "  Contraseña: 1234567890\n";
    
} catch (PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
