<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class MesaService
{
    protected $db;

    public function __construct()
    {
        $this->db = dbConnection();
    }

    public function buscarMesaPorId($idMesa)
    {
        if (empty($idMesa)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM mesa WHERE id_mesa = :id_mesa');

        $buscar->execute([
            ':id_mesa' => $idMesa
        ]);

        $mesa = $buscar->fetch();

        if (empty($mesa)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Mesa não encontrada',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $mesa
        ];
    }

    public function listarMesas()
    {
        $query = $this->db->query("SELECT * FROM mesa ORDER BY id_mesa DESC");

        $mesas = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $mesas
        ];
    }


    public function criarMesas($mesaDados)
    {
        try {
         

            $criar = $this->db->prepare('INSERT INTO mesa (capacidade, restricao)
            VALUES (:capacidade, :restricao)');

            $criar->execute([
                ':capacidade' => $mesaDados['capacidade'],
                ':restricao' => $mesaDados['restricao']
                
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa criada com sucesso'
            ];
        } catch (PDOException $e) {
            
            throw new Exception('Erro ao criar mesa', 500);
        }
    }


   


    public function atualizarMesa($mesaDados, $idMesa)
    {
        try {

            if (empty($idMesa)) {
                throw new Exception('Dados inválidos', 400);
            }

            

            $mesa = $this->buscarMesaPorId($idMesa);

            if ($mesa['sucesso'] === false) {
                throw new Exception($mesa['mensagem'], $mesa['codigo']);
            }

            $atualizar = $this->db->prepare('UPDATE mesa set capacidade = :capacidade, restricao = :restricao WHERE id_mesa = :id_mesa');

            $atualizar->execute([
                ':capacidade' => $mesaDados['capacidade'],
                ':restricao' => $mesaDados['restricao'],
                ':id_mesa' => $idMesa
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa atualizada com sucesso'
            ];
        } catch (PDOException $e) {
            

            throw new Exception('Erro ao atualizar mesa', 500);
        }
    }

    public function deletarMesa($idMesa)
    {
        try {

            $mesa = $this->buscarMesaPorId($idMesa);

            if ($mesa['sucesso'] === false) {
                throw new Exception($mesa['mensagem'], $mesa['codigo']);
            }


            $deletar = $this->db->prepare('DELETE FROM mesa WHERE id_mesa = :id_mesa');

            $deletar->execute([
                ':id_mesa' => $idMesa
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa deletada com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar mesa', 500);
        }
    }
}
