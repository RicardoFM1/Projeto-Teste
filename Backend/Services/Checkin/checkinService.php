<?php



require_once __DIR__ . "/../../Connection/connection.php";

class CheckinService
{
    private $Db;


    public function __construct()
    {
        $this->Db = dbConnection();
    }

    public function buscarCheckinPorId($idCheckin)
    {
        if (empty($idCheckin)) {
            throw new Exception('Id do checkin não fornecido', 400);
        }

        $buscarCheckin = $this->Db->prepare("SELECT * FROM checkin WHERE id_checkin = :id_checkin");
        $buscarCheckin->execute([
            'id_checkin' => $idCheckin
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
        $query = $this->Db->query("SELECT * FROM checkin");
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

            $criarCheckin = $this->Db->prepare("INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
            VALUES (:usuario_idusuario, :convidado_idconvidado, :data_e_hora)");

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
                throw new Exception('Checkin já existente', 409);
            }

            throw new Exception('Erro ao criar checkin', 500);
        }
    }




    public function atualizarCheckin($checkinDados, $idCheckin, $tokenJWT)
    {
        try {

            if (empty($idCheckin)) {
                throw new Exception('Id do checkin não fornecido', 400);
            }


            $checkin = $this->buscarCheckinPorId($idCheckin);

            if ($checkin['sucesso'] === false) {
                throw new Exception($checkin['mensagem'], $checkin['codigo']);
            }

            if ($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']) {
                throw new Exception('Sem permissão pra editar esse checkin', 403);
            }

            $dataehora = new DateTime();

            $atualizarCheckin = $this->Db->prepare("UPDATE checkin SET usuario_idusuario = :usuario_idusuario, convidado_idconvidado = :convidado_idconvidado, 
            data_e_hora = :data_e_hora WHERE id_checkin = :id_checkin");

            $atualizarCheckin->execute([
                'usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_id_convidado' => $checkinDados['convidado_id_convidado'],
                ':data_e_hora' => $dataehora->getTimestamp()

            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Checkin atualizado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'convidado_idconvidado')) {
                throw new Exception('Checkin já existente', 409);
            }

            throw new Exception('Erro ao criar checkin', 500);
        }
    }

    public function deletarCheckin($idCheckin, $tokenJWT)
    {
        if (empty($idCheckin)) {
            throw new Exception('Id do checkin não fornecido', 400);
        }



        $checkin = $this->buscarCheckinPorId($idCheckin);

        if ($checkin['sucesso'] === false) {
            throw new Exception($checkin['mensagem'], $checkin['codigo']);
        }

        if($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $checkin['dados']['usuario_idusuario']){
                throw new Exception('Sem permissão pra deletar esse checkin', 403);
        }

        $deletarCheckin = $this->Db->prepare('DELETE FROM checkin WHERE id_checkin = :id_checkin');
        $deletarCheckin->execute([
            ':id_checkin' => $idCheckin
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Checkin deletado com sucesso'
        ];
    }
}
