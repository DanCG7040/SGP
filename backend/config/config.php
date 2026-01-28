<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

define('JWT_SECRET', 'sgp_secret_key_2024_development');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600);

date_default_timezone_set('America/Bogota');
