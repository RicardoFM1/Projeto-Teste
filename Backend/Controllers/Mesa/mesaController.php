<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;


require_once __DIR__ . "/../../Services/Mesa/mesaService.php";

class MesaController
{

    protected $mesaService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->mesaService = new MesaService();
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



    public function validarDados($mesaDados)
    {
       

        $esquema = v::key('capacidade', v::intVal()->notEmpty())
            ->key('restricao', v::intVal()->notEmpty());
           

        try {
            $esquema->assert($mesaDados);
        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'capacidade' => 'Capacidade inválida',
                'restricao' => 'Restrição inválida'
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
    public function listarMesas()
    {
        $this->validarToken();
        // Aqui só valida token para ver se está autenticado.
        http_response_code(200);
        echo json_encode($this->mesaService->listarMesas());
        exit;
    }

    public function criarMesa()
    {
        try {

            $mesaDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $this->validarToken();
            $this->validarDados($mesaDados);
            http_response_code(201);
            echo json_encode($this->mesaService->criarMesa($mesaDados));
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



    public function atualizarMesa()
    {
        try {

            $mesaDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idMesa = $_GET['id_mesa'] ?? null;
            $this->validarToken();

            $this->validarDados($mesaDados);
            http_response_code(200);
            echo json_encode($this->mesaService->atualizarMesa($mesaDados, $idMesa));
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

    public function deletarMesa()
    {
        try {

            $idMesa = $_GET['id_mesa'] ?? null;
            $this->validarToken();


            http_response_code(200);
            echo json_encode($this->mesaService->deletarMesa($idMesa));
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
