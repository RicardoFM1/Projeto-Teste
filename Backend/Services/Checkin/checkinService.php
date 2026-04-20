<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class checkinService
{
    private $checkinDb;

    public function __construct()
    {
        $this->checkinDb = dbConnection();
    }


    public function buscarCheckinPorId($idCheckin)
    {
        if (empty($idCheckin)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarCheckin = $this->checkinDb->prepare("SELECT * FROM checkin WHERE id_checkin = :id_checkin");

        $buscarCheckin->execute([
            ':id_checkin' => $idCheckin
        ]);

        $checkin = $buscarCheckin->fetch();

        if (empty($checkin)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Checkin não encontrado pelo id',
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
        $query = $this->checkinDb->query("SELECT * FROM checkin");
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

            $criarCheckin = $this->checkinDb->prepare('INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
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
            if (str_contains($e->getMessage(), 'fk_checkin_usuario')) {
                throw new Exception('Usuário referenciado não encontrado', 409);
            }

            if (str_contains($e->getMessage(), 'fk_checkin_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 409);
            }

            if(str_contains($e->getMessage(), 'convidado_idconvidado')){
                throw new Exception('Checkin já cadastrado', 409);
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

            $dataehora = new DateTime();

            if ($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']) {
                throw new Exception('Sem permissão para editar este checkin', 403);
            }

            $atualizarCheckin = $this->checkinDb->prepare("UPDATE checkin SET usuario_idusuario = :usuario_idusuario, 
            convidado_idconvidado = :convidado_idconvidado  WHERE id_checkin = :id_checkin");

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
            if (str_contains($e->getMessage(), 'fk_checkin_usuario')) {
                throw new Exception('Usuário referenciado não encontrado', 409);
            }

            if (str_contains($e->getMessage(), 'fk_checkin_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 409);
            }

            if(str_contains($e->getMessage(), 'convidado_idconvidado')){
                throw new Exception('Checkin já cadastrado', 409);
            }

            throw new Exception('Erro ao atualizar checkin' . $e->getMessage(), 500);
        }
    }

    public function deletarCheckin($idCheckin, $tokenJWT)
    {
        try {

            if (empty($idCheckin)) {
                throw new Exception('Dados inválidos', 400);
            }

            $checkin = $this->buscarCheckinPorId($idCheckin);

            if ($checkin['sucesso'] === false) {
                throw new Exception($checkin['mensagem'], $checkin['codigo']);
            }

            if ($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']) {
                throw new Exception('Sem permissão para editar este checkin', 403);
            }

            $deletarCheckin = $this->checkinDb->prepare("DELETE FROM checkin WHERE id_checkin = :id_checkin");

            $deletarCheckin->execute([

                ':id_checkin' => $idCheckin
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Checkin deletado com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar checkin' . $e->getMessage(), 500);
        }
    }
}
