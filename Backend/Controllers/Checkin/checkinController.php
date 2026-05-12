<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Checkin/checkinService.php";
require_once __DIR__ . "/../../Middleware/middleware.php";

class CheckinController
{
    protected $checkinService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->checkinService = new CheckinService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarDados($dados)
    {
        try {


            $esquema = v::key('convidado_idconvidado', v::intVal()->notEmpty());



            $esquema->assert($dados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'convidado_idconvidado' => 'Referência do convidado inválido'
            ];

            $mensagemOriginal = $e->getMessages();
            $mensagemTraduzida = [];

            foreach ($mensagemOriginal as $campo => $mensagem) {
                $mensagemTraduzida[$campo] = $mensagemPersonalizada[$campo] ?? $mensagem;
            }

            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erros de validação',
                'erros' => $mensagemTraduzida
            ]);
            http_response_code(400);
            exit;
        }
    }

    public function listarCheckins()
    {
        try {
            Middleware::validarMiddleware();
            http_response_code(200);
            echo json_encode($this->checkinService->listarCheckins());
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

    public function criarCheckin()
    {
        try {
            $jwt = Middleware::validarMiddleware();

            http_response_code(201);
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->validarDados($dados);

            echo json_encode($this->checkinService->criarCheckin($dados, $jwt));
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
