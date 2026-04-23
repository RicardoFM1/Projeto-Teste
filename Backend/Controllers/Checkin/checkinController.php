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



    public function listarCheckins()
    {
        $this->validarToken();
        echo json_encode($this->checkinService->listarCheckins());
        exit;
    }

    public function criarCheckin()
    {
        try {

           $tokenJWT = $this->validarToken();
            $checkinDados = json_decode(file_get_contents("php://input"), true);

           

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
            $checkinDados = json_decode(file_get_contents("php://input"), true);
           
            $idCheckin = $_GET['id_checkin'];

            echo json_encode($this->checkinService->atualizarCheckin($checkinDados, $idCheckin, $tokenJWT));
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

    public function deletarCheckin () {
        try{
        $tokenJWT = $this->validarToken();
        $idCheckin = $_GET['id_checkin'];

        echo json_encode($this->checkinService->deletarCheckin($idCheckin, $tokenJWT));
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
