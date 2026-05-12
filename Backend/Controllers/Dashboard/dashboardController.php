<?php
require_once __DIR__ . "/../../Services/Dashboard/dashboardService.php";

class DashboardController
{
    protected $dashboardService;

    public function __construct()
    {
      $this->dashboardService = new DashboardService();
    }

    public function listarDashboard()
    {
        http_response_code(200);
        echo json_encode($this->dashboardService->listarDashboard());
        exit;
    }
}
