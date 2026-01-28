<?php

class AuthMiddleware {
    
    public static function handle() {
        $token = JWT::getTokenFromHeader();
        
        if (!$token) {
            Response::unauthorized('Token no proporcionado');
        }

        $payload = JWT::validate($token);
        
        if (!$payload) {
            Response::unauthorized('Token inválido o expirado');
        }

        return $payload;
    }
}
