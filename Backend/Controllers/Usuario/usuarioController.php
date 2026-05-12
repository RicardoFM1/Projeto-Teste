<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Usuario/usuarioService.php";
require_once __DIR__ . "/../../Middleware/middleware.php";

class UsuarioController
{
    protected $usuarioService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function validarDados($dados)
    {
        try {
            $cargosPermitidos = ['admin', 'ceremonialista'];

            $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('email', v::email())
                ->key('cpf', v::cpf())
                ->key('senha', v::stringVal()->notEmpty()->length(8, 255))
                ->key('cargo', v::in($cargosPermitidos));

            $esquema->assert($dados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 1, max 45',
                'email' => 'Email inválido',
                'cpf' => 'CPF inválido',
                'senha' => 'Senha inválida, min 8, max 255',
                'cargo' => 'Cargo fora do escopo: admin ou ceremonialista'
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

    public function apenasAdmin()
    {
        $jwt = Middleware::validarMiddleware();

        if ($jwt->dados->cargo_usuario !== 'admin') {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário sem permissão'
            ]);
            exit;
        }
    }

    public function listarUsuarios()
    {
        try {
            $this->apenasAdmin();
            http_response_code(200);
            echo json_encode($this->usuarioService->listarUsuarios());
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

    public function criarUsuario()
    {
        try {
            $this->apenasAdmin();


            http_response_code(201);
            $dados = json_decode(file_get_contents('php://input'), true);
            $this->validarDados($dados);

            echo json_encode($this->usuarioService->criarUsuario($dados));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function fazerLogin()
    {
        try {
            $dados = json_decode(file_get_contents('php://input'), true);
            http_response_code(200);
            echo json_encode($this->usuarioService->fazerLogin($dados, $this->chaveSecreta));
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

    public function atualizarUsuario()
    {
        try {
            $this->apenasAdmin();

            $dados = json_decode(file_get_contents('php://input'), true);
            $this->validarDados($dados);

            $emailUsuario = $_GET['email_usuario'];
            http_response_code(200);

            echo json_encode($this->usuarioService->atualizarUsuario($dados, $emailUsuario));
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

    public function deletarUsuario()
    {
        try {
            $this->apenasAdmin();

            $emailUsuario = $_GET['email_usuario'];
            http_response_code(200);

            echo json_encode($this->usuarioService->deletarUsuario($emailUsuario));
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
