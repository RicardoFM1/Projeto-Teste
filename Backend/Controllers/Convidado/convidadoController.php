<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";

class ConvidadoController
{

    private $convidadoService;
    private $chaveSecreta;

    public function __construct()
    {
        $this->convidadoService = new UsuarioService();
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

    public function validarDados($convidadoDados)
    {
        $confirmacaoPermitida = ['confirmado', 'não confirmado', 'cancelado'];

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('email', v::email())
            ->key('cpf', v::cpf())
            ->key('telefone', v::phone())
            ->key('categoria', v::stringVal()->notEmpty())
            ->key('confirmacao', v::in($confirmacaoPermitida));

        try {
            $esquema->assert($convidadoDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 4, max 45',
                'sobrenome' => 'Nome inválido, min 4, max 45',
                'email' => 'Email inválido',
                'telefone' => 'Telefone inválido',
                'cpf' => 'Cpf inválido',
                'categoria' => 'categoria inválida',
                'confirmacao' => 'Confirmacao inválida, é permitido apenas confirmado, não confirmado e cancelado'
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




    public function listarConvidados()
    {
        $this->validarToken();

        http_response_code(200);
        echo json_encode($this->convidadoService->listarUsuarios());
        exit;
    }

    public function criarConvidado()
    {
        try {
            $this->validarToken();


            $convidadoDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($convidadoDados);
            http_response_code(201);
            echo json_encode($this->convidadoService->criarUsuario($convidadoDados));
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

            $this->validarToken();

            $convidadoDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($convidadoDados);
            $emailConvidado = $_GET['email_convidado'];

            http_response_code(200);
            echo json_encode($this->convidadoService->atualizarUsuario($convidadoDados, $emailConvidado));
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

            $this->validarToken();
            $emailConvidado = $_GET['email_convidado'];

            http_response_code(200);
            echo json_encode($this->convidadoService->deletarUsuario($emailConvidado));
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
