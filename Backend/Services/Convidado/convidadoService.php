<?php



require_once __DIR__ . "/../../Connection/connection.php";

class ConvidadoService
{
    private $Db;


    public function __construct()
    {
        $this->Db = dbConnection();
    }

    public function buscarConvidadoPorEmail($emailConvidado)
    {
        if (empty($emailConvidado)) {
            throw new Exception('Email do convidado não fornecido', 400);
        }

        $buscarConvidado = $this->Db->prepare("SELECT * FROM convidado WHERE email = :email");
        $buscarConvidado->execute([
            ':email' => $emailConvidado
        ]);

        $convidado = $buscarConvidado->fetch();

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
    }


    public function listarConvidados()
    {
        $query = $this->Db->query("SELECT * FROM convidado");
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

            $criarConvidado = $this->Db->prepare("INSERT INTO convidado (nome, sobrenome, email, cpf, telefone, categoria, confirmacao, mesa_id_mesa)
            VALUES (:nome, :sobrenome, :email, :cpf, :telefone, :categoria, :confirmacao, :mesa_id_mesa)");

            $criarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':telefone' => $convidadoDados['telefone'],
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

            throw new Exception('Erro ao criar convidado', 500);
        }
    }




    public function atualizarConvidado($convidadoDados, $emailConvidado)
    {
        try {

            if (empty($emailConvidado)) {
                throw new Exception('Email do convidado não fornecido', 400);
            }



            $convidadoDados['cpf'] = preg_replace('/\D/', '', $convidadoDados['cpf']);
            $convidadoDados['telefone'] = preg_replace('/\D/', '', $convidadoDados['telefone']);


            $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

            if ($convidado['sucesso'] === false) {
                throw new Exception($convidado['mensagem'], $convidado['codigo']);
            }


            $atualizarConvidado = $this->Db->prepare("UPDATE convidado SET nome = :nome, sobrenome = :sobrenome, email = :email,
            cpf = :cpf, telefone = :telefone, categoria = :categoria, confirmacao = :confirmacao, mesa_id_mesa = :mesa_id_mesa WHERE email = :email_antigo");

            $atualizarConvidado->execute([
                ':nome' => $convidadoDados['nome'],
                ':sobrenome' => $convidadoDados['sobrenome'],
                ':email' => $convidadoDados['email'],
                ':cpf' => $convidadoDados['cpf'],
                ':telefone' => $convidadoDados['telefone'],
                ':categoria' => $convidadoDados['categoria'],
                ':confirmacao' => $convidadoDados['confirmacao'],
                ':mesa_id_mesa' => $convidadoDados['mesa_id_mesa'],
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
                throw new Exception('Cpf já em uso', 409);
            }

            throw new Exception('Erro ao criar convidado', 500);
        }
    }

    public function deletarConvidado($emailConvidado)
    {
        if (empty($emailConvidado)) {
            throw new Exception('Email do convidado não fornecido', 400);
        }



        $convidado = $this->buscarConvidadoPorEmail($emailConvidado);

        if ($convidado['sucesso'] === false) {
            throw new Exception($convidado['mensagem'], $convidado['codigo']);
        }

        $deletarConvidado = $this->Db->prepare('DELETE FROM convidado WHERE email = :email_antigo');
        $deletarConvidado->execute([
            ':email_antigo' => $emailConvidado
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Convidado deletado com sucesso'
        ];
    }
}
