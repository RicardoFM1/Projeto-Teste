<?php
require_once __DIR__ . "/../../Services/Retrieve/retrieveService.php";

class RetrieveController
{

    protected $retrieveService;

    public function __construct()
    {
        $this->retrieveService = new RetrieveService();
    }

    public function retrieveUsuario()
    {
        try {
            echo json_encode($this->retrieveService->retrieveUsuario());
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
