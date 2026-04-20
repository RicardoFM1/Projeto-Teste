<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Usuario/usuarioService.php";

class UsuarioController
{

    private $usuarioService;
    private $chaveSecreta;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
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

    public function validarDados($usuarioDados)
    {
        $cargosPermitidos = ['admin', 'ceremonialista'];

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('email', v::email())
            ->key('senha', v::stringVal()->notEmpty()->length(8, 255))
            ->key('cpf', v::cpf())
            ->key('cargo', v::in($cargosPermitidos));

        try {
            $esquema->assert($usuarioDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 4, max 45',
                'email' => 'Email inválido',
                'senha' => 'Senha inválida, min 8, max 255',
                'cpf' => 'Cpf inválido',
                'cargo' => 'Cargo fora dos padrões, permitido apenas admin ou ceremonialista'
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

    public function apenasAdmin()
    {
        $tokenJWT = $this->validarToken();

        if ($tokenJWT->dados->cargo_usuario !== 'admin') {
            http_response_code(403);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Sem permissão'
            ]);
            exit;
        }
    }


    public function listarUsuarios()
    {
        $this->apenasAdmin();
        http_response_code(200);
        echo json_encode($this->usuarioService->listarUsuarios());
        exit;
    }

    public function criarUsuario()
    {
        try {
            $this->apenasAdmin();

            $usuarioDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($usuarioDados);
            http_response_code(201);
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

            $usuarioDados = json_decode(file_get_contents('php://input'), true) ?? null;
            http_response_code(200);
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
            $usuarioDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($usuarioDados);
            $emailUsuario = $_GET['email_usuario'];

            http_response_code(200);
            echo json_encode($this->usuarioService->atualizarUsuario($usuarioDados, $emailUsuario));
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
