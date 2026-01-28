<?php

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $required = Validator::required($data, ['username', 'password']);
        if ($required !== true) {
            Response::error('Datos incompletos', 400, $required);
        }

        $username = Validator::sanitize($data['username']);
        $password = $data['password'];

        $user = $this->userModel->findByUsername($username);
        
        if (!$user) {
            Response::error('Usuario o contraseña incorrectos', 401);
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            Response::error('Usuario o contraseña incorrectos', 401);
        }

        $token = JWT::generate([
            'user_id' => $user['id'],
            'username' => $user['username']
        ]);

        Response::success([
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ]
        ], 'Login exitoso');
    }
}
