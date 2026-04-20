<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Checkin/checkinService.php";

class CheckinController
{

    private $checkinService;
    private $chaveSecreta;

    public function __construct()
    {
        $this->checkinService = new checkinService();
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

    public function validarDados($checkinDados)
    {


        $esquema = v::key('usuario_idusuario', v::intVal()->notEmpty())
            ->key('convidado_idconvidado', v::intVal()->notEmpty());


        try {
            $esquema->assert($checkinDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'usuario_idusuario' => 'Referência de usuário inválida',
                'convidado_idconvidado' => 'Referência de convidado inválida'
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




    public function listarCheckins()
    {
        $this->validarToken();

        http_response_code(200);
        echo json_encode($this->checkinService->listarCheckins());
        exit;
    }

    public function criarCheckin()
    {
        try {
            $tokenJWT = $this->validarToken();


            $checkinDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($checkinDados);
            http_response_code(201);
            echo json_encode($this->checkinService->criarCheckin($checkinDados, $tokenJWT));
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



    public function atualizarCheckin()
    {
        try {

            $tokenJWT = $this->validarToken();

            $checkinDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($checkinDados);
            $idCheckin = $_GET['id_checkin'];

            http_response_code(200);
            echo json_encode($this->checkinService->atualizarCheckin($checkinDados, $idCheckin, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
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

            $tokenJWT = $this->validarToken();
            $idCheckin = $_GET['id_checkin'];

            http_response_code(200);
            echo json_encode($this->checkinService->deletarCheckin($idCheckin, $tokenJWT));
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
