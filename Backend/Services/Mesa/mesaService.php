<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/db.php";

class MesaService
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function buscarMesaPorId($idMesa)
    {
        try {
            if (empty($idMesa)) {
                throw new Exception('Id da mesa não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM mesa WHERE id_mesa = :id_mesa');
            $buscar->execute([
                ':id_mesa' => $idMesa
            ]);

            $mesa = $buscar->fetch();

            if (empty($mesa)) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Mesa não encontrada pelo id',
                    'codigo' => 404
                ];
            }

            return [
                'sucesso' => true,
                'dados' => $mesa
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar mesa por id', 500);
        }
    }

    public function listarMesas()
    {
        try {

            $query = $this->db->query("SELECT * FROM mesa");

            $mesas = $query->fetchAll();

            return [
                'sucesso' => true,
                'dados' => $mesas
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar listar mesas', 500);
        }
    }

    public function criarMesa($mesaDados)
    {
        try {



            $criar = $this->db->prepare('INSERT INTO mesa (capacidade, restricao)
        VALUES(:capacidade, :restricao)');

            $criar->execute([
                ':capacidade' => $mesaDados['capacidade'],
                ':restricao' => $mesaDados['restricao']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa criada com sucesso'
            ];
        } catch (PDOException $e) {

            throw new Exception('Erro ao tentar criar mesa', 500);
        }
    }



    public function atualizarMesa($mesaDados, $idMesa)
    {
        try {
            $mesa = $this->buscarMesaPorId($idMesa);

            if ($mesa['sucesso'] === false) {
                throw new Exception($mesa['mensagem'], $mesa['codigo']);
            }



            $atualizar = $this->db->prepare('UPDATE mesa SET capacidade = :capacidade, restricao = :restricao
             WHERE id_mesa = :id_mesa');

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


            throw new Exception('Erro ao tentar atualizar mesa', 500);
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

            throw new Exception('Erro ao tentar deletar mesa', 500);
        }
    }
}
