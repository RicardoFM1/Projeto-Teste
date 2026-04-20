<?php


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
        $usuarios = $this->usuarioService->listarUsuarios();
        $convidados = $this->convidadoService->listarConvidados();
        $acompanhantes = $this->acompanhanteService->listarAcompanhante();
        $checkins = $this->checkinService->listarCheckins();
        $mesas = $this->mesaService->listarMesas();


        $usuariosAdmin = 0;
        $convidadosConfirmados = 0;
        $convidadosNaoConfirmados = 0;
        $convidadosCancelados = 0;
        $acompanhantesMaioresIdade = 0;
        $acompanhantesMenoresIdade = 0;
        $mesasComRestricao = 0;
        $mesasLotadas = 0;
        $mesasDisponiveis = 0;


        foreach ($usuarios['dados'] as $usuario) {
            if ($usuario['cargo'] === 'admin') {
                $usuariosAdmin++;
            }
        }


       $contagemPorMesa = [];

        foreach ($convidados['dados'] as $convidado) {
            if ($convidado['confirmacao'] === "confirmado") {
                $convidadosConfirmados++;
            }
            if ($convidado['confirmacao'] === "não confirmado") {
                $convidadosNaoConfirmados++;
            }
            if ($convidado['confirmacao'] === "cancelado") {
                $convidadosCancelados++;
            }

           $idMesa = $convidado['mesa_id_mesa'];

           if($convidado['confirmacao'] === 'confirmado' && !empty($idMesa)){
                if(!isset($contagemPorMesa[$idMesa])){
                    $contagemPorMesa[$idMesa] = 0;
                }
                $contagemPorMesa[$idMesa]++;
           }

            
        }

        
        foreach ($acompanhantes['dados'] as $acompanhante) {
            if ($acompanhante['idade'] >= 18) {
                $acompanhantesMaioresIdade++;
            }

            if ($acompanhante['idade'] < 18) {
                $acompanhantesMenoresIdade++;
            }
        }

        foreach ($mesas['dados'] as $mesa) {
            if (!empty($mesa['restricao'])) {
                $mesasComRestricao++;
            }

           $idMesa = $mesa['id_mesa'];
           $capacidade = $mesa['capacidade'];
           $totalAlocado = $contagemPorMesa[$idMesa] ?? 0;

           if($totalAlocado >= $capacidade && $capacidade > 0){
                $mesasLotadas++;
           }
           else{
            $mesasDisponiveis++;
           }
        }

        return [
            'sucesso' => true,
            'dados' => [
                'usuarios' => [
                    'listagem' => $usuarios ?? 'Nenhum usuário',
                    'usuarios_admin' => $usuariosAdmin,
                    'total_usuarios' => count($usuarios['dados'])
                ],
                'convidados' => [
                    'listagem' => $convidados ?? 'Nenhum convidado',
                    'convidados_confirmados' => $convidadosConfirmados,
                    'convidados_nao_confirmados' => $convidadosNaoConfirmados,
                    'convidados_cancelados' => $convidadosCancelados,
                    'total_convidados' => count($convidados['dados'])
                ],
                'acompanhantes' => [
                    'listagem' => $acompanhantes ?? 'Nenhum acompanhante',
                    'acompanhantes_maiores' => $acompanhantesMaioresIdade,
                    'acompanhantes_menores' => $acompanhantesMenoresIdade,
                    'total_acompanhantes' => count($acompanhantes['dados'])
                ],
                'checkins' => [
                    'listagem' => $checkins ?? 'Nenhum checkin',
                    'total_checkins' => count($checkins['dados'])
                ],
                'mesas' => [
                    'listagem' => $mesas,
                    'mesas_com_restricao' => $mesasComRestricao,
                    'mesas_lotadas' => $mesasLotadas,
                    'mesas_disponiveis' => $mesasDisponiveis,
                    'total_mesas' => count($mesas['dados'])
                ]
            ]
        ];
    }
}
