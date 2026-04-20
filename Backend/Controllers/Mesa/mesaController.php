<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Mesa/mesaService.php";

class MesaController
{

    private $mesaService;
    private $chaveSecreta;

    public function __construct()
    {
        $this->mesaService = new MesaService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarToken()
    {
        try {

            $token = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $token = $_SERVER['HTTP_AUTHORIZATION'];
            }

            if (isset($_SERVER['AUTHORIZATION'])) {
                $token = $_SERVER['AUTHORIZATION'];
            }

            if (empty($token)) {
                http_response_code(401);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Usuário não autenticado'
                ]);
                exit;
            }

            $partesToken = explode(' ', $token);

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

    public function validarDados($mesaDados)
    {
       

        $esquema = v::key('capacidade', v::intVal()->notEmpty());
            

        try {
            $esquema->assert($mesaDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'capacidade' => 'Capacidade inválida'
                
            ];

            $mensagemOriginal = $e->getMessages();
            $mensagemTraduzida = [];

            foreach ($mensagemOriginal as $campo => $mensagem) {
                $mensagemTraduzida[$campo] = $mensagemPersonalizada[$campo] ?? $mensagem;
            }

            return [
                'sucesso' => false,
                'mensagem' => 'Erro de validação',
                'erros' => $mensagemTraduzida
            ];
        }
    }




    public function listarMesas()
    {
        $this->validarToken();

        http_response_code(200);
        echo json_encode($this->mesaService->listarMesas());
        exit;
    }

    public function criarMesa()
    {
        try {
            $this->validarToken();


            $mesaDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($mesaDados);
            http_response_code(201);
            echo json_encode($this->mesaService->criarMesa($mesaDados));
            exit;
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }



    public function atualizarMesa()
    {
        try {

            $this->validarToken();

            $mesaDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($mesaDados);
            $idMesa = $_GET['id_mesa'];

            http_response_code(200);
            echo json_encode($this->mesaService->atualizarMesa($mesaDados, $idMesa));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function deletarMesa()
    {
        try {

            $this->validarToken();
            $idMesa = $_GET['id_Mesa'];

            http_response_code(200);
            echo json_encode($this->mesaService->deletarMesa($idMesa));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }
}
