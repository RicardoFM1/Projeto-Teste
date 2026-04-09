<?php


use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../Connection/checkinConnection.php";


class CheckinService
{
    protected $checkinDb;

    public function __construct()
    {
        $this->checkinDb = dbCheckinConnection();
    }

    public function listarCheckins()
    {
        $stmt = $this->checkinDb->query("SELECT * FROM checkin");
        $checkins = $stmt->fetchAll();
        return [
            'sucesso' => true,
            'dados' => $checkins
        ];
    }

    public function buscarCheckinPorId($idCheckin)
    {
        if (empty($idCheckin)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Id do checkin não informado',
                'codigo' => 400
            ];
        }

        $acharCheckin = $this->checkinDb->prepare("SELECT * FROM checkin WHERE id_checkin = :id_checkin");
        $acharCheckin->execute([':id_checkin' => $idCheckin]);
        $checkin = $acharCheckin->fetch();

        if (empty($checkin)) {

            return [
                'sucesso' => false,
                'mensagem' => "Checkin não encontrado pelo ID",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $checkin
        ];
    }

    public function buscarCheckinExistentePorConvidadoId($idConvidado)
    {
        if (empty($idConvidado)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Id do convidado não informado',
                'codigo' => 400
            ];
        }

        $acharCheckinPorConvidadoId = $this->checkinDb->prepare("SELECT * FROM checkin WHERE convidado_idconvidado = :convidado_idconvidado");
        $acharCheckinPorConvidadoId->execute([':convidado_idconvidado' => $idConvidado]);
        $checkin = $acharCheckinPorConvidadoId->fetch();

        if (empty($checkin)) {

            return [
                'sucesso' => false,
                'mensagem' => "Checkin não encontrado pelo Id do convidado",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $checkin
        ];
    }


    private function validarToken($idCheckin = null, $tokenJWT)
    {
        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }

        $checkin = $this->buscarCheckinPorId($idCheckin);

        if (isset($checkin['sucesso']) && $checkin['sucesso'] === false) {
            throw new Exception($checkin['mensagem'], $checkin['codigo']);
        }

        $chaveSecreta = $_ENV['JWT_SECRET_KEY'];

        $partesToken = explode(' ', $tokenJWT);

        if (count($partesToken) !== 2 || strcmp($partesToken[0], 'Bearer') !== 0) {
            throw new Exception('Token inválido, aceito apenas: Bearer {token}', 401);
        }
        try {

            return JWT::decode($partesToken[1], new Key($chaveSecreta, 'HS256'));
        } catch (Exception $e) {

            throw new Exception('Token inválido, expirado ou sem permissão', 401);
        }
    }




    public function criarCheckin($checkinDados, $tokenJWT)
    {

        if(empty($checkinDados['convidado_idconvidado'])){
            throw new Exception("Necessário informar o convidado", 400);
        }
       
        if ($this->buscarCheckinExistentePorConvidadoId($checkinDados['convidado_idconvidado'])['sucesso']) {
            throw new Exception("Este Checkin já está cadastrado", 409);
        }

       $jwtDecodificado = $this->validarToken(null, $tokenJWT);

        $stmt = $this->checkinDb->prepare("INSERT INTO checkin(usuario_idusuario, convidado_idconvidado)
        VALUES (:usuario_idusuario, :convidado_idconvidado)");

        $stmt->execute([
            ':usuario_idusuario' => $jwtDecodificado->dados->id_usuario,
            ':convidado_idconvidado' => $checkinDados['convidado_idconvidado']
        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Checkin criado com sucesso'
        ];
    }


    public function atualizarCheckin($checkinDados, $idCheckin, $tokenJWT)
    {

        if(empty($idCheckin)){
            throw new Exception("Necessário informar o checkin", 400);
        }


        $checkin = $this->buscarCheckinPorId($idCheckin);

        if (isset($checkin['sucesso']) && $checkin['sucesso'] === false) {
            throw new Exception($checkin['mensagem'], $checkin['codigo']);
        }

        $jwtDecodificado = $this->validarToken(null, $tokenJWT);

        if ($jwtDecodificado->dados->cargo !== 'admin' && $jwtDecodificado->dados->id_usuario !== $checkin['dados']['usuario_idusuario']) {
            throw new Exception('Sem permissão para atualizar esse checkin', 401);
        }

        $atualizarCheckin = $this->checkinDb->prepare("UPDATE checkin SET usuario_idusuario = :usuario_idusuario, convidado_idconvidado = :convidado_idconvidado
        WHERE id_checkin = :id_checkin");

        $atualizarCheckin->execute([
            ':id_checkin' => $idCheckin,
            ':usuario_idusuario' => $jwtDecodificado->dados->id_usuario,
            ':convidado_idconvidado' => $checkinDados['convidado_idconvidado']

        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Checkin atualizado com sucesso'
        ];
    }

    public function deletarCheckin($idCheckin, $tokenJWT)
    {

        if(empty($idCheckin)){
            throw new Exception("Necessário informar o checkin", 400);
        }

        $checkin = $this->buscarCheckinPorId($idCheckin);

        if (isset($checkin['sucesso']) && $checkin['sucesso'] === false) {
            throw new Exception($checkin['mensagem'], $checkin['codigo']);
        }

        $jwtDecodificado = $this->validarToken(null, $tokenJWT);

        if ($jwtDecodificado->dados->cargo !== 'admin' && $jwtDecodificado->dados->id_usuario !== $checkin['dados']['usuario_idusuario']) {
            throw new Exception('Sem permissão para deletar esse checkin', 401);
        }

        $deletarCheckin = $this->checkinDb->prepare("DELETE FROM checkin WHERE id_checkin = :id_checkin");
        $deletarCheckin->execute([':id_checkin' => $idCheckin]);


        return [
            'sucesso' => true,
            'mensagem' => 'Checkin deletado com sucesso'
        ];
    }
}
