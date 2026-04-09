<?php

require_once __DIR__ . "/../Service/checkinService.php";

class CheckinController
{

    protected $checkinService;

    public function __construct()
    {
        $this->checkinService = new CheckinService();
    }

    public function listarCheckins()
    {
        http_response_code(200);
        echo json_encode($this->checkinService->listarCheckins());
    }

    public function criarCheckin()
    {
        try {
           $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            $checkinDados = json_decode(file_get_contents("php://input"), true) ?? null;
            http_response_code(201);
            echo json_encode($this->checkinService->criarCheckin($checkinDados, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
        }
    }

    

    public function atualizarCheckin()
    {
        try {

            $checkinDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idCheckin = $_GET['id_checkin'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

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

            $idCheckin = $_GET['id_checkin'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            http_response_code(200);
            echo json_encode($this->checkinService->deletarCheckin($idCheckin, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
        }
    }
}
