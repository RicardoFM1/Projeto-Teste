<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class AcompanhanteService
{
    protected $db;

    public function __construct()
    {
        $this->db = dbConnection();
    }

    public function buscarAcompanhantePorId($idAcompanhante)
    {
        if (empty($idAcompanhante)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM acompanhante WHERE id_acompanhante = :id_acompanhante');

        $buscar->execute([
            ':id_acompanhante' => $idAcompanhante
        ]);

        $acompanhante = $buscar->fetch();

        if (empty($acompanhante)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Acompanhante não encontrado',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $acompanhante
        ];
    }

    public function listarAcompanhantes()
    {
        $query = $this->db->query("SELECT a.id_acompanhante, a.nome, a.sobrenome,
        a.cpf, a.idade, co.cpf as convidado_cpf FROM acompanhante a JOIN convidado co ON co.id_convidado = a.convidado_idconvidado
         ORDER BY id_acompanhante DESC");

        $acompanhantes = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $acompanhantes,
            'total' => count($acompanhantes)
        ];
    }


    public function criarAcompanhante($acompanhanteDados)
    {
        try {
            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);



            $criar = $this->db->prepare('INSERT INTO acompanhante (nome, sobrenome, cpf, idade, convidado_idconvidado)
            VALUES (:nome, :sobrenome, :cpf, :idade, :convidado_idconvidado)');

            $criar->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':idade' => $acompanhanteDados['idade'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado']

            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante criado com sucesso'
            ];
        } catch (PDOException $e) {

            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_acompanhante_convidado')) {
                throw new Exception('Convidado referenciado não encontrada', 409);
            }


            throw new Exception('Erro ao criar acompanhante', 500);
        }
    }




    public function atualizarAcompanhante($acompanhanteDados, $idAcompanhante)
    {
        try {

            if (empty($idAcompanhante)) {
                throw new Exception('Dados inválidos', 400);
            }

            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);




            $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }

            $atualizar = $this->db->prepare('UPDATE acompanhante set nome = :nome, sobrenome = :sobrenome, cpf = :cpf, idade = :idade,
            convidado_idconvidado = :convidado_idconvidado WHERE id_acompanhante = :id_acompanhante');

            $atualizar->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':idade' => $acompanhanteDados['idade'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado'],
                ':id_acompanhante' => $idAcompanhante
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante atualizado com sucesso'
            ];
        } catch (PDOException $e) {

            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_acompanhante_convidado')) {
                throw new Exception('Convidado referenciado não encontrada', 409);
            }


            throw new Exception('Erro ao atualizar acompanhante', 500);
        }
    }

    public function deletarAcompanhante($idAcompanhante)
    {
        try {

            $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }


            $deletar = $this->db->prepare('DELETE FROM acompanhante WHERE id_acompanhante = :id_acompanhante');

            $deletar->execute([
                ':id_acompanhante' => $idAcompanhante
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante deletado com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar acompanhante', 500);
        }
    }
}
