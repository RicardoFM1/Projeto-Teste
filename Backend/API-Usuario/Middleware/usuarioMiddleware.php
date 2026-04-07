<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsuarioMiddleware
{
    public static function validarMiddlewareUsuario($metodo)
    {
        $tokenJWT = null;
        $secretKey = $_ENV['JWT_SECRET_KEY'];

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
        }
        if (isset($_SERVER['AUTHORIZATION'])) {
            $tokenJWT = trim($_SERVER['AUTHORIZATION']);
        }

        if(!$tokenJWT || empty($tokenJWT)){
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'message' => 'Usuário não autenticado'
            ]);
            exit;
        }

        $partesToken = explode(' ', $tokenJWT);

        if(count($partesToken) !== 2 || strcmp($partesToken[0], 'Bearer') !== 0){
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'message' => 'Formato token inválido, esperado: Bearer {token}'
            ]);
            exit;
        }

        $jwtDecoded = JWT::decode($partesToken[1], new Key($secretKey, 'HS256'));

        if(($jwtDecoded->dados->cargo !== "admin") && ($metodo === "GET" || $metodo === "POST" ||
        $metodo === "PUT" || $metodo === "DELETE")){
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'message' => "Sem permissão para fazer isto"
            ]);
            exit;
        }



        
    }
}
