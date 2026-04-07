<?php

require_once __DIR__ . "/../Service/usuarioService.php";

class usuarioController
{

    protected $usuarioService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
    }

    public function listarUsuarios()
    {
        http_response_code(200);
        echo json_encode($this->usuarioService->listarUsuarios());
    }

    public function criarUsuario()
    {
        try {

            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;
            http_response_code(201);
            echo json_encode($this->usuarioService->criarUsuario($usuarioDados));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function fazerLogin()
    {
        try{

            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;
            
            echo json_encode($this->usuarioService->fazerLogin($usuarioDados));
            }catch(Exception $e){
                http_response_code($e->getCode());
                echo json_encode([
                    'sucesso' => false,
                    'message' => $e->getMessage()
                ]);
            } 
    }

    public function atualizarUsuario()
    {
        try {

            $usuarioDados = json_decode(file_get_contents("php://input"), true) ?? null;
            $idUsuario = $_GET['id_usuario'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            http_response_code(200);
            echo json_encode($this->usuarioService->atualizarUsuario($usuarioDados, $idUsuario, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function deletarUsuario()
    {
        try {

            $idUsuario = $_GET['id_usuario'];
            $tokenJWT = null;

            if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['HTTP_AUTHORIZATION']);
            }
            if (isset($_SERVER['AUTHORIZATION'])) {
                $tokenJWT = trim($_SERVER['AUTHORIZATION']);
            }

            http_response_code(200);
            echo json_encode($this->usuarioService->deletarUsuario($idUsuario, $tokenJWT));
        } catch (Exception $e) {
            http_response_code($e->getCode());
            echo json_encode([
                'sucesso' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
