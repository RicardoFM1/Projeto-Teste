<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

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

        $buscarCheckin = $this->db->prepare('SELECT * FROM checkin WHERE id_checkin = :id_checkin');
        $buscarCheckin->execute([
            ':id_checkin' => $idCheckin
        ]);

        $checkin = $buscarCheckin->fetch();

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
        $query = $this->db->query('SELECT * FROM checkin');

        $checkins = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $checkins
        ];
    }

    public function criarCheckin($checkinDados, $tokenJWT)
    {
        try {
            $dataehora = new DateTime();

            $criarCheckin = $this->db->prepare('INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
        VALUES (:usuario_idusuario, :convidado_idconvidado, :data_e_hora)');

            $criarCheckin->execute([
                ':usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataehora->getTimestamp()
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

            throw new Exception('Erro ao criar usuário', 500);
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
            throw new Exception('Sem permissão para atualizar', 403);
        }

            $dataehora = new Datetime();


            $atualizarCheckin = $this->db->prepare('UPDATE checkin SET usuario_idusuario = :usuario_idusuario,
             convidado_idconvidado = :convidado_idconvidado, data_e_hora = :data_e_hora
            WHERE id_checkin = :id_checkin');

            $atualizarCheckin->execute([
                ':usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataehora->getTimestamp(),
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

            throw new Exception('Erro ao criar usuário', 500);
        }
    }

    public function deletarCheckin($idCheckin, $tokenJWT)
    {
        if (empty($idCheckin)) {
            throw new Exception('Dados inválidos', 400);
        }

        $checkin = $this->buscarCheckinPorId($idCheckin);

        if ($checkin['sucesso'] === false) {
            throw new Exception($checkin['mensagem'], $checkin['codigo']);
        }

        if($tokenJWT->dados->cargo_usuario !== 'admin' && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']){
            throw new Exception('Sem permissão para deletar', 403);
        }

        $deletarCheckin = $this->db->prepare('DELETE FROM checkin WHERE id_checkin = :id_checkin');

        $deletarCheckin->execute([
            ':id_checkin' => $idCheckin
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Checkin deletado com sucesso'
        ];
    }
}
