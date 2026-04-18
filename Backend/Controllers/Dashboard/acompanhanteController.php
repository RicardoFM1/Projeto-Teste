<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Dashboard/dashboardService.php";

class DashboardController
{

    protected $dashboardService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }


    public function validarToken()
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

        try {

            $partesToken = explode(' ', $tokenJWT);

            if (count($partesToken) !== 2) {
                http_response_code(401);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Token inválido'
                ]);
                exit;
            }

            return JWT::decode($partesToken[1], new Key($this->chaveSecreta, 'HS256'));
        } catch (ExpiredException $e) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Token expirado'
            ]);
            exit;
        }
    }

    public function apenasAdmin () {
        $tokenJWT = $this->validarToken();

        if($tokenJWT->dados->cargo_usuario !== 'admin'){
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário sem permissão'
            ]);
            exit;
        }
    }


   
    // Formatar cpf só quando for enviar para o banco, ou seja, no service em criar e atualizar.
    public function listarDashboard()
    {
        
        $this->apenasAdmin();
        http_response_code(200);
        echo json_encode($this->dashboardService->listarDashboard());
        exit;
    }

   
}
