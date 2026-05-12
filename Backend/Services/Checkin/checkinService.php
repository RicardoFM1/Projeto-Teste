<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/db.php";

class CheckinService
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function buscarCheckinPorId($idCheckin)
    {
        try {
            if (empty($idCheckin)) {
                throw new Exception('Id do checkin não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM checkin WHERE id_checkin = :id_checkin');
            $buscar->execute([
                ':id_checkin' => $idCheckin
            ]);

            $checkin = $buscar->fetch();

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
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar checkin por id', 500);
        }
    }

    public function listarCheckins()
    {
        try {

            $query = $this->db->query("SELECT c.id_checkin, c.data_e_hora, u.id_usuario, u.nome AS usuario_nome,
            u.email AS usuario_email, u.cpf as usuario_cpf, u.cargo as usuario_cargo, co.id_convidado, 
            co.nome as convidado_nome, co.sobrenome as convidado_sobrenome, co.email as convidado_email,
            co.cpf as convidado_cpf, co.categoria as convidado_categoria, co.confirmacao as convidado_confirmacao,
            co.mesa_idmesa as convidado_mesa_idmesa, co.telefone as convidado_telefone FROM checkin c INNER JOIN usuario u ON u.id_usuario = c.usuario_idusuario
            INNER JOIN convidado co ON co.id_convidado = c.convidado_idconvidado");

            $resultado = [];

            while ($row = $query->fetch()) {
                $resultado = [
                    'checkin' => [
                        'id_checkin' => $row['id_checkin'],
                        'data_e_hora' => $row['data_e_hora'],
                        'usuario' => [
                            'id_usuario' => $row['id_usuario'],
                            'nome' => $row['usuario_nome'],
                            'email' => $row['usuario_email'],
                            'cpf' => $row['usuario_cpf'],
                            'cargo' => $row['usuario_cargo']
                        ],
                        'convidado' => [
                            'id_convidado' => $row['id_convidado'],
                            'nome' => $row['convidado_nome'],
                            'sobrenome' => $row['convidado_sobrenome'],
                            'email' => $row['convidado_email'],
                            'cpf' => $row['convidado_cpf'],
                            'categoria' => $row['convidado_categoria'],
                            'confirmacao' => $row['convidado_confirmacao'],
                            'mesa' => $row['convidado_mesa_idmesa'],
                            'telefone' => $row['convidado_telefone']
                        ]
                    ]
                ];

                return [
                    'sucesso' => true,
                    'dados' => $resultado
                ];
            }
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar listar checkins', 500);
        }
    }

    public function criarCheckin($checkinDados, $tokenJWT)
    {
        try {


            $dataFormatada = date('Y-m-d', $checkinDados['data_e_hora']);

            $buscarConvidado = $this->db->prepare('SELECT * FROM convidado WHERE id_convidado = :id_convidado');

            $buscarConvidado->execute([
                ':id_convidado' => $checkinDados['convidado_idconvidado']
            ]);

            
            $convidado = $buscarConvidado->fetch();
            
            if(empty($convidado)){
                throw new Exception('Convidado não encontrado para o checkin', 404);
            }

            if(isset($convidado) && $convidado['confirmacao'] === 'confirmado'){
                throw new Exception('Impossível fazer checkin de um convidado já confirmado', 409);
            }

            
            if(isset($convidado) && $convidado['confirmacao'] === 'cancelado'){
                throw new Exception('Impossível fazer checkin de um convidado cancelado', 409);
            }

            $criar = $this->db->prepare('INSERT INTO checkin (usuario_idusuario, convidado_idconvidado, data_e_hora)
        VALUES(:usuario_idusuario, :convidado_idconvidado, :data_e_hora)');

            $criar->execute([
                ':usuario_idusuario' => $tokenJWT->dados->id_usuario,
                ':convidado_idconvidado' => $checkinDados['convidado_idconvidado'],
                ':data_e_hora' => $dataFormatada

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
                throw new Exception('Convidado já confirmado', 409);
            }


            if (str_contains($e->getMessage(), 'fk_checkin_usuario')) {
                throw new Exception('Usuário referenciado não encontrado', 404);
            }

            if (str_contains($e->getMessage(), 'fk_checkin_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 404);
            }

            throw new Exception('Erro ao tentar criar checkin', 500);
        }
    }



    
}
