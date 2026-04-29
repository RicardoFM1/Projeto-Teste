<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

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
            'dados' => $checkins
        ];
    }


    public function criarCheckin($checkinDados, $tokenJWT)
    {
        try {

            $criar = $this->db->prepare('INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
            VALUES (:usuario_idusuario, :convidado_idconvidado, :data_e_hora)');

            $dataehora = new DateTime();   
            $dataehoraFormatado = date("Y-m-d H:i:s", $dataehora->getTimestamp());        

            $criar->execute([
                ':usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataehoraFormatado
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


   

    public function atualizarCheckin($checkinDados, $idCheckin, $tokenJWT)
    {
        try {

            if (empty($idCheckin)) {
                throw new Exception('Dados inválidos', 400);
            }

             

            $checkin = $this->buscarCheckinPorId($idCheckin);

            if ($checkin['sucesso'] === false) {
                throw new Exception($checkin['mensagem'], $checkin['codigo']);
            }

            if($tokenJWT->dados->cargo_usuario !== 'admin' && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']){
                throw new Exception('Sem permissão', 403);
            }

           $dataehora = new DateTime();   
            $dataehoraFormatado = date("Y-m-d H:i:s", $dataehora->getTimestamp());            

            $atualizar = $this->db->prepare('UPDATE checkin set convidado_idconvidado = :convidado_idconvidado,  data_e_hora = :data_e_hora
             WHERE id_checkin = :id_checkin');

            $atualizar->execute([
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataehoraFormatado,
                ':id_checkin' => $idCheckin
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Checkin atualizado com sucesso'
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


            throw new Exception('Erro ao atualizar checkin', 500);
        }
    }

    public function deletarCheckin($idCheckin, $tokenJWT)
    {
        try {

            $checkin = $this->buscarCheckinPorId($idCheckin);

            if ($checkin['sucesso'] === false) {
                throw new Exception($checkin['mensagem'], $checkin['codigo']);
            }

             if($tokenJWT->dados->cargo_usuario !== 'admin' && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']){
                throw new Exception('Sem permissão', 403);
            }

            $deletar = $this->db->prepare('DELETE FROM checkin WHERE id_checkin = :id_checkin');

            $deletar->execute([
                ':id_checkin' => $idCheckin
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Checkin deletado com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar checkin', 500);
        }
    }
}
