<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Acompanhante/acompanhanteService.php";
require_once __DIR__ . "/../../Middleware/middleware.php";

class AcompanhanteController
{
    protected $acompanhanteService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->acompanhanteService = new AcompanhanteService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarDados($dados)
    {
        try {
          

            $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('email', v::email())
                ->key('cpf', v::cpf())
                ->key('idade', v::intVal()->notEmpty())
                ->key('convidado_idconvidado', v::intVal()->notEmpty());
            


            $esquema->assert($dados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido',
                'sobrenome' => 'Sobrenome inválido',
                'email' => 'Email inválido',
                'cpf' => 'Cpf inválido',
                'idade' => 'Idade inválida',
                'convidado_idconvidado' => 'Referência do convidado inválida'
                

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

    public function listarAcompanhantes()
    {
        try {
            Middleware::validarMiddleware();
            http_response_code(200);
            echo json_encode($this->acompanhanteService->listarAcompanhantes());
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

    public function criarAcompanhante()
    {
        try {
            Middleware::validarMiddleware();

            http_response_code(201);
            $dados = json_decode(file_get_contents('php://input'), true);
            echo json_encode($this->acompanhanteService->criarAcompanhante($dados));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }


    public function atualizarAcompanhante()
    {
        try {
            Middleware::validarMiddleware();
            $dados = json_decode(file_get_contents('php://input'), true);
            $emailAcompanhante = $_GET['email_acompanhante'];
            http_response_code(200);

            echo json_encode($this->acompanhanteService->atualizarAcompanhante($dados, $emailAcompanhante));
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

    public function deletarAcompanhante()
    {
        try {
            Middleware::validarMiddleware();
            $emailAcompanhante = $_GET['email_acompanhante'];
            http_response_code(200);

            echo json_encode($this->acompanhanteService->deletarAcompanhante($emailAcompanhante));
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
