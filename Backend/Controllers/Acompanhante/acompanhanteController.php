<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Acompanhante/acompanhanteService.php";

class AcompanhanteController
{

    protected $acompanhanteService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->acompanhanteService = new AcompanhanteService();
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

        try {

            $partesToken = explode(' ', $tokenJWT);

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



    public function validarDados($acompanhanteDados)
    {

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('cpf', v::cpf())
            ->key('idade', v::intVal());

        try {
            $esquema->assert($acompanhanteDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 1, max 50',
                'sobrenome' => 'sobrenome inválido, min 1, max 50',
                'cpf' => 'Cpf inválido',
                'idade' => 'Idade inválida'
                
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
    public function listarAcompanhantes()
    {
        $this->validarToken();
        // Aqui só valida token para ver se está autenticado.
        http_response_code(200);
        echo json_encode($this->acompanhanteService->listarAcompanhantes());
        exit;
    }

    public function criarAcompanhante()
    {
        try {

            $acompanhanteDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $this->validarToken();
            $this->validarDados($acompanhanteDados);
            http_response_code(201);
            echo json_encode($this->acompanhanteService->criarAcompanhante($acompanhanteDados));
            exit;
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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

            $acompanhanteDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idAcompanhante = $_GET['id_acompanhante'] ?? null;
            $this->validarToken();

            $this->validarDados($acompanhanteDados);
            http_response_code(200);
            echo json_encode($this->acompanhanteService->atualizarAcompanhante($acompanhanteDados, $idAcompanhante));
            exit;
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
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

            $idAcompanhante = $_GET['id_acompanhante'] ?? null;
            $this->validarToken();


            http_response_code(200);
            echo json_encode($this->acompanhanteService->deletarAcompanhante($idAcompanhante));
            exit;
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
