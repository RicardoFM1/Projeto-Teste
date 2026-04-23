<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";
require_once __DIR__ . "/../../Services/Mesa/mesaService.php";

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

        $buscarConvidado = $this->db->prepare('SELECT * FROM convidado WHERE email = :email_convidado');
        $buscarConvidado->execute([
            ':email_convidado' => $emailConvidado
        ]);

        $convidado = $buscarConvidado->fetch();

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

     public function buscarConvidadoPorIdMesa($idMesa)
    {
        if (empty($idMesa)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarConvidado = $this->db->prepare('SELECT * FROM convidado WHERE mesa_idmesa = :mesa_idmesa');
        $buscarConvidado->execute([
            ':mesa_idmesa' => $idMesa
        ]);

        $convidado = $buscarConvidado->fetch();

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

    public function listarConvidados()
    {
        $query = $this->db->query('SELECT * FROM convidado');

        $convidados = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $convidados
        ];
    }

    public function criarConvidado($convidadoDados)
    {
        try {
            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);
            $convidadoDados['telefone'] = substr($convidadoDados['telefone'], 0, 45);
            

            if(empty($convidadoDados['mesa_idmesa'])){
                $convidadoDados['mesa_idmesa'] = null;
            }

            $convidadosComReferenciaMesa = $this->buscarConvidadoPorIdMesa($convidadoDados['mesa_idmesa']);
            $mesa = new MesaService();
            $mesaComReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);

            if(count($convidadosComReferenciaMesa['dados']) >= $mesaComReferencia['dados']['capacidade']){
                throw new Exception('Mesa lotada', 409);
            }

            $criarConvidado = $this->db->prepare('INSERT INTO convidado (nome, sobrenome, email, cpf, telefone, categoria, confirmacao, mesa_idmesa)
        VALUES (:nome, :sobrenome, :email, :cpf, :telefone, :categoria, :confirmacao, :mesa_idmesa)');

            $criarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':telefone' => $convidadoDados['telefone'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
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

            if(str_contains($e->getMessage(), 'fk_convidado_mesa')){
                 throw new Exception('Mesa não encontrada', 404);
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
            
             if(empty($convidadoDados['mesa_idmesa'])){
                $convidadoDados['mesa_idmesa'] = null;
            }

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);
            $convidadoDados['telefone'] = substr($convidadoDados['telefone'], 0, 45);

             $convidadosComReferenciaMesa = $this->buscarConvidadoPorIdMesa($convidadoDados['mesa_idmesa']);
            $mesa = new MesaService();
            $mesaComReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_idmesa']);

            if(count($convidadosComReferenciaMesa['dados']) >= $mesaComReferencia['dados']['capacidade']){
                throw new Exception('Mesa lotada', 409);
            }

            $atualizarConvidado = $this->db->prepare('UPDATE convidado SET nome = :nome, sobrenome = :sobrenome,
             email = :email, cpf = :cpf, telefone = :telefone, categoria = :categoria, confirmacao = :confirmacao, mesa_idmesa = :mesa_idmesa
             WHERE email = :email_antigo');

            $atualizarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':telefone' => $convidadoDados['telefone'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':mesa_idmesa' => $convidadoDados['mesa_idmesa'],
                'email_antigo' => $emailConvidado
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
            if(str_contains($e->getMessage(), 'fk_convidado_mesa')){
                 throw new Exception('Mesa não encontrada', 404);
            }

            throw new Exception('Erro ao criar convidado', 500);
        }
    }

    public function deletarConvidado($emailConvidado)
    {
        if (empty($emailConvidado)) {
            throw new Exception('Dados inválidos', 400);
        }

        $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

        if ($convidado['sucesso'] === false) {
            throw new Exception($convidado['mensagem'], $convidado['codigo']);
        }

        $deletarConvidado = $this->db->prepare('DELETE FROM convidado WHERE email = :email');

        $deletarConvidado->execute([
            ':email' => $emailConvidado
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Convidado deletado com sucesso'
        ];
    }
}
