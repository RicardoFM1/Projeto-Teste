<?php


class DashboardService {
    protected $usuarioService;
    protected $convidadoService;
    protected $acompanhanteService;
    protected $mesaService;
    protected $checkinService;

    public function __construct()
    {
       $this->usuarioService = new UsuarioService();
       $this->convidadoService = new ConvidadoService();
       $this->acompanhanteService = new AcompanhanteService();
       $this->mesaService = new MesaService();
       $this->checkinService = new CheckinService();
    }


    public function listarDashboard () {
        
        $usuarios = $this->usuarioService->listarUsuarios();
        $convidados = $this->convidadoService->listarConvidados();
        $acompanhantes = $this->acompanhanteService->listarAcompanhantes();
        $mesas = $this->mesaService->listarMesas();
        $checkins = $this->checkinService->listarCheckins();

        
        $usuariosAdmin = null;

        foreach ($usuarios as $usuario){
            if($usuario['dados']['cargo'] === 'admin'){
                $usuariosAdmin = $usuariosAdmin + 1;
            }
        }

        $convidadosConfirmados = null;
        $convidadosNaoConfirmados = null;
        $convidadosCancelados = null;

        foreach ($convidados as $convidado){
            if($convidado['dados']['confirmacao'] === 'confirmado'){
                $convidadosConfirmados = $convidadosConfirmados + 1;
            }

            if($convidado['dados']['confirmacao'] === 'não confirmado'){
                $convidadosNaoConfirmados = $convidadosNaoConfirmados + 1;
            }

            if($convidado['dados']['confirmacao'] === 'cancelado'){
                $convidadosCancelados = $convidadosCancelados + 1;
            }
        }

        $acompanhantesMaioresDeIdade = null;
        $acompanhantesMenoresDeIdade = null;


        foreach($acompanhantes as $acompanhante){
            if($acompanhante['dados']['idade'] >= 18){
                $acompanhantesMaioresDeIdade = $acompanhantesMaioresDeIdade + 1;
            }

            if($acompanhante['dados']['idade'] < 18){
                $acompanhantesMenoresDeIdade = $acompanhantesMenoresDeIdade + 1;
            }
        }

        $mesasComRestricao = null;

        foreach ($mesas as $mesa){
            if(!empty($mesas['dados']['restricao'])){
                $mesasComRestricao = $mesasComRestricao + 1;
            }
        }


        return [
            'sucesso' => true,
            'dados' => [
                'usuarios' => [
                    $usuarios,
                    'total_usuarios' => count($usuarios),
                    'admin' => $usuariosAdmin
                ],
                'convidados' => [
                    $convidados,
                    'total_convidados' => count($convidados),
                    'confirmados' => $convidadosConfirmados, 
                    'não confirmados' => $convidadosNaoConfirmados,
                    'cancelados' => $convidadosCancelados
                ],
                'acompanhantes' => [
                    $acompanhantes,
                    'total_acompanhantes' => count($acompanhantes),
                    'maiores_de_idade' => $acompanhantesMaioresDeIdade,
                    'menores_de_idade' => $acompanhantesMenoresDeIdade
                ],
                'mesas' => [
                    $mesas,
                    'total_mesas' => count($mesas),
                    'mesas_com_restricao' => $mesasComRestricao
                    // Fazer com capacidade também, mesas lotadas e disponíveis
                ],
                'checkins' => [
                    $checkins,
                    'total_checkins' => count($checkins)
                ]
            ]
        ];
    }
}