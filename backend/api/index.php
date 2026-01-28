<?php

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/Database.php';
require_once __DIR__ . '/../utils/JWT.php';
require_once __DIR__ . '/../utils/Response.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Paciente.php';
require_once __DIR__ . '/../models/Catalogo.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/PacienteController.php';
require_once __DIR__ . '/../controllers/CatalogoController.php';

$method = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/api', '', $uri);
$uri = trim($uri, '/');
$segments = explode('/', $uri);

try {
    if ($segments[0] === 'auth' && $method === 'POST') {
        $controller = new AuthController();
        $controller->login();
        exit;
    }

    if ($segments[0] === 'catalogos') {
        $controller = new CatalogoController();
        
        if ($segments[1] === 'departamentos' && $method === 'GET') {
            $controller->getDepartamentos();
        } elseif ($segments[1] === 'municipios' && $method === 'GET') {
            $controller->getMunicipios();
        } elseif ($segments[1] === 'tipos-documento' && $method === 'GET') {
            $controller->getTiposDocumento();
        } elseif ($segments[1] === 'generos' && $method === 'GET') {
            $controller->getGeneros();
        } else {
            Response::notFound('Endpoint no encontrado');
        }
        exit;
    }

    if ($segments[0] === 'pacientes') {
        AuthMiddleware::handle();
        $controller = new PacienteController();
        
        if ($method === 'GET' && empty($segments[1])) {
            $controller->index();
        } elseif ($method === 'GET' && is_numeric($segments[1])) {
            $controller->show((int)$segments[1]);
        } elseif ($method === 'POST' && empty($segments[1])) {
            $controller->store();
        } elseif ($method === 'PUT' && is_numeric($segments[1])) {
            $controller->update((int)$segments[1]);
        } elseif ($method === 'DELETE' && is_numeric($segments[1])) {
            $controller->delete((int)$segments[1]);
        } else {
            Response::notFound('Endpoint no encontrado');
        }
        exit;
    }

    Response::notFound('Endpoint no encontrado');

} catch (Exception $e) {
    Response::error('Error interno del servidor: ' . $e->getMessage(), 500);
}
