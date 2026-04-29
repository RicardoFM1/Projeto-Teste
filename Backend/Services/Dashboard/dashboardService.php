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
        // 1. Busca de todos os dados necessários
        $usuarios = $this->usuarioService->listarUsuarios();
        $convidados = $this->convidadoService->listarConvidados();
        $acompanhantes = $this->acompanhanteService->listarAcompanhantes();
        $checkins = $this->checkinService->listarCheckins();
        $mesas = $this->mesaService->listarMesas();

        // 2. Inicialização dos contadores
        $usuariosAdmin = 0;
        $convidadosConfirmados = 0;
        $convidadosNaoConfirmados = 0;
        $convidadosCancelados = 0;
        $acompanhantesMaioresIdade = 0;
        $acompanhantesMenoresIdade = 0;
        $mesasComRestricao = 0;
        $mesasLotadas = 0;
        $mesasDisponiveis = 0;

        // --- LÓGICA DE USUÁRIOS ---
        foreach ($usuarios['dados'] as $usuario) {
            if ($usuario['cargo'] === 'admin') {
                $usuariosAdmin++;
            }
        }

        // --- LÓGICA DE CONVIDADOS E MAPEAMENTO DE OCUPAÇÃO ---
        $contagemPorMesa = []; // Array associativo [id_mesa => total_pessoas]

        foreach ($convidados['dados'] as $convidado) {
            // Contagem de status de confirmação
            if ($convidado['confirmacao'] === "confirmado") {
                $convidadosConfirmados++;

                // Agrupando ocupação por mesa (Apenas confirmados)
                $idMesa = $convidado['mesa_idmesa'];
                if (!empty($idMesa)) {
                    if (!isset($contagemPorMesa[$idMesa])) {
                        $contagemPorMesa[$idMesa] = 0;
                    }
                    $contagemPorMesa[$idMesa]++;
                }
            } elseif ($convidado['confirmacao'] === "não confirmado") {
                $convidadosNaoConfirmados++;
            } elseif ($convidado['confirmacao'] === "cancelado") {
                $convidadosCancelados++;
            }
        }

        // --- LÓGICA DE ACOMPANHANTES ---
        foreach ($acompanhantes['dados'] as $acompanhante) {
            if ($acompanhante['idade'] >= 18) {
                $acompanhantesMaioresIdade++;
            } else {
                $acompanhantesMenoresIdade++;
            }
        }

        // --- LÓGICA DE MESAS (Lotação e Restrição) ---
        foreach ($mesas['dados'] as $mesa) {
            // Conta mesas com restrição
            if (!empty($mesa['restricao'])) {
                $mesasComRestricao++;
            }

            $idMesa = $mesa['id_mesa'];
            $capacidade = (int) $mesa['capacidade'];
            $ocupacaoAtual = $contagemPorMesa[$idMesa] ?? 0;

            // Define se a mesa está lotada ou disponível
            if ($capacidade > 0 && $ocupacaoAtual >= $capacidade) {
                $mesasLotadas++;
            } else {
                $mesasDisponiveis++;
            }
        }

        // 3. Retorno estruturado para o Frontend/Controller
        return [
            'sucesso' => true,
            'dados' => [
                'usuarios' => [
                    'listagem' => $usuarios['dados'] ?? [],
                    'usuarios_admin' => $usuariosAdmin,
                    'total_usuarios' => count($usuarios['dados'] ?? [])
                ],
                'convidados' => [
                    'listagem' => $convidados['dados'] ?? [],
                    'convidados_confirmados' => $convidadosConfirmados,
                    'convidados_nao_confirmados' => $convidadosNaoConfirmados,
                    'convidados_cancelados' => $convidadosCancelados,
                    'total_convidados' => count($convidados['dados'] ?? [])
                ],
                'acompanhantes' => [
                    'listagem' => $acompanhantes['dados'] ?? [],
                    'acompanhantes_maiores' => $acompanhantesMaioresIdade,
                    'acompanhantes_menores' => $acompanhantesMenoresIdade,
                    'total_acompanhantes' => count($acompanhantes['dados'] ?? [])
                ],
                'checkins' => [
                    'listagem' => $checkins['dados'] ?? [],
                    'total_checkins' => count($checkins['dados'] ?? [])
                ],
                'mesas' => [
                    'listagem' => $mesas['dados'] ?? [],
                    'mesas_com_restricao' => $mesasComRestricao,
                    'mesas_lotadas' => $mesasLotadas,
                    'mesas_disponiveis' => $mesasDisponiveis,
                    'total_mesas' => count($mesas['dados'] ?? [])
                ]
            ]
        ];
    }
}