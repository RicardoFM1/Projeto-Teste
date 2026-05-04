<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RetrieveService
{

    public function retrieveUsuario()
    {
        $tokenJWT = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenJWT = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['AUTHORIZATION'])) {
            $tokenJWT = $_SERVER['AUTHORIZATION'];
        }

        if (empty($tokenJWT)) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário não autenticado'
            ]);
            exit;
        }

        $partesToken = explode(' ', $tokenJWT);


        if (count($partesToken) !== 2) {
            throw new Exception('Token inválido', 401);
        }

        try {
            return JWT::decode($partesToken[1], new Key($_ENV['JWT_SECRET_KEY'], 'HS256'));
        } catch (ExpiredException $e) {
            throw new Exception('Token expirado', 401);
        }
    }
}
