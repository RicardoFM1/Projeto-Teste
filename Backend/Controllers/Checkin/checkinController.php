<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Checkin/checkinService.php";
require_once __DIR__ . "/../../Middleware/authMiddleware.php";

class CheckinController
{
    protected $checkinService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->checkinService = new CheckinService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }




    public function listarCheckins()
    {
        Auth::validarMiddleware();
        http_response_code(200);

        echo json_encode($this->checkinService->listarCheckins());
        exit;
    }

    public function criarCheckin()
    {
        try {

            $tokenJWT = Auth::validarMiddleware();
            $checkinDados = json_decode(file_get_contents("php://input"), true);


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



   
}
