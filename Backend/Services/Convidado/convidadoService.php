<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class ConvidadoService
{
    protected $db;

    public function __construct()
    {
        $this->db = dbConnection();
    }

    public function buscarConvidadoPorEmail($emailConvidado)
    {
        if (empty($emailConvidado)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM convidado WHERE email = :email');

        $buscar->execute([
            ':email' => $emailConvidado
        ]);

        $convidado = $buscar->fetch();

        if (empty($convidado)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Convidado não encontrado',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidado
        ];
    }

    public function buscarConvidadoPorMesaId($idMesa)
    {
        if (empty($idMesa)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM convidado WHERE mesa_idmesa = :mesa_idmesa');

        $buscar->execute([
            ':mesa_idmesa' => $idMesa
        ]);

        $convidados = $buscar->fetchAll();

        if (empty($convidados)) {
            return [
                'sucesso' => true,
                'dados' => $convidados ?? []
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidados
        ];
    }

    public function listarConvidados()
    {
        $query = $this->db->query("SELECT * FROM convidado ORDER BY id_convidado DESC");

        $convidados = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $convidados,
            'total' => count($convidados)
        ];
    }


    public function criarConvidado($convidadoDados)
    {
        try {
            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);
            $convidadoDados['telefone'] = substr($convidadoDados['telefone'], 0, 45);

            if (empty($convidadoDados['mesa_idmesa'])) {
                $convidadoDados['mesa_idmesa'] = null;
            }

            if (!empty($convidadoDados['mesa_idmesa'])) {

                $convidadosReferenciaMesa = $this->buscarConvidadoPorMesaId($convidadoDados['mesa_idmesa']);
                $mesa = new MesaService();
                $mesaReferenciada = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);


                if (count($convidadosReferenciaMesa['dados']) >= $mesaReferenciada['dados']['capacidade']) {
                    throw new Exception('Mesa lotada', 409);
                }
            }

            $criar = $this->db->prepare('INSERT INTO convidado (nome, sobrenome, email, cpf, categoria, telefone, mesa_idmesa)
            VALUES (:nome, :sobrenome, :email, :cpf, :categoria, :telefone, :mesa_idmesa)');

            $criar->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':telefone' => $convidadoDados['telefone'],
                ':mesa_idmesa' => $convidadoDados['mesa_idmesa']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Convidado criado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_convidado_mesa')) {
                throw new Exception('Mesa referenciada não encontrada', 409);
            }


            throw new Exception('Erro ao criar convidado', 500);
        }
    }




    public function atualizarConvidado($convidadoDados, $emailConvidado)
    {
        try {

            if (empty($emailConvidado)) {
                throw new Exception('Dados inválidos', 400);
            }

            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);
            $convidadoDados['telefone'] = substr($convidadoDados['telefone'], 0, 45);

            if (empty($convidadoDados['mesa_idmesa'])) {
                $convidadoDados['mesa_idmesa'] = null;
            }

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            if ($convidadoDados['confirmacao'] !== 'cancelado') {
                throw new Exception('Confirmação inválida', 400);
            }

            if ($convidado['dados']['confirmacao'] === 'confirmado') {
                throw new Exception('Impossível atualizar um convidado já confirmado');
            }

            if (!empty($convidadoDados['mesa_idmesa'])) {

                $convidadosReferenciaMesa = $this->buscarConvidadoPorMesaId($convidadoDados['mesa_idmesa']);
                $mesa = new MesaService();
                $mesaReferenciada = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);


                if (count($convidadosReferenciaMesa['dados']) >= $mesaReferenciada['dados']['capacidade']) {
                    throw new Exception('Mesa lotada', 409);
                }
            }

            $atualizar = $this->db->prepare('UPDATE convidado set nome = :nome, sobrenome = :sobrenome,  email = :email, cpf = :cpf, categoria = :categoria,
            confirmacao = :confirmacao, telefone = :telefone, mesa_idmesa = :mesa_idmesa WHERE email = :email_antigo');

            $atualizar->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':telefone' => $convidadoDados['telefone'],
                ':mesa_idmesa' => $convidadoDados['mesa_idmesa'],
                ':email_antigo' => $emailConvidado
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Convidado atualizado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            if (str_contains($e->getMessage(), 'fk_convidado_mesa')) {
                throw new Exception('Mesa referenciada não encontrada', 409);
            }


            throw new Exception('Erro ao atualizar convidado', 500);
        }
    }

    public function deletarConvidado($emailConvidado)
    {
        try {

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            if ($convidado['dados']['confirmacao'] === 'confirmado') {
                throw new Exception('Impossível deletar um convidado já confirmado');
            }


            $deletar = $this->db->prepare('DELETE FROM convidado WHERE email = :email');

            $deletar->execute([
                ':email' => $emailConvidado
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Convidado deletado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'parent row')) {
                throw new Exception('Impossível deletar um convidado referenciado');
            }

            throw new Exception('Erro ao deletar convidado', 500);
        }
    }
}
