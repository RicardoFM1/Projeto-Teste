<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Usuario/usuarioService.php";
require_once __DIR__ . "/../../Middleware/authMiddleware.php";

class UsuarioController
{
    protected $usuarioService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }


    public function validarDados($usuarioDados)
    {
        try {
            $cargosPermitidos = ['admin', 'ceremonialista'];
        
            $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
                ->key('email', v::email())
                ->key('senha', v::stringVal()->notEmpty()->length(8, 255))
                ->key('cpf', v::cpf())
                ->key('cargo', v::in($cargosPermitidos));

            $esquema->assert($usuarioDados);
        } catch (NestedValidationException $e) {

            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 1, max 45',
                'email' => 'Email inválido',
                'senha' => 'Senha inválida, min 8, max 255',
                'cpf' => 'Cpf inválido',
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
            exit;
        }
    }


    public function apenasAdmin()
    {
        $jwt = Auth::validarMiddleware();

        if ($jwt->dados->cargo_usuario !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário sem permissão'
            ]);
            exit;
        }
    }

    public function listarUsuarios()
    {

        echo json_encode($this->usuarioService->listarUsuarios());
        exit;
    }

    public function criarUsuario()
    {
        try {


            $usuarioDados = json_decode(file_get_contents("php://input"), true);

            $this->validarDados($usuarioDados);

            echo json_encode($this->usuarioService->criarUsuario($usuarioDados));
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

    public function fazerLogin()
    {
        try {
            $usuarioDados = json_decode(file_get_contents("php://input"), true);

            echo json_encode($this->usuarioService->fazerLogin($usuarioDados, $this->chaveSecreta));
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
            $usuarioDados = json_decode(file_get_contents("php://input"), true);
            $this->validarDados($usuarioDados);
            $emailUsuario = $_GET['email_usuario'];

            echo json_encode($this->usuarioService->atualizarUsuario($usuarioDados, $emailUsuario));
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

            echo json_encode($this->usuarioService->deletarUsuario($emailUsuario));
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
