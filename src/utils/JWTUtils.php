<?php
require_once('../utils/JWTGenerator.php');

class JWTUtils
{
    // Verificar si el token JWT es válido
    public static function handleVerifyToken($token)
    {

        // Puedes utilizar la función jwt_decode() si ya tienes un token válido
        $decoded_token = jwt_decode($token);


        // Devolver true si el token es válido, de lo contrario, false
        return $decoded_token ? true : false;
    }

    // Obtener el token JWT de la cabecera Authorization
    public static function getTokenFromHeaders()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization']) && preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
            return $matches[1];
        }
        return null;
    }
}
