<?php
require_once __DIR__ . "/../../Services/Dashboard/dashboardService.php";

class DashboardController
{
    protected $dashboardService;

    public function __construct()
    {
      $this->dashboardService = new DashboardService();
    }

     public function apenasAdmin()
    {
        $jwt = Middleware::validarMiddleware();

        if ($jwt->dados->cargo_usuario !== 'admin') {
            http_response_code(401);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Usuário sem permissão'
            ]);
            exit;
        }
    }

    public function listarDashboard()
    {
        $this->apenasAdmin();
        http_response_code(200);
        echo json_encode($this->dashboardService->listarDashboard());
        exit;
    }
}
