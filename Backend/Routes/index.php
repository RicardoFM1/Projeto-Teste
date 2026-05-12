<?php

use Dotenv\Dotenv;

require_once __DIR__  . "/../vendor/autoload.php";
require_once __DIR__ . "/../Controllers/Usuario/usuarioController.php";
require_once __DIR__ . "/../Controllers/Mesa/mesaController.php";
require_once __DIR__ . "/../Controllers/Convidado/convidadoController.php";
require_once __DIR__ . "/../Controllers/Checkin/checkinController.php";
require_once __DIR__ . "/../Controllers/Acompanhante/acompanhanteController.php";
require_once __DIR__ . "/../Controllers/Dashboard/dashboardController.php";
require_once __DIR__ . "/../Controllers/Retrieve/retrieveController.php";



$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: OPTIONS, GET, POST, PUT, DELETE');
header('Access-Control-Allow-Credentials: true');


$rotaRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodoRequisicao = $_SERVER['REQUEST_METHOD'];


if ($metodoRequisicao === 'OPTIONS') {
    http_response_code(200);
}


if ($rotaRequisicao === '/usuario') {
    $usuarioController = new UsuarioController();

    if ($metodoRequisicao === 'GET') {
        $usuarioController->listarUsuarios();
    }
    if ($metodoRequisicao === 'POST') {
        $usuarioController->criarUsuario();
    }
    if ($metodoRequisicao === 'PUT') {
        $usuarioController->atualizarUsuario();
    }
    if ($metodoRequisicao === 'DELETE') {
        $usuarioController->deletarUsuario();
    }
}

if ($rotaRequisicao === '/usuario/login') {
    $usuarioController = new UsuarioController();

    if ($metodoRequisicao === 'POST') {
        $usuarioController->fazerLogin();
    }
}

if ($rotaRequisicao === '/mesa') {
    $mesaController = new MesaController();

    if ($metodoRequisicao === 'GET') {
        $mesaController->listarMesas();
    }
    if ($metodoRequisicao === 'POST') {
        $mesaController->criarMesa();
    }
    if ($metodoRequisicao === 'PUT') {
        $mesaController->atualizarMesa();
    }
    if ($metodoRequisicao === 'DELETE') {
        $mesaController->deletarMesa();
    }
}


if ($rotaRequisicao === '/convidado') {
    $convidadoController = new convidadoController();

    if ($metodoRequisicao === 'GET') {
        $convidadoController->listarConvidados();
    }
    if ($metodoRequisicao === 'POST') {
        $convidadoController->criarConvidado();
    }
    if ($metodoRequisicao === 'PUT') {
        $convidadoController->atualizarConvidado();
    }
    if ($metodoRequisicao === 'DELETE') {
        $convidadoController->deletarConvidado();
    }
}

if ($rotaRequisicao === '/checkin') {
    $checkinController = new CheckinController();

    if ($metodoRequisicao === 'GET') {
        $checkinController->listarCheckins();
    }
    if ($metodoRequisicao === 'POST') {
        $checkinController->criarCheckin();
    }
}


if ($rotaRequisicao === '/convidado') {
    $acompanhanteController = new AcompanhanteController();

    if ($metodoRequisicao === 'GET') {
        $acompanhanteController->listarAcompanhantes();
    }
    if ($metodoRequisicao === 'POST') {
        $acompanhanteController->criarAcompanhante();
    }
    if ($metodoRequisicao === 'PUT') {
        $acompanhanteController->atualizarAcompanhante();
    }
    if ($metodoRequisicao === 'DELETE') {
        $acompanhanteController->deletarAcompanhante();
    }
}

if ($rotaRequisicao === '/dashboard') {
    $dashboardController = new DashboardController();

    if ($metodoRequisicao === 'GET') {
        $dashboardController->listarDashboard();
    }
}

if ($rotaRequisicao === '/retrieve') {
    $retrieveController = new RetrieveController();

    if ($metodoRequisicao === 'GET') {
        $retrieveController->listarRetrieve();
    }
}
