<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/db.php";

class AcompanhanteService
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function buscarAcompanhantePorEmail($emailAcompanhante)
    {
        try {
            if (empty($emailAcompanhante)) {
                throw new Exception('Email do acompanhante não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM acompanhante WHERE email = :email');
            $buscar->execute([
                ':email' => $emailAcompanhante
            ]);

            $acompanhante = $buscar->fetch();

            if (empty($acompanhante)) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Acompanhante não encontrado pelo email',
                    'codigo' => 404
                ];
            }

            return [
                'sucesso' => true,
                'dados' => $acompanhante
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar acompanhante por email', 500);
        }
    }

    public function listarAcompanhantes()
    {
        try {

            $query = $this->db->query("SELECT * FROM acompanhante");

            $acompanhante = $query->fetchAll();

            return [
                'sucesso' => true,
                'dados' => $acompanhante
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar listar acompanhantes', 500);
        }
    }

    public function criarAcompanhante($acompanhanteDados)
    {
        try {

            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);




            $criar = $this->db->prepare('INSERT INTO acompanhante (nome, sobrenome, email, cpf, idade, convidado_idconvidado)
        VALUES(:nome, :sobrenome, :email, :cpf, :idade, :convidado_idconvidado)');

            $criar->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':email' => $acompanhanteDados['email'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':idade' => $acompanhanteDados['idade'],
                ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado']

            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante criado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_acompanhante_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 404);
            }

            throw new Exception('Erro ao tentar criar convidado', 500);
        }
    }



    public function atualizarAcompanhante($acompanhanteDados, $emailAcompanhante)
    {
        try {
            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);
           

            $acompanhante = $this->buscarAcompanhantePorEmail($emailAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }

           
            $atualizar = $this->db->prepare('UPDATE acompanhante SET nome = :nome, sobrenome = :sobrenome,
            email = :email, cpf = :cpf, idade = :idade, convidado_idconvidado = :convidado_idconvidado
           WHERE email = :email_acompanhante');

            $atualizar->execute([
                  ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':email' => $acompanhanteDados['email'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':idade' => $acompanhanteDados['idade'],
                ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado'],
                ':email_acompanhante' => $emailAcompanhante
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante atualizado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_acompanhante_convidado')) {
                throw new Exception('Convidado referenciado não encontrado', 404);
            }


            throw new Exception('Erro ao tentar atualizar convidado', 500);
        }
    }

    public function deletarAcompanhante($emailAcompanhante)
    {
        try {

            $acompanhante = $this->buscarAcompanhantePorEmail($emailAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }

            $deletar = $this->db->prepare('DELETE FROM acompanhante WHERE email = :email');

            $deletar->execute([
                ':email' => $emailAcompanhante
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante deletado com sucesso'
            ];
        } catch (PDOException $e) {
            
            throw new Exception('Erro ao tentar deletar acompanhante', 500);
        }
    }
}
