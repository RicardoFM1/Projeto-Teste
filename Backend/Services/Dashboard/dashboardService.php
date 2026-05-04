<?php
require_once __DIR__ . "/../Usuario/usuarioService.php";
require_once __DIR__ . "/../Convidado/convidadoService.php";
require_once __DIR__ . "/../Acompanhante/acompanhanteService.php";
require_once __DIR__ . "/../Checkin/checkinService.php";
require_once __DIR__ . "/../Mesa/mesaService.php";




class DashboardService
{
    private $usuarioService;
    private $convidadoService;
    private $acompanhanteService;
    private $checkinService;
    private $mesaService;

    public function __construct()
    {
        $this->usuarioService = new UsuarioService();
        $this->convidadoService = new ConvidadoService();
        $this->acompanhanteService = new AcompanhanteService();
        $this->checkinService = new checkinService();
        $this->mesaService = new MesaService();
    }

    public function listarDashboard()
    {
       
        $convidados = $this->convidadoService->listarConvidados();

        $convidadosConfirmados = 0;
        $convidadosNaoConfirmados = 0;
        $convidadosCancelados = 0;
        

       

       

        foreach ($convidados['dados'] as $convidado) {
         
            if ($convidado['confirmacao'] === "confirmado") {
                $convidadosConfirmados++;

            } elseif ($convidado['confirmacao'] === "não confirmado") {
                $convidadosNaoConfirmados++;
            } elseif ($convidado['confirmacao'] === "cancelado") {
                $convidadosCancelados++;
            }
        }

    
        return [
            'sucesso' => true,
            'dados' => [
                'convidados' => [
                    'listagem' => $convidados['dados'] ?? [],
                    'convidados_confirmados' => $convidadosConfirmados,
                    'convidados_nao_confirmados' => $convidadosNaoConfirmados,
                    'convidados_cancelados' => $convidadosCancelados,
                    'total_convidados' => count($convidados['dados'] ?? [])
                ]
                
            ]
        ];
    }
}