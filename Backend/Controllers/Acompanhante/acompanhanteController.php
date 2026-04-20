<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Acompanhante/acompanhanteService.php";

class AcompanhanteController
{

    private $acompanhanteService;
    private $chaveSecreta;

    public function __construct()
    {
        $this->acompanhanteService = new AcompanhanteService();
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

    public function validarDados($acompanhanteDados)
    {
       

        $esquema = v::key('nome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('sobrenome', v::stringVal()->notEmpty()->length(1, 45))
            ->key('cpf', v::cpf())
            ->key('idade', v::intVal()->notEmpty())
            ->key('convidado_id_convidado', v::intVal()->notEmpty());
           

        try {
            $esquema->assert($acompanhanteDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, min 4, max 45',
                'sobrenome' => 'Nome inválido, min 4, max 45',
                'cpf' => 'Cpf inválido',
                'idade' => 'Idade inválida',
                'convidado_id_convidado' => 'Referência do convidado inválido'
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




    public function listarAcompanhantes()
    {
        $this->validarToken();

        http_response_code(200);
        echo json_encode($this->acompanhanteService->listarAcompanhante());
        exit;
    }

    public function criarAcompanhante()
    {
        try {
            $this->validarToken();


            $acompanhanteDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($acompanhanteDados);
            http_response_code(201);
            echo json_encode($this->acompanhanteService->criarAcompanhante($acompanhanteDados));
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



    public function atualizarAcompanhante()
    {
        try {

            $this->validarToken();

            $acompanhanteDados = json_decode(file_get_contents('php://input'), true) ?? null;
            $this->validarDados($acompanhanteDados);
            $idAcompanhante = $_GET['id_acompanhante'];

            http_response_code(200);
            echo json_encode($this->acompanhanteService->atualizarAcompanhante($acompanhanteDados, $idAcompanhante));
        } catch (Exception $e) {
            http_response_code($e->getCode());
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

            $this->validarToken();
            $idAcompanhante = $_GET['id_acompanhante'];

            http_response_code(200);
            echo json_encode($this->acompanhanteService->deletarAcompanhante($idAcompanhante));
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
