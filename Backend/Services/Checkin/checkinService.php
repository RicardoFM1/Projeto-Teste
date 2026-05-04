<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";
require_once __DIR__ . "/../../Services/Convidado/convidadoService.php";

date_default_timezone_set('America/Sao_Paulo');

class CheckinService
{
    protected $db;

    public function __construct()
    {
        $this->db = dbConnection();
    }

    public function buscarCheckinPorId($idCheckin)
    {
        if (empty($idCheckin)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM checkin WHERE id_checkin = :id_checkin');

        $buscar->execute([
            ':id_checkin' => $idCheckin
        ]);

        $checkin = $buscar->fetch();

        if (empty($checkin)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Checkin não encontrado',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $checkin
        ];
    }

    public function listarCheckins()
    {
        $query = $this->db->query("SELECT * FROM checkin");

        $checkins = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $checkins,
            'total' => count($checkins)
        ];
    }


    public function criarCheckin($checkinDados, $tokenJWT)
    {
        try {

            $buscarConvidadoPorId = $this->db->prepare("SELECT confirmacao FROM convidado WHERE id_convidado = :id_convidado");
            $buscarConvidadoPorId->execute([
                ':id_convidado' => $checkinDados['convidado_idconvidado']
            ]);

            $convidado = $buscarConvidadoPorId->fetch();

            if(empty($convidado)){
                throw new Exception('Convidado não encontrado', 404);
            }

            if($convidado['confirmacao'] === 'cancelado'){
                throw new Exception('Convidado cancelado', 400);
            }

            if($convidado['confirmacao'] === 'confirmado'){
                throw new Exception('Convidado já confirmado', 400);
            }


            $criar = $this->db->prepare('INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
            VALUES (:usuario_idusuario, :convidado_idconvidado, :data_e_hora)');

            $dataehora = new DateTime();
            $dataehoraFormatado = date("Y-m-d H:i:s", $dataehora->getTimestamp());

            $criar->execute([
                ':usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataehoraFormatado
            ]);

            $atualizarConvidado = $this->db->prepare('UPDATE convidado SET confirmacao = :confirmacao WHERE id_convidado = :id_convidado');
            $atualizarConvidado->execute([
                ':confirmacao' => 'confirmado',
                ':id_convidado' => $checkinDados['convidado_idconvidado']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Checkin criado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'convidado_idconvidado')) {
                throw new Exception('Checkin já cadastrado', 409);
            }
            if (str_contains($e->getMessage(), 'fk_checkin_usuario')) {
                throw new Exception('Usuário referenciado não encontrado', 409);
            }

            if (str_contains($e->getMessage(), 'fk_checkin_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 409);
            }


            throw new Exception('Erro ao criar checkin' . $e->getMessage(), 500);
        }
    }
}
