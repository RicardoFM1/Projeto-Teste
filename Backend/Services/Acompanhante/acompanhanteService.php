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

        $buscarAcompanhante = $this->db->prepare('SELECT * FROM acompanhante WHERE id_acompanhante = :id_acompanhante');
        $buscarAcompanhante->execute([
            ':id_acompanhante' => $idAcompanhante
        ]);

        $acompanhante = $buscarAcompanhante->fetch();

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
        $query = $this->db->query('SELECT * FROM acompanhante');

        $acompanhantes = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $acompanhantes
        ];
    }

    public function criarAcompanhante($acompanhanteDados)
    {
        try {
            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);
           

            $criarAcompanhante = $this->db->prepare('INSERT INTO acompanhante (nome, sobrenome, cpf, idade, convidado_idconvidado)
        VALUES (:nome, :sobrenome, :cpf, :idade, :convidado_idconvidado)');

            $criarAcompanhante->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':idade' => $acompanhanteDados['idade'],
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

            if(str_contains($e->getMessage(), 'fk_acompanhante_convidado')){
                 throw new Exception('Convidado não encontrado', 404);
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
            
           

            $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }

            $convidadoDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);
           

            

            $atualizarAcompanhante = $this->db->prepare('UPDATE acompanhante SET nome = :nome, sobrenome = :sobrenome,
            cpf = :cpf, idade = :idade, convidado_idconvidado = :convidado_idconvidado
            WHERE id_acompanhante = :id_acompanhante');

            $atualizarAcompanhante->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':idade' => $acompanhanteDados['idade'],
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

            if(str_contains($e->getMessage(), 'fk_acompanhante_convidado')){
                 throw new Exception('Convidado não encontrado', 404);
            }

            throw new Exception('Erro ao criar acompanhante', 500);
        }
    }

    public function deletarAcompanhante($idAcompanhante)
    {
        if (empty($idAcompanhante)) {
            throw new Exception('Dados inválidos', 400);
        }

        $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

        if ($acompanhante['sucesso'] === false) {
            throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
        }

        $deletarAcompanhante = $this->db->prepare('DELETE FROM acompanhante WHERE id_acompanhante = :id_acompanhante');

        $deletarAcompanhante->execute([
            ':id_acompanhante' => $idAcompanhante
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Acompanhante deletado com sucesso'
        ];
    }
}
