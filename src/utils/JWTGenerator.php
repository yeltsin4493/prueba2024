<?php

function generateJWT($user)
{
    // Definir el header del token
    $header = [
        'alg' => 'HS256', // Algoritmo de encriptación
        'typ' => 'JWT' // Tipo de token
    ];

    // Convertir el header a JSON y codificarlo en Base64
    $encoded_header = base64_encode(json_encode($header));

    // Definir el payload del token
    $payload = [
        'user_id' => $user['id'],
        'email' => $user['email'],
        'exp' => time() + (60 * 60) // Expiración del token en 1 hora
    ];

    // Convertir el payload a JSON y codificarlo en Base64
    $encoded_payload = base64_encode(json_encode($payload));

    // Crear la parte de la firma (sin firmar aún)
    $signature = hash_hmac('sha256', $encoded_header . '.' . $encoded_payload, 'tu_clave_secreta', true);

    // Codificar la firma en Base64
    $encoded_signature = base64_encode($signature);

    // Concatenar el header, el payload y la firma para formar el token completo
    $jwt_token = $encoded_header . '.' . $encoded_payload . '.' . $encoded_signature;

    return $jwt_token;
}

function jwt_decode($token)
{
    
    // Verificar si el token está vacío
    if (empty($token)) {
        return false;
    }
    
    // Dividir el token en sus partes (encabezado, carga útil y firma)
    $token_parts = explode('.', $token);
    
    // Verificar si hay tres partes
    if (count($token_parts) !== 3) {
        return false;
    }
    
    // Decodificar la carga útil (parte del token que contiene los datos)
    $payload = base64_decode($token_parts[1]);
    
    // Convertir la carga útil decodificada en un array asociativo
    $decoded_payload = json_decode($payload, true);

    // Verificar si el token ha expirado
    if (isset($decoded_payload['exp']) && $decoded_payload['exp'] < time()) {
        return false; // Token expirado
    }

    // Devolver true si todas las verificaciones pasan, de lo contrario, false
    return true;
}
