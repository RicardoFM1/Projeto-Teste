<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../Service/usuarioService.php";

class UsuarioController
{

    protected $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function pegarToken()
    {
        $token = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $token = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['AUTHORIZATION'])) {
            $token = $_SERVER['AUTHORIZATION'];
        }

        return $token;
    }

    public function validarMiddleware()
    {
        try {

            $token = $this->pegarToken();

            return $this->usuarioService->validarToken($token);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage(),

            ]);
            exit;
        }
    }

    public function apenasAdmin()
    {
        $jwt = $this->validarMiddleware();
        if ($jwt->dados->cargo_usuario !== "admin") {
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário sem permissão'
            ]);
            exit;
        }
    }

    public function validarDados($usuarioDados)
    {
        $cargosPermitidos = ['admin', 'ceremonialista'];

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(4, 50))
            ->key('email', v::email())
            ->key('senha', v::stringVal()->length(8, 50))
            ->key('cpf', v::cpf())
            ->key('cargo', v::in($cargosPermitidos));

        try {
            $esquema->assert($usuarioDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválida, min 4, max 50',
                'email' => 'Email inválido',
                'senha' => 'Senha inválida, min 8, max 50',
                'cpf' => 'Cpf inválido',
                'cargo' => 'Cargo inválido, é aceito apenas admin ou ceremonialista'
            ];

            $mensagemOriginal = $e->getMessages();
            $mensagemFormatada = [];

            foreach ($mensagemOriginal as $campo => $mensagem) {
                $mensagemFormatada[$campo] = $mensagemPersonalizada[$campo] ?? $mensagem;
            }

            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro de validação',
                'erros' => $mensagemFormatada
            ]);
            exit;
        }
    }
    // Formatar cpf só quando for enviar para o banco, ou seja, no service em criar e atualizar.
    public function listarUsuarios()
    {
        $this->apenasAdmin();
        http_response_code(200);
        echo json_encode($this->usuarioService->listarUsuarios());
    }

    public function criarUsuario()
    {
        try {
            $this->apenasAdmin();
            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;

            $this->validarDados($usuarioDados);
            http_response_code(201);
            echo json_encode($this->usuarioService->criarUsuario($usuarioDados));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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

            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;
            http_response_code(200);
            echo json_encode($this->usuarioService->fazerLogin($usuarioDados));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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
            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $emailUsuario = $_GET['email_usuario'] ?? null;
            $tokenJWT = $this->pegarToken();

            $this->validarDados($usuarioDados);
            http_response_code(200);
            echo json_encode($this->usuarioService->atualizarUsuario($usuarioDados, $emailUsuario, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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
            $emailUsuario = $_GET['email_usuario'] ?? null;
            $tokenJWT = $this->pegarToken();

            http_response_code(200);
            echo json_encode($this->usuarioService->deletarUsuario($emailUsuario, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }
}
