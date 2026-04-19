<?php

use Dotenv\Dotenv;
require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Controllers/Usuario/usuarioController.php";
require_once __DIR__ . "/../Controllers/Convidado/convidadoController.php";
require_once __DIR__ . "/../Controllers/Acompanhante/acompanhanteController.php";
require_once __DIR__ . "/../Controllers/Mesa/mesaController.php";
require_once __DIR__ . "/../Controllers/Dashboard/dashboardController.php";
require_once __DIR__ . "/../Controllers/Checkin/checkinController.php";


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$caminhoRequisicao = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$metodoRequisicao = $_SERVER['REQUEST_METHOD'];




if($metodoRequisicao === "OPTIONS"){
    http_response_code(200);
    exit;
}

// Rotas Usuário:
if($caminhoRequisicao === "/usuario"){
    $usuarioController = new UsuarioController();
   

    if($metodoRequisicao === "GET"){
        $usuarioController->listarUsuarios();
    }
    
    if($metodoRequisicao === "POST"){
        $usuarioController->criarUsuario();
    }

    if($metodoRequisicao === "PUT"){
        $usuarioController->atualizarUsuario();
    }

    if($metodoRequisicao === "DELETE"){
        $usuarioController->deletarUsuario();
    }
}

if($caminhoRequisicao === "/usuario/login"){
    $usuarioController = new UsuarioController();

    if($metodoRequisicao === "POST"){
        $usuarioController->fazerLogin();
    }
}


// Rotas Convidado:

if($caminhoRequisicao === "/convidado"){
    $convidadoController = new ConvidadoController();

    if($metodoRequisicao === "GET"){
        $convidadoController->listarConvidados();
    }
    
    if($metodoRequisicao === "POST"){
        $convidadoController->criarConvidado();
    }

    if($metodoRequisicao === "PUT"){
        $convidadoController->atualizarConvidado();
    }

    if($metodoRequisicao === "DELETE"){
        $convidadoController->deletarConvidado();
    }
}


// Rotas Mesa:

if($caminhoRequisicao === "/mesa"){
    $mesaController = new MesaController();

    if($metodoRequisicao === "GET"){
        $mesaController->listarMesas();
    }
    
    if($metodoRequisicao === "POST"){
        $mesaController->criarMesa();
    }

    if($metodoRequisicao === "PUT"){
        $mesaController->atualizarMesa();
    }

    if($metodoRequisicao === "DELETE"){
        $mesaController->deletarMesa();
    }
}

// Rotas Checkin: 

if($caminhoRequisicao === "/checkin"){
    $checkinController = new CheckinController();

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

// Rotas Dashboard:

if($caminhoRequisicao === "/dashboard"){
    $dashboardController = new DashboardController();

    if($metodoRequisicao === "GET"){
        $dashboardController->listarDashboard();
    }
    
    
}
