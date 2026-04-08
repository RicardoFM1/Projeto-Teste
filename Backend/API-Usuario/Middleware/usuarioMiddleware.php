<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuarioMiddleware
{
    public static function validarMiddlewareUsuario($metodo)
    {
        try {

            $tokenJWT = null;
            $secretKey = $_ENV['JWT_SECRET_KEY'];

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            if (!$tokenJWT || empty($tokenJWT)) {
                http_response_code(401);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não autenticado'
                ]);
                exit;
            }

            $partesToken = explode(' ', $tokenJWT);

            if (count($partesToken) !== 2 || strcmp($partesToken[0], 'Bearer') !== 0) {
                http_response_code(401);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Formato token inválido, esperado: Bearer {token}'
                ]);
                exit;
            }

            $jwtDecoded = JWT::decode($partesToken[1], new Key($secretKey, 'HS256'));

            if (($jwtDecoded->dados->cargo !== "admin") && ($metodo === "GET" || $metodo === "POST" ||
                $metodo === "PUT" || $metodo === "DELETE")) {
                http_response_code(403);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => "Sem permissão para fazer isto"
                ]);
                exit;
            }
        } catch (ExpiredException $e) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Token expirado'
            ]);
            exit;
        }
    }
}
