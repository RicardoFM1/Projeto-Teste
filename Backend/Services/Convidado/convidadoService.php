<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/db.php";

class ConvidadoService
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function buscarConvidadoPorEmail($emailConvidado)
    {
        try {
            if (empty($emailConvidado)) {
                throw new Exception('Email do convidado não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM convidado WHERE email = :email');
            $buscar->execute([
                ':email' => $emailConvidado
            ]);

            $convidado = $buscar->fetch();

            if (empty($convidado)) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Convidado não encontrado pelo email',
                    'codigo' => 404
                ];
            }

            return [
                'sucesso' => true,
                'dados' => $convidado
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar convidado por email', 500);
        }
    }

    public function buscarConvidadoPorMesaId($idMesa)
    {
        try {
            if (empty($idMesa)) {
                throw new Exception('Id da mesa não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM convidado WHERE mesa_idmesa = :mesa_idmesa');
            $buscar->execute([
                ':mesa_idmesa' => $idMesa
            ]);

            $convidados = $buscar->fetchAll();

            if (empty($mesa)) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Convidado não encontrado pelo id da mesa',
                    'codigo' => 404
                ];
            }

            return [
                'sucesso' => true,
                'dados' => $convidados
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar convidado por id da mesa', 500);
        }
    }

    public function listarConvidados()
    {
        try {

            $query = $this->db->query("SELECT * FROM convidado");

            $convidados = $query->fetchAll();

            return [
                'sucesso' => true,
                'dados' => $convidados
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar listar convidados', 500);
        }
    }

    public function criarConvidado($convidadoDados)
    {
        try {

            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);

            $mesa = new MesaService();
            $mesaComReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);
            $convidadosNaMesa = $this->buscarConvidadoPorMesaId($convidadoDados['mesa_idmesa']);

            if (count($convidadosNaMesa['dados']) >= $mesaComReferencia['dados']['capacidade']) {
                throw new Exception('Mesa lotada', 409);
            }

            $criar = $this->db->prepare('INSERT INTO convidado (nome, sobrenome, email, cpf, categoria, mesa_idmesa, telefone)
        VALUES(:nome, :sobrenome, :email, :cpf, :categoria, :mesa_idmesa, :telefone)');

            $criar->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':mesa_idmesa' => $convidadoDados['mesa_idmesa'],
                ':telefone' => $convidadoDados['telefone']
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
                throw new Exception('Mesa referenciada não encontrada', 404);
            }

            throw new Exception('Erro ao tentar criar convidado', 500);
        }
    }



    public function atualizarConvidado($convidadoDados, $emailConvidado)
    {
        try {
            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            if ($convidadoDados['confirmacao'] !== 'cancelado') {
                throw new Exception('Só é possível cancelar um convidado', 400);
            }

            if ($convidado['dados']['confirmacao'] === 'confirmado') {
                throw new Exception('Não é possível cancelar um convidado já confirmado', 400);
            }

            $mesa = new MesaService();
            $mesaComReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);
            $convidadosNaMesa = $this->buscarConvidadoPorMesaId($convidadoDados['mesa_idmesa']);

            if (count($convidadosNaMesa['dados']) >= $mesaComReferencia['dados']['capacidade'] && $convidadoDados['mesa_idmesa'] !== $convidado['dados']['mesa_idmesa']) {
                throw new Exception('Mesa lotada', 409);
            }

            $atualizar = $this->db->prepare('UPDATE convidado SET nome = :nome, sobrenome = :sobrenome,
            email = :email, cpf = :cpf, categoria = :categoria, confirmacao = :confirmacao, 
            mesa_idmesa = :mesa_idmesa, telefone = :telefone WHERE email = :email_convidado');

            $atualizar->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':mesa_idmesa' => $convidadoDados['mesa_idmesa'],
                ':telefone' => $convidadoDados['telefone'],
                ':email_convidado' => $emailConvidado
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
                throw new Exception('Mesa referenciada não encontrada', 404);
            }


            throw new Exception('Erro ao tentar atualizar convidado', 500);
        }
    }

    public function deletarConvidado($emailConvidado)
    {
        try {

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
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
                throw new Exception('Impossível deletar um convidado referenciado', 409);
            }

            throw new Exception('Erro ao tentar deletar convidado', 500);
        }
    }
}
