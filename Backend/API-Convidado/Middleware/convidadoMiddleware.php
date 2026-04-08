<?php

use Firebase\JWT\ExpiredException;


class ConvidadoMiddleware
{
    public static function validarMiddlewareConvidado()
    {
        try {

            $tokenJWT = null;
            

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
