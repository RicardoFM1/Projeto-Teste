<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class ConvidadoService
{
    private $convidadoDb;

    public function __construct()
    {
        $this->convidadoDb = dbConnection();
    }


    public function buscarConvidadoPorEmail($emailConvidado)
    {
        if (empty($emailConvidado)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarConvidado = $this->convidadoDb->prepare("SELECT * FROM convidado WHERE email = :email");

        $buscarConvidado->execute([
            ':email' => $emailConvidado
        ]);

        $convidado = $buscarConvidado->fetch();

        if (empty($convidado)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Convidado não encontrado pelo id',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidado
        ];
    }

    public function buscarConvidadosPorMesaId($mesaId)
    {
        if (empty($mesaId)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarConvidado = $this->convidadoDb->prepare("SELECT * FROM convidado WHERE mesa_id_mesa = :mesa_id_mesa");

        $buscarConvidado->execute([
            ':mesa_id_mesa' => $mesaId
        ]);

        $convidado = $buscarConvidado->fetchAll();

        if (empty($convidado)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Convidado não encontrado pelo id da mesa',
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
        $query = $this->convidadoDb->query("SELECT * FROM convidado");
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
            $convidadoDados['cpf'] = substr($convidadoDados['telefone'], 0, 45);

            if (empty($convidadoDados['mesa_id_mesa'])) {
                $convidadoDados['mesa_id_mesa'] = null;
            }

            $convidadosComReferenciasMesas = $this->buscarConvidadosPorMesaId($convidadoDados['mesa_id_mesa']);
            $mesa = new MesaService();
            $mesaComAReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_id_mesa']);

            if (count($convidadosComReferenciasMesas['dados']) >= $mesaComAReferencia['dados']['capacidade'] && $convidadosComReferenciasMesas['dados']['confirmacao'] === 'confirmado') {
                throw new Exception('Mesa lotada', 409);
            }

            $criarConvidado = $this->convidadoDb->prepare('INSERT INTO convidado (nome, sobrenome, email, cpf, telefone, categoria, confirmacao, mesa_id_mesa)
        VALUES (:nome, :sobrenome, :email, :cpf, :telefone, :categoria, :confirmacao, :mesa_id_mesa)');

            $criarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':telefone' => $convidadoDados['telefone'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':mesa_id_mesa' => $convidadoDados['mesa_id_mesa'],

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
                throw new Exception('Cpf já em uso', 409);
            }

            if(str_contains($e->getMessage(), 'fk_convidado_mesa')){
                throw new Exception('Mesa referenciada não encontrada', 404);
            }

            throw new Exception('Erro ao criar convidado' . $e->getMessage(), 500);
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
            $convidadoDados['cpf'] = substr($convidadoDados['telefone'], 0, 45);

            if (empty($convidadoDados['mesa_id_mesa'])) {
                $convidadoDados['mesa_id_mesa'] = null;
            }

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            $convidadosComReferenciasMesas = $this->buscarConvidadosPorMesaId($convidadoDados['mesa_id_mesa']);
            $mesa = new MesaService();
            $mesaComAReferencia = $mesa->buscarMesaPorId($convidadoDados['mesa_id_mesa']);

            if (count($convidadosComReferenciasMesas['dados']) >= $mesaComAReferencia['dados']['capacidade'] && $convidadosComReferenciasMesas['dados']['confirmacao'] === "confirmado") {
                throw new Exception('Mesa lotada', 409);
            }

            $atualizarConvidado = $this->convidadoDb->prepare("UPDATE convidado SET nome = :nome, sobrenome = :sobrenome, 
            email = :email, cpf = :cpf, telefone = :telefone, confirmacao = :confirmacao, categoria = :categoria,
            mesa_id_mesa = :mesa_id_mesa WHERE email = :email_convidado");

            $atualizarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':telefone' => $convidadoDados['telefone'],
                ':cpf' => $convidadoDados['cpf'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':mesa_id_mesa' => $convidadoDados['mesa_id_mesa'],
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
                throw new Exception('Cpf já em uso', 409);
            }

            if(str_contains($e->getMessage(), 'fk_convidado_mesa')){
                throw new Exception('Mesa referenciada não encontrada', 404);
            }

            throw new Exception('Erro ao atualizar usuário' . $e->getMessage(), 500);
        }
    }

    public function deletarConvidado($emailConvidado)
    {
        try {

            if (empty($emailConvidado)) {
                throw new Exception('Dados inválidos', 400);
            }

            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }

            $deletarConvidado = $this->convidadoDb->prepare("DELETE FROM convidado WHERE email = :email_convidado");

            $deletarConvidado->execute([

                ':email_convidado' => $emailConvidado
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Convidado deletado com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar convidado' . $e->getMessage(), 500);
        }
    }
}
