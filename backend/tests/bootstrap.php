<?php

define('JWT_SECRET', 'test_secret_key');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600);

require_once __DIR__ . '/../config/database.php';
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
