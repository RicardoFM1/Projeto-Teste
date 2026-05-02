<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

require_once __DIR__ . "/../../Services/Mesa/mesaService.php";
require_once __DIR__ . "/../../Middleware/authMiddleware.php";

class MesaController
{
    protected $mesaService;
    protected $chaveSecreta;

    public function __construct()
    {
        $this->mesaService = new MesaService();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }



    public function listarMesas()
    {
        Auth::validarMiddleware();
        http_response_code(200);

        echo json_encode($this->mesaService->listarMesas());
        exit;
    }

    public function criarMesa()
    {
        try {

            Auth::validarMiddleware();

            $mesaDados = json_decode(file_get_contents("php://input"), true);

            http_response_code(201);


            echo json_encode($this->mesaService->criarMesas($mesaDados));
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
            Auth::validarMiddleware();

            $mesaDados = json_decode(file_get_contents("php://input"), true);

            $idMesa = $_GET['id_mesa'];

            http_response_code(200);

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

    public function deletarMesa()
    {
        try {
            Auth::validarMiddleware();

            $idMesa = $_GET['id_mesa'];

            http_response_code(200);

            echo json_encode($this->mesaService->deletarMesa($idMesa));
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
