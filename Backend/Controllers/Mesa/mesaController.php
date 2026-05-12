<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Mesa/mesaService.php";
require_once __DIR__ . "/../../Middleware/middleware.php";

class MesaController
{
    protected $mesaService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->mesaService = new MesaService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarDados($dados)
    {
        try {


            $esquema = v::key('capacidade', v::intVal()->notEmpty())
                ->key('restricao', v::strinVal());


            $esquema->assert($dados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'capacidade' => 'Capacidade inválida',
                'restricao' => 'Restricao inválida'

            ];

            $mensagemOriginal = $e->getMessages();
            $mensagemTraduzida = [];

            foreach ($mensagemOriginal as $campo => $mensagem) {
                $mensagemTraduzida[$campo] = $mensagemPersonalizada[$campo] ?? $mensagem;
            }

            return [
                'sucesso' => false,
                'mensagem' => 'Erros de validação',
                'erros' => $mensagemTraduzida
            ];
        }
    }

    public function listarMesas()
    {
        try {
            Middleware::validarMiddleware();
            http_response_code(200);
            echo json_encode($this->mesaService->listarMesas());
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

    public function criarMesa()
    {
        try {
            Middleware::validarMiddleware();

            http_response_code(201);
            $dados = json_decode(file_get_contents('php://input'), true);
            echo json_encode($this->mesaService->criarMesa($dados));
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
            Middleware::validarMiddleware();
            $dados = json_decode(file_get_contents('php://input'), true);
            $idMesa = $_GET['id_mesa'];
            http_response_code(200);

            echo json_encode($this->mesaService->atualizarMesa($dados, $idMesa));
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

    public function deletarMesa()
    {
        try {
            Middleware::validarMiddleware();
            $idMesa = $_GET['id_mesa'];
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
