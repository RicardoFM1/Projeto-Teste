<?php
require_once __DIR__ . "/../../Services/Retrieve/retrieveService.php";

class RetrieveController {
    protected $retrieveService;

    public function __construct()
    {
       $this->retrieveService = new RetrieveService();
    }

    public function listarRetrieve () {
        http_response_code(200);
        echo json_encode($this->retrieveService->listarRetrieve());
        exit;
    }
}