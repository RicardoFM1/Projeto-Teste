<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";
require_once __DIR__ . "/../../Middleware/middleware.php";

class ConvidadoController
{
    protected $convidadoService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->convidadoService = new ConvidadoService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarDados($dados)
    {
        try {
            $confirmacaoPermitida = ['confirmado', 'não confirmado', 'cancelado'];

            $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('email', v::email())
                ->key('cpf', v::cpf())
                ->key('categoria', v::stringVal()->notEmpty())
                ->key('confirmacao', v::in($confirmacaoPermitida))
                ->key('mesa_idmesa', v::intVal())
                ->key('telefone', v::phone());


            $esquema->assert($dados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido',
                'sobrenome' => 'Sobrenome inválido',
                'email' => 'Email inválido',
                'cpf' => 'Cpf inválido',
                'categoria' => 'Categoria inválida',
                'confirmacao' => 'Confirmacao fora do escopo: confirmado, não confirmado e cancelado',
                'mesa_idmesa' => 'Referencia da mesa inválida',
                'telefone' => 'Telefone inválido'

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

    public function listarConvidados()
    {
        try {
            Middleware::validarMiddleware();
            http_response_code(200);
            echo json_encode($this->convidadoService->listarConvidados());
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

    public function criarConvidado()
    {
        try {
            Middleware::validarMiddleware();

            http_response_code(201);
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->validarDados($dados);

            echo json_encode($this->convidadoService->criarConvidado($dados));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }


    public function atualizarConvidado()
    {
        try {
            Middleware::validarMiddleware();
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->validarDados($dados);

            $emailConvidado = $_GET['email_convidado'];
            http_response_code(200);

            echo json_encode($this->convidadoService->atualizarConvidado($dados, $emailConvidado));
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

    public function deletarConvidado()
    {
        try {
            Middleware::validarMiddleware();
            $emailConvidado = $_GET['email_convidado'];
            http_response_code(200);

            echo json_encode($this->convidadoService->deletarConvidado($emailConvidado));
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
