<?php
require_once __DIR__ . "/../../Middleware/middleware.php";

class RetrieveService {

    public function listarRetrieve () {
        $jwt = Middleware::validarMiddleware();
        return [
            'sucesso' => true,
            'dados' => $jwt
        ];
    }
}