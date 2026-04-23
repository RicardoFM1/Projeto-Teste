<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";

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
            exit;
        }

        $partesToken = explode(' ', $tokenJWT);


        if (count($partesToken) !== 2) {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Token inválido'
            ]);
            exit;
        }

        try {
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
        try {
        $confirmacaoPermitida = ['confirmado', 'não confirmado', 'cancelado'];

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('email', v::email())
            ->key('cpf', v::cpf())
            ->key('telefone', v::phone())
            ->key('categoria', v::stringVal()->notEmpty())
            ->key('confirmacao', v::in($confirmacaoPermitida));

            $esquema->assert($convidadoDados);
        } catch (NestedValidationException $e) {

            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 1, max 45',
                'sobrenome' => 'Sobrenome inválido, min 1, max 45',
                'email' => 'Email inválido',
                'cpf' => 'Cpf inválido',
                'telefone' => 'Telefone inválido',
                'categoria' => 'Categoria inválida',
                'confirmacao' => 'Fora do escopo: confirmado, não confirmado e cancelado'
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
        $this->validarToken();
        echo json_encode($this->convidadoService->listarConvidados());
        exit;
    }

    public function criarConvidado()
    {
        try {

            $this->validarToken();
            $convidadoDados = json_decode(file_get_contents("php://input"), true);

            $this->validarDados($convidadoDados);

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
            $this->validarToken();
            $convidadoDados = json_decode(file_get_contents("php://input"), true);
            $this->validarDados($convidadoDados);
            $emailConvidado = $_GET['email_convidado'];

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

    public function deletarConvidado () {
        try{
        $this->validarToken();
        $emailConvidado = $_GET['email_convidado'];

        echo json_encode($this->convidadoService->deletarConvidado($emailConvidado));
        exit;
        }catch(Exception $e){
             http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
            exit;
        }
    }
}
