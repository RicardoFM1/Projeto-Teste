<?php

use Dotenv\Dotenv;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Controllers/Usuario/usuarioController.php";
require_once __DIR__ . "/../Controllers/Mesa/mesaController.php";
require_once __DIR__ . "/../Controllers/Convidado/convidadoController.php";
require_once __DIR__ . "/../Controllers/Checkin/checkinController.php";
require_once __DIR__ . "/../Controllers/Acompanhante/acompanhanteController.php";




$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$rotaRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodoRequisicao = $_SERVER['REQUEST_METHOD'];

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');


if ($metodoRequisicao === "OPTIONS") {
    http_response_code(200);
    exit;
}


// Rotas Usuário: 

if ($rotaRequisicao === '/usuario') {
    $usuarioController = new UsuarioController();

    if ($metodoRequisicao === "GET") {
        $usuarioController->listarUsuarios();
    }

    if ($metodoRequisicao === "POST") {
        $usuarioController->criarUsuario();
    }
    if ($metodoRequisicao === "PUT") {
        $usuarioController->atualizarUsuario();
    }
    if ($metodoRequisicao === "DELETE") {
        $usuarioController->deletarUsuario();
    }
}


// Rota Login: 

if ($rotaRequisicao === '/usuario/login') {
    $usuarioController = new UsuarioController();


    if ($metodoRequisicao === "POST") {
        $usuarioController->fazerLogin();
    }
}



// Rotas Mesa: 

if ($rotaRequisicao === '/mesa') {
    $mesaController = new MesaController();

    if ($metodoRequisicao === "GET") {
        $mesaController->listarMesas();
    }

    if ($metodoRequisicao === "POST") {
        $mesaController->criarMesa();
    }
    if ($metodoRequisicao === "PUT") {
        $mesaController->atualizarMesa();
    }
    if ($metodoRequisicao === "DELETE") {
        $mesaController->deletarMesa();
    }
}

// Rotas Convidado: 

if ($rotaRequisicao === '/convidado') {
    $conviadoController = new ConvidadoController();

    if ($metodoRequisicao === "GET") {
        $convidadoController->listarConvidados();
    }

    if ($metodoRequisicao === "POST") {
        $convidadoController->criarConvidado();
    }
    if ($metodoRequisicao === "PUT") {
        $convidadoController->atualizarConvidado();
    }
    if ($metodoRequisicao === "DELETE") {
        $convidadoController->deletarConvidado();
    }
}

// Rotas Checkin: 

if ($rotaRequisicao === '/checkin') {
    $checkinController = new CheckinController();

    if ($metodoRequisicao === "GET") {
        $checkinController->listarCheckins();
    }

    if ($metodoRequisicao === "POST") {
        $checkinController->criarCheckin();
    }
    if ($metodoRequisicao === "PUT") {
        $checkinController->atualizarCheckin();
    }
    if ($metodoRequisicao === "DELETE") {
        $checkinController->deletarCheckin();
    }
}

// Rotas Acompanhante:

if ($rotaRequisicao === '/acompanhante') {
    $acompanhanteController = new AcompanhanteController();

    if ($metodoRequisicao === "GET") {
        $acompanhanteController->listarAcompanhantes();
    }

    if ($metodoRequisicao === "POST") {
        $acompanhanteController->criarAcompanhante();
    }
    if ($metodoRequisicao === "PUT") {
        $acompanhanteController->atualizarAcompanhante();
    }
    if ($metodoRequisicao === "DELETE") {
        $acompanhanteController->deletarAcompanhante();
    }
}
