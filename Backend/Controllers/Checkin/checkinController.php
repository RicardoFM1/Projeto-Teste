<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Checkin/checkinService.php";

class CheckinController
{

    protected $checkinService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->checkinService = new CheckinService();
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



    public function validarDados($checkinDados)
    {

        $esquema = v::key('convidado_idconvidado', v::intVal()->notEmpty());

        try {
            $esquema->assert($checkinDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'convidado_idconvidado' => 'Convidado inválido'
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
    public function listarCheckins()
    {
        $this->validarToken();
        // Aqui só valida token para ver se está autenticado.
        http_response_code(200);
        echo json_encode($this->checkinService->listarCheckins());
        exit;
    }

    public function criarCheckin()
    {
        try {

            $checkinDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $tokenJWT = $this->validarToken();
            $this->validarDados($checkinDados);
            http_response_code(201);
            echo json_encode($this->checkinService->criarCheckin($checkinDados, $tokenJWT));
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



    public function atualizarCheckin()
    {
        try {

            $checkinDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idCheckin = $_GET['id_checkin'] ?? null;
            $tokenJWT = $this->validarToken();

            $this->validarDados($checkinDados);
            http_response_code(200);
            echo json_encode($this->checkinService->atualizarCheckin($checkinDados, $idCheckin, $tokenJWT));
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

    public function deletarCheckin()
    {
        try {

            $idCheckin = $_GET['id_checkin'] ?? null;
            $tokenJWT = $this->validarToken();


            http_response_code(200);
            echo json_encode($this->checkinService->deletarCheckin($idCheckin, $tokenJWT));
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
