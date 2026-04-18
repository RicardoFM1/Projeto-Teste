<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "../../Convidado/convidadoController.php";

class ConvidadoController
{

    protected $convidadoService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->convidadoService = new ConvidadoService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }


    public function validarToken()
    {
        $tokenJWT = null;

        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $tokenJWT = $_SERVER['HTTP_AUTHORIZATION'];
        }
        if (isset($_SERVER['AUTHORIZATION'])) {
            $tokenJWT = $_SERVER['AUTHORIZATION'];
        }

        if (empty($tokenJWT)) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário não autenticado'
            ]);
        }

        try {

            $partesToken = explode(' ', $tokenJWT);

            if (count($partesToken) !== 2) {
                http_response_code(401);
                echo json_encode([
                    'sucesso' => false,
                    'mensagem' => 'Token inválido'
                ]);
            }

            return JWT::decode($partesToken[1], new Key($this->chaveSecreta, 'HS256'));
        } catch (ExpiredException $e) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Token expirado'
            ]);
        }
    }



    public function validarDados($convidadoDados)
    {
        $confirmacaoPermitida = ['confirmado', 'não confirmado', 'cancelado'];

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(4, 50))
            ->key('sobrenome', v::stringVal()->notEmpty()->length(4, 50))
            ->key('email', v::email())
            ->key('telefone', v::phone())
            ->key('cpf', v::cpf())
            ->key('confirmacao', v::in($confirmacaoPermitida))
            ->key('categoria', v::stringVal()->notEmpty());

        try {
            $esquema->assert($convidadoDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválida, min 4, max 50',
                'email' => 'Email inválido',
                'cpf' => 'Cpf inválido',
                'confirmacao' => 'Confirmação inválida, é aceito apenas confirmado, não confirmado ou cancelado',
                'categoria' => 'Categoria inválida'
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
    public function listarConvidados()
    {
        $this->validarToken();
        // Aqui só valida token para ver se está autenticado.
        http_response_code(200);
        echo json_encode($this->convidadoService->listarConvidados());
    }

    public function criarConvidado()
    {
        try {

            $convidadoDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $this->validarToken();
            $this->validarDados($convidadoDados);
            http_response_code(201);
            echo json_encode($this->convidadoService->criarConvidado($convidadoDados));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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

            $convidadoDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $emailConvidado = $_GET['email_convidado'] ?? null;
            $this->validarToken();

            $this->validarDados($convidadoDados);
            http_response_code(200);
            echo json_encode($this->convidadoService->atualizarConvidado($convidadoDados, $emailConvidado));
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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

            $emailConvidado = $_GET['email_convidado'] ?? null;
            $this->validarToken();


            http_response_code(200);
            echo json_encode($this->convidadoService->deletarConvidado($emailConvidado));
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
