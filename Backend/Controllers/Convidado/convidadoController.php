<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";
require_once __DIR__ . "/../../Middleware/authMiddleware.php";

class ConvidadoController
{
    protected $convidadoService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->convidadoService = new ConvidadoService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }


    public function validarDados($convidadoDados)
    {
        try {
            $confirmacaoPermitida = ['confirmado', 'não confirmado', 'cancelado'];

            $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('email', v::email())
                ->key('cpf', v::cpf())
                ->key('categoria', v::stringVal()->notEmpty())
                ->key('confirmacao', v::in($confirmacaoPermitida))
                ->key('telefone', v::phone());

            $esquema->assert($convidadoDados);
        } catch (NestedValidationException $e) {

            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 1, max 45',
                'sobrenome' => 'Sobrenome inválido, min 1, max 45',
                'email' => 'Email inválido',
                'cpf' => 'Cpf inválido',
                'categoria' => 'Categoria inválida',
                'telefone' => 'Telefone inválido',
                'confirmacao' => 'Confirmacao fora do escopo: confirmado, não confirmado ou cancelado'
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
            exit;
        }
    }



    public function listarConvidados()
    {
        Auth::validarMiddleware();
        http_response_code(200);

        echo json_encode($this->convidadoService->listarConvidados());
        exit;
    }

    public function criarConvidado()
    {
        try {

            Auth::validarMiddleware();
            $convidadoDados = json_decode(file_get_contents("php://input"), true);

            $this->validarDados($convidadoDados);
            http_response_code(201);
            echo json_encode($this->convidadoService->criarConvidado($convidadoDados));
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



    public function atualizarConvidado()
    {
        try {
            Auth::validarMiddleware();
            $convidadoDados = json_decode(file_get_contents("php://input"), true);
            $this->validarDados($convidadoDados);
            $emailConvidado = $_GET['email_convidado'];

            http_response_code(200);

            echo json_encode($this->convidadoService->atualizarConvidado($convidadoDados, $emailConvidado));
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
            Auth::validarMiddleware();
            $emailConvidado = $_GET['email_convidado'];

            http_response_code(200);

            echo json_encode($this->convidadoService->deletarConvidado($emailConvidado));
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
}
