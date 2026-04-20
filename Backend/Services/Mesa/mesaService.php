<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class MesaService
{
    private $mesaDb;

    public function __construct()
    {
        $this->mesaDb = dbConnection();
    }


    public function buscarMesaPorId($idMesa)
    {
        if (empty($idMesa)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarMesa = $this->mesaDb->prepare("SELECT * FROM mesa WHERE id_mesa = :id_mesa");

        $buscarMesa->execute([
            ':id_mesa' => $idMesa
        ]);

        $mesa = $buscarMesa->fetch();

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
    }



    public function listarMesas()
    {
        $query = $this->mesaDb->query("SELECT * FROM mesa");
        $mesas = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $mesas
        ];
    }

    public function criarMesa($mesaDados)
    {
        try {

            $criarMesa = $this->mesaDb->prepare('INSERT INTO mesa (capacidade, restricao)
        VALUES (:capacidade, :restricao)');

            $criarMesa->execute([
                ':capacidade' => $mesaDados['capacidade'],
                ':restricao' => $mesaDados['restricao']

            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa criada com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao criar mesa' . $e->getMessage(), 500);
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


            $atualizarMesa = $this->mesaDb->prepare("UPDATE mesa SET capacidade = :capacidade, restricao = :restricao
            WHERE id_mesa = :id_mesa");

            $atualizarMesa->execute([
                ':capacidade' => $mesaDados['capacidade'],
                ':restricao' => $mesaDados['restricao'],
                ':id_mesa' => $idMesa
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa atualizada com sucesso'
            ];
        } catch (PDOException $e) {


            throw new Exception('Erro ao atualizar mesa' . $e->getMessage(), 500);
        }
    }

    public function deletarMesa($idMesa)
    {
        try {

            if (empty($idMesa)) {
                throw new Exception('Dados inválidos', 400);
            }

            $mesa = $this->buscarMesaPorId($idMesa);

            if ($mesa['sucesso'] === false) {
                throw new Exception($mesa['mensagem'], $mesa['codigo']);
            }

            $deletarMesa = $this->mesaDb->prepare("DELETE FROM mesa WHERE id_mesa = :id_mesa");

            $deletarMesa->execute([

                ':id_mesa' => $idMesa
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Mesa deletada com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar mesa' . $e->getMessage(), 500);
        }
    }
}
