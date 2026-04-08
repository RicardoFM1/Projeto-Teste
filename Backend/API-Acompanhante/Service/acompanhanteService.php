<?php


require_once __DIR__ . "/../Connection/acompanhanteConnection.php";
require_once __DIR__ . "/../Validator/acompanhanteValidador.php";

class AcompanhanteService
{
    protected $acompanhanteDb;

    public function __construct()
    {
        $this->acompanhanteDb = dbAcompanhanteConnection();
    }

    public function listarAcompanhantes()
    {
        $stmt = $this->acompanhanteDb->query("SELECT * FROM acompanhante");
        $acompanhantes = $stmt->fetchAll();
        return [
            'sucesso' => true,
            'dados' => $acompanhantes
        ];
    }

    public function buscarAcompanhantePorId($idAcompanhante)
    {
        if (empty($idAcompanhante)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Id do acompanhante não informado',
                'codigo' => 400
            ];
        }

        $acharAcompanhante = $this->acompanhanteDb->prepare("SELECT id_acompanhante FROM acompanhante WHERE id_acompanhante = :id_acompanhante");
        $acharAcompanhante->execute([':id_acompanhante' => $idAcompanhante]);
        $acompanhante = $acharAcompanhante->fetch();

        if (empty($acompanhante)) {

            return [
                'sucesso' => false,
                'mensagem' => "Acompanhante não encontrado pelo ID",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $acompanhante
        ];
    }


    public function buscarAcompanhantePorCPF($cpfAcompanhante)
    {
        if (empty($cpfAcompanhante)) {

            return [
                'sucesso' => false,
                'mensagem' => 'CPF do acompanhante não informado',
                'codigo' => 400
            ];
        }

        $acharAcompanhanteCPF = $this->acompanhanteDb->prepare("SELECT id_acomphante FROM acompanhante WHERE cpf = :cpf");
        $acharAcompanhanteCPF->execute([':cpf' => $cpfAcompanhante]);
        $acompanhante = $acharAcompanhanteCPF->fetch();

        if (empty($conacompanhantevidado)) {

            return [
                'sucesso' => false,
                'mensagem' => "Acompanhante não encontrado pelo CPF",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $acompanhante
        ];
    }


    public function criarAcompanhante($acompanhanteDados)
    {

        AcompanhanteValidador::validarAcompanhante($acompanhanteDados);
        // formatar cpf
        $acompanhanteDados['cpf'] = str_replace([' ', '.', '-'], '', $acompanhanteDados['cpf']);
        // formatar telefone (trocar por regex)
        $acompanhanteDados['telefone'] = str_replace(
            [' ', '.', '-', '(', ')', '[', ']', '!', '@', '#', '$', '%', '¨', '&', '*', '_', '-', '=', '§', '', '+']
            , '', $acompanhanteDados['telefone']);


        if ($this->buscarAcompanhantePorCPF($acompanhanteDados['cpf'])['sucesso']) {
            throw new Exception("Este CPF já está cadastrado", 409);
        }

  

        $stmt = $this->acompanhanteDb->prepare("INSERT INTO acompanhante(nome, sobrenome, cpf, telefone, convidado_idconvidado)
        VALUES (:nome, :sobrenome, :cpf, :telefone, :convidado_idconvidado)");

        $stmt->execute([
            ':nome' => $acompanhanteDados['nome'],
            ':sobrenome' => $acompanhanteDados['sobrenome'],
            ':cpf' => $acompanhanteDados['cpf'],
            ':telefone' => $acompanhanteDados['telefone'],
            ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado']

        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Acompanhante criado com sucesso'
        ];
    }


    public function atualizarAcompanhante($acompanhanteDados, $idAcompanhante, $tokenJWT)
    {

        AcompanhanteValidador::validarAcompanhante($acompanhanteDados);
        // formatar cpf
        $acompanhanteDados['cpf'] = str_replace([' ', '.', '-'], '', $acompanhanteDados['cpf']);
        // formatar telefone
        $acompanhanteDados['telefone'] = str_replace([' ', '.', '-', '(', ')', '+'], '', $acompanhanteDados['telefone']);


        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }

         $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

        if (isset($acompanhante['sucesso']) && $acompanhante['sucesso'] === false) {
            throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
        }


        $atualizarAcompanhante = $this->acompanhanteDb->prepare("UPDATE acompanhante SET nome = :nome, sobrenome = :sobrenome,
        cpf = :cpf, telefone = :telefone, convidado_idconvidado = :convidado_idconvidado WHERE id_acompanhante = :id_acompanhante");

        $atualizarAcompanhante->execute([
            ':nome' => $acompanhanteDados['nome'],
            ':sobrenome' => $acompanhanteDados['sobrenome'],
            ':cpf' => $acompanhanteDados['cpf'],
            ':telefone' => $acompanhanteDados['telefone'],
            ':convidado_idconvidado' => $acompanhanteDados['convidado_idconvidado'],
            ':id_acompanhante' => $idAcompanhante
        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Acompanhante atualizado com sucesso'
        ];
    }

    public function deletarAcompanhante($idAcompanhante, $tokenJWT)
    {

        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }
       
        $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

        if (isset($acompanhante['sucesso']) && $acompanhante['sucesso'] === false) {
            throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
        }

        $deletarAcompanhante = $this->acompanhanteDb->prepare("DELETE FROM acompanhante WHERE id_acompanhante = :id_acompanhante");
        $deletarAcompanhante->execute([':id_acompanhante' => $idAcompanhante]);


        return [
            'sucesso' => true,
            'mensagem' => 'Acompanhante deletado com sucesso'
        ];
    }
}
