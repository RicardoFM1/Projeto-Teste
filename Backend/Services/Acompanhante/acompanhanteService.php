<?php



require_once __DIR__ . "/../../Connection/connection.php";

class AcompanhanteService
{
    private $Db;


    public function __construct()
    {
        $this->Db = dbConnection();
    }

    public function buscarAcompanhantePorId($idAcompanhante)
    {
        if (empty($idAcompanhante)) {
            throw new Exception('Id do acompanhante não fornecido', 400);
        }

        $buscarAcompanhante = $this->Db->prepare("SELECT * FROM acompanhante WHERE id_acompanhante = :id_acompanhante");
        $buscarAcompanhante->execute([
            'id_acompanhante' => $idAcompanhante
        ]);

        $acompanhante = $buscarAcompanhante->fetch();

        if (empty($acompanhante)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Acompanhante não encontrado pelo id',
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
        $query = $this->Db->query("SELECT * FROM acompanhante");
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


            $criarAcompanhante = $this->Db->prepare("INSERT INTO acompanhante (nome, sobrenome, cpf, idade, convidado_id_convidado)
            VALUES (:nome, :sobrenome, :cpf, :idade, :convidado_id_convidado)");

            $criarAcompanhante->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],
                ':cpf' => $acompanhanteDados['cpf'],
                ':idade' => $acompanhanteDados['idade'],
                ':convidado_id_convidado' => $acompanhanteDados['convidado_id_convidado']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante criado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('Cpf já em uso', 409);
            }

            if(str_contains($e->getMessage(), 'fk_acompanhante_convidado')){
                throw new Exception('Convidado referenciado não encontrado', 404);
            }

            throw new Exception('Erro ao criar acompanhante', 500);
        }
    }




    public function atualizarAcompanhante($acompanhanteDados, $idAcompanhante)
    {
        try {

            if (empty($idAcompanhante)) {
                throw new Exception('Id do acompanhante não fornecido', 400);
            }



            $acompanhanteDados['cpf'] = preg_replace('/\D/', '', $acompanhanteDados['cpf']);


            $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

            if ($acompanhante['sucesso'] === false) {
                throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
            }


            $atualizarAcompanhante = $this->Db->prepare("UPDATE acompanhante SET nome = :nome, sobrenome = :sobrenome, idade = :idade,
            cpf = :cpf, convidado_id_convidado = :convidado_id_convidado WHERE id_acompanhante = :id_acompanhante");

            $atualizarAcompanhante->execute([
                ':nome' => $acompanhanteDados['nome'],
                ':sobrenome' => $acompanhanteDados['sobrenome'],

                ':cpf' => $acompanhanteDados['cpf'],


                ':convidado_id_convidado' => $acompanhanteDados['convidado_id_convidado'],
                ':idade' => $acompanhanteDados['idade'],
                ':id_acompanhante' => $idAcompanhante
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Acompanhante atualizado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('Cpf já em uso', 409);
            }

             if(str_contains($e->getMessage(), 'fk_acompanhante_convidado')){
                throw new Exception('Convidado referenciado não encontrado', 404);
            }

            throw new Exception('Erro ao criar acompanhante', 500);
        }
    }

    public function deletarAcompanhante($idAcompanhante)
    {
        if (empty($idAcompanhante)) {
            throw new Exception('Id do acompanhante não fornecido', 400);
        }



        $acompanhante = $this->buscarAcompanhantePorId($idAcompanhante);

        if ($acompanhante['sucesso'] === false) {
            throw new Exception($acompanhante['mensagem'], $acompanhante['codigo']);
        }

        $deletarAcompanhante = $this->Db->prepare('DELETE FROM acompanhante WHERE id_acompanhante = :id_acompanhante');
        $deletarAcompanhante->execute([
            ':id_acompanhante' => $idAcompanhante
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Acompanhante deletado com sucesso'
        ];
    }
}
