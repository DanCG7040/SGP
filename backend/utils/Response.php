<?php

class Response {
    
    public static function success($data = null, $message = 'Operación exitosa', $code = 200) {
        http_response_code($code);
        echo json_encode([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function error($message = 'Error en la operación', $code = 400, $errors = null) {
        http_response_code($code);
        $response = [
            'success' => false,
            'message' => $message
        ];
        
        if ($errors !== null) {
            $response['errors'] = $errors;
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public static function unauthorized($message = 'No autorizado') {
        self::error($message, 401);
    }

    public static function notFound($message = 'Recurso no encontrado') {
        self::error($message, 404);
    }
}
