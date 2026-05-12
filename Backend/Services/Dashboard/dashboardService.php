<?php
require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";

class DashboardService {
    public function listarDashboard () {
        $convidados = new ConvidadoService()->listarConvidados();

        $convidadosConfirmados = null;
        $convidadoNaoConfirmados = null;
        $convidadosCancelados = null;

        foreach($convidados['dados'] as $convidado ){
            if($convidado['confirmacao'] === 'confirmado'){
                $convidadosConfirmados++;
            }
            else if($convidado['confirmacao'] === 'não confirmado'){
                $convidadoNaoConfirmados++;
            }
            else{
                $convidadosCancelados++;
            }
        }

        return [
            'sucesso' => true,
            'dados' => [
                'convidados' => [
                    'listagem' => $convidados ?? null, 
                    'convidados_confirmados' => $convidadosConfirmados ?? 0,
                    'convidados_nao_confirmados' => $convidadoNaoConfirmados ?? 0,
                    'convidados_cancelados' => $convidadosCancelados ?? 0,
                    'total' => count($convidados['dados'])
                ]
            ]
        ];
    }
}