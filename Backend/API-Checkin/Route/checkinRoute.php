<?php

use Dotenv\Dotenv;
require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../Controller/checkinController.php";
require_once __DIR__ . "/../Middleware/checkinMiddleware.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$dotenv = Dotenv::createImmutable(__DIR__ . "/../../");
$dotenv->load();

$caminhoRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodoRequisicao = $_SERVER['REQUEST_METHOD'];




if($metodoRequisicao === "OPTIONS"){
    http_response_code(200);
    exit;
}


if($caminhoRequisicao === "/checkin"){
    $checkinController = new CheckinController();
    CheckinMiddleware::validarMiddlewareCheckin();

    if($metodoRequisicao === "GET"){
        $checkinController->listarCheckins();
    }
    
    if($metodoRequisicao === "POST"){
        $checkinController->criarCheckin();
    }

    if($metodoRequisicao === "PUT"){
        $checkinController->atualizarCheckin();
    }

    if($metodoRequisicao === "DELETE"){
        $checkinController->deletarCheckin();
    }
}

