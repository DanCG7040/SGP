<?php

class Validator {
    
    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($data, $fields) {
        $errors = [];
        
        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = "El campo $field es requerido";
            }
        }
        
        return empty($errors) ? true : $errors;
    }

    public static function length($value, $min = null, $max = null) {
        $len = strlen($value);
        
        if ($min !== null && $len < $min) {
            return false;
        }
        
        if ($max !== null && $len > $max) {
            return false;
        }
        
        return true;
    }

    public static function sanitize($value) {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    public static function documento($value) {
        return preg_match('/^[A-Z0-9]+$/i', $value);
    }
}
