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


    
    public function listarMesas()
    {
        $this->validarToken();
        echo json_encode($this->mesaService->listarMesas());
        exit;
    }

    public function criarMesa()
    {
        try {

            $this->validarToken();
            $mesaDados = json_decode(file_get_contents("php://input"), true);

            echo json_encode($this->mesaService->criarMesa($mesaDados));
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

   

    public function atualizarMesa()
    {
        try {
            $this->validarToken();
            $mesaDados = json_decode(file_get_contents("php://input"), true);
           
            $idMesa = $_GET['id_mesa'];

            echo json_encode($this->mesaService->atualizarMesa($mesaDados, $idMesa));
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

    public function deletarMesa () {
        try{
        $this->validarToken();
        $idMesa = $_GET['id_mesa'];

        echo json_encode($this->mesaService->deletarMesa($idMesa));
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
