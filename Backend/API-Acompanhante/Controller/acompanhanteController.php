<?php

require_once __DIR__ . "/../Service/acompanhanteService.php";

class AcompanhanteController
{

    protected $acompanhanteService;

    public function __construct()
    {
        $this->acompanhanteService = new AcompanhanteService();
    }

    public function listarAcompanhantes()
    {
        http_response_code(200);
        echo json_encode($this->acompanhanteService->listarAcompanhantes());
    }

    public function criarAcompanhante()
    {
        try {

            $acompanhanteDados = json_decode(file_get_contents("php://input"), true) ?? null;
            http_response_code(201);
            echo json_encode($this->acompanhanteService->criarAcompanhante($acompanhanteDados));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
        }
    }

    

    public function atualizarAcompanhante()
    {
        try {

            $acompanhanteDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idAcompanhante = $_GET['id_acompanhante'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            http_response_code(200);
            echo json_encode($this->acompanhanteService->atualizarAcompanhante($acompanhanteDados, $idAcompanhante, $tokenJWT));
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

            $idAcompanhante = $_GET['id_acompanhante'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            http_response_code(200);
            echo json_encode($this->acompanhanteService->deletarAcompanhante($idAcompanhante, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'mensagem' => $e->getMessage()
            ]);
        }
    }
}
