<?php


use Firebase\JWT\JWT;

require_once __DIR__ . "/../Connection/convidadoConnection.php";
require_once __DIR__ . "/../Validator/convidadoValidador.php";

class ConvidadoService
{
    protected $convidadoDb;

    public function __construct()
    {
        $this->convidadoDb = dbConvidadoConnection();
    }

    public function listarConvidados()
    {
        $stmt = $this->convidadoDb->query("SELECT * FROM convidado");
        $convidados = $stmt->fetchAll();
        return [
            'sucesso' => true,
            'dados' => $convidados
        ];
    }

    public function buscarConvidadoPorId($idConvidado)
    {
        if (empty($idConvidado)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Id do convidado não informado',
                'codigo' => 400
            ];
        }

        $acharConvidado = $this->convidadoDb->prepare("SELECT id_convidado FROM convidado WHERE id_convidado = :id_convidado");
        $acharConvidado->execute([':id_convidado' => $idConvidado]);
        $convidado = $acharConvidado->fetch();

        if (empty($convidado)) {

            return [
                'sucesso' => false,
                'mensagem' => "Convidado não encontrado pelo ID",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidado
        ];
    }

    public function buscarConvidadoPorEmail($emailConvidado)
    {
        if (empty($emailConvidado)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Email do convidado não informado',
                'codigo' => 400
            ];
        }

        $acharConvidadoEmail = $this->convidadoDb->prepare("SELECT * FROM convidado WHERE email = :email");
        $acharConvidadoEmail->execute([':email' => $emailConvidado]);
        $convidado = $acharConvidadoEmail->fetch();

        if (empty($convidado)) {

            return [
                'sucesso' => false,
                'mensagem' => "Convidado não encontrado pelo Email",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidado
        ];
    }

    public function buscarConvidadoPorCPF($cpfConvidado)
    {
        if (empty($cpfConvidado)) {

            return [
                'sucesso' => false,
                'mensagem' => 'CPF do convidado não informado',
                'codigo' => 400
            ];
        }

        $acharConvidadoCPF = $this->convidadoDb->prepare("SELECT id_convidado FROM convidado WHERE cpf = :cpf");
        $acharConvidadoCPF->execute([':cpf' => $cpfConvidado]);
        $convidado = $acharConvidadoCPF->fetch();

        if (empty($convidado)) {

            return [
                'sucesso' => false,
                'mensagem' => "Convidado não encontrado pelo CPF",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $convidado
        ];
    }


    public function criarConvidado($convidadoDados)
    {

        ConvidadoValidador::validarConvidado($convidadoDados);
        // formatar cpf
        $convidadoDados['cpf'] = str_replace([' ', '.', '-'], '', $convidadoDados['cpf']);
        // formatar telefone (troca por regex)
        $convidadoDados['telefone'] = str_replace(
            [' ', '.', '-', '(', ')', '[', ']', '!', '@', '#', '$', '%', '¨', '&', '*', '_', '-', '=', '§', '', '+']
            , '', $convidadoDados['telefone']);


        if ($this->buscarConvidadoPorCPF($convidadoDados['cpf'])['sucesso']) {
            throw new Exception("Este CPF já está cadastrado", 409);
        }

        if ($this->buscarConvidadoPorEmail($convidadoDados['email'])['sucesso']) {
            throw new Exception("Este Email já está cadastrado", 409);
        }


  

        $stmt = $this->convidadoDb->prepare("INSERT INTO convidado(nome, sobrenome, email, cpf, telefone, numero_mesa)
        VALUES (:nome, :sobrenome, :email, :cpf, :telefone, :numero_mesa)");

        $stmt->execute([
            ':nome' => $convidadoDados['nome'],
            ':sobrenome' => $convidadoDados['sobrenome'],
            ':email' => $convidadoDados['email'],
            ':cpf' => $convidadoDados['cpf'],
            ':telefone' => $convidadoDados['telefone'],
            ':numero_mesa' => $convidadoDados['numero_mesa']

        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Convidado criado com sucesso'
        ];
    }


    public function atualizarConvidado($convidadoDados, $idConvidado, $tokenJWT)
    {

        ConvidadoValidador::validarConvidado($convidadoDados);
        // formatar cpf
        $convidadoDados['cpf'] = str_replace([' ', '.', '-'], '', $convidadoDados['cpf']);
        // formatar telefone
        $convidadoDados['telefone'] = str_replace([' ', '.', '-', '(', ')', '+'], '', $convidadoDados['telefone']);


        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }

         $convidado = $this->buscarConvidadoPorId($idConvidado);

        if (isset($convidado['sucesso']) && $convidado['sucesso'] === false) {
            throw new Exception($convidado['mensagem'], $convidado['codigo']);
        }


        $atualizarConvidado = $this->convidadoDb->prepare("UPDATE convidado SET nome = :nome, sobrenome = :sobrenome, email = :email,
        cpf = :cpf, telefone = :telefone, numero_mesa = :numero_mesa WHERE id_convidado = :id_convidado");

        $atualizarConvidado->execute([
            ':nome' => $convidadoDados['nome'],
            ':sobrenome' => $convidadoDados['sobrenome'],
            ':email' => $convidadoDados['email'],
            ':cpf' => $convidadoDados['cpf'],
            ':telefone' => $convidadoDados['telefone'],
            ':numero_mesa' => $convidadoDados['numero_mesa'],
            ':id_convidado' => $idConvidado
        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Convidado atualizado com sucesso'
        ];
    }

    public function deletarConvidado($idConvidado, $tokenJWT)
    {

        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }
       
        $convidado = $this->buscarConvidadoPorId($idConvidado);

        if (isset($convidado['sucesso']) && $convidado['sucesso'] === false) {
            throw new Exception($convidado['mensagem'], $convidado['codigo']);
        }

        $deletarConvidado = $this->convidadoDb->prepare("DELETE FROM convidado WHERE id_convidado = :id_convidado");
        $deletarConvidado->execute([':id_convidado' => $idConvidado]);


        return [
            'sucesso' => true,
            'mensagem' => 'Convidado deletado com sucesso'
        ];
    }
}
