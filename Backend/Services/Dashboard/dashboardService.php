<?php


class DashboardService
{
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


    public function listarDashboard()
    {

        $resUsuarios = $this->usuarioService->listarUsuarios();
        $resConvidados = $this->convidadoService->listarConvidados();
        $resAcompanhantes = $this->acompanhanteService->listarAcompanhantes();
        $resMesas = $this->mesaService->listarMesas();
        $resCheckins = $this->checkinService->listarCheckins();


        $usuariosAdmin = 0;
        $convidadosConfirmados = 0;
        $convidadosNaoConfirmados = 0;
        $convidadosCancelados = 0;
        $acompanhantesMaioresDeIdade = 0;
        $acompanhantesMenoresDeIdade = 0;
        $mesasComRestricao = 0;



        foreach ($resUsuarios['dados'] as $usuario) {

            if ($usuario['cargo'] === 'admin') {
                $usuariosAdmin++;
            }
        }




        foreach ($resConvidados['dados'] as $convidado) {
            if ($convidado['confirmacao'] === 'confirmado') {
                $convidadosConfirmados++;
            } elseif ($convidado['confirmacao'] === 'não confirmado') {
                $convidadosNaoConfirmados++;
            } elseif ($convidado['confirmacao'] === 'cancelado') {
                $convidadosCancelados++;
            }
        }








        foreach ($resAcompanhantes['dados'] as $acompanhante) {
            if ($acompanhante['idade'] >= 18) {
                $acompanhantesMaioresDeIdade++;
            }

            if ($acompanhante['idade'] < 18) {
                $acompanhantesMenoresDeIdade++;
            }
        }


        foreach ($resMesas['dados'] as $mesa) {
            if (!empty($mesa['restricao'])) {
                $mesasComRestricao++;
            }
        }




        return [
            'sucesso' => true,
            'dados' => [
                'usuarios' => [
                    'listagem' => $resUsuarios['dados'] ?? 'Nenhum usuário',
                    'total_usuarios' => count($resUsuarios['dados']),
                    'admin' => $usuariosAdmin
                ],
                'convidados' => [
                    'listagem' => $resConvidados['dados'] ?? 'Nenhum convidado',
                    'total_convidados' => count($resConvidados['dados']),
                    'confirmados' => $convidadosConfirmados,
                    'não confirmados' => $convidadosNaoConfirmados,
                    'cancelados' => $convidadosCancelados
                ],
                'acompanhantes' => [
                    'listagem' => $resAcompanhantes['dados']  ?? 'Nenhum acompanhante',
                    'total_acompanhantes' => count($resAcompanhantes['dados']),
                    'maiores_de_idade' => $acompanhantesMaioresDeIdade,
                    'menores_de_idade' => $acompanhantesMenoresDeIdade
                ],
                'mesas' => [
                    'listagem' => $resMesas['dados']  ?? 'Nenhuma mesa',
                    'total_mesas' => count($resMesas['dados']),
                    'mesas_com_restricao' => $mesasComRestricao
                    // Fazer com capacidade também, mesas lotadas e disponíveis
                ],
                'checkins' => [
                    'listagem' => $resCheckins['dados']  ?? 'Nenhum checkin',
                    'total_checkins' => count($resCheckins['dados'])
                ]
            ]
        ];
    }
}
