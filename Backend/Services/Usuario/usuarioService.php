<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/connection.php";

class UsuarioService
{
    protected $db;

    public function __construct()
    {
        $this->db = dbConnection();
    }

    public function buscarUsuarioPorEmail($emailUsuario)
    {
        if (empty($emailUsuario)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscar = $this->db->prepare('SELECT * FROM usuario WHERE email = :email');

        $buscar->execute([
            ':email' => $emailUsuario
        ]);

        $usuario = $buscar->fetch();

        if (empty($usuario)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Usuário não encontrado',
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $usuario
           
        ];
    }

    public function listarUsuarios()
    {
        $query = $this->db->query("SELECT id_usuario, nome, email, cpf, cargo FROM usuario ORDER BY id_usuario DESC");

        $usuarios = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $usuarios,
            'total' => count($usuarios)
        ];
    }


    public function criarUsuario($usuarioDados)
    {
        try {
            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $criar = $this->db->prepare('INSERT INTO usuario (nome, email, senha, cpf, cargo)
            VALUES (:nome, :email, :senha, :cpf, :cargo)');

            $criar->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo']
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Usuário criado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            throw new Exception('Erro ao criar usuário', 500);
        }
    }


    public function fazerLogin($usuarioDados, $chaveSecreta)
    {
        try {
            $usuario = $this->buscarUsuarioPorEmail($usuarioDados['email']);

            if ($usuario['sucesso'] === false) {
                throw new Exception('Credenciais inválidas', 401);
            }

            $senhaCorreta = password_verify($usuarioDados['senha'], $usuario['dados']['senha']);

            if (!$senhaCorreta) {
                throw new Exception('Credenciais inválidas', 401);
            }

            $payload = [
                'exp' => time() + 10000,
                'dados' => [
                    'id_usuario' => $usuario['dados']['id_usuario'],
                    'cargo_usuario' => $usuario['dados']['cargo']
                ]
            ];

            $jwt = JWT::encode($payload, $chaveSecreta, 'HS256');

            return [
                'sucesso' => true,
                'mensagem' => 'Usuário logado com sucesso',
                'token' => $jwt
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar fazer login', 500);
        }
    }


    public function atualizarUsuario($usuarioDados, $emailUsuario)
    {
        try {

            if (empty($emailUsuario)) {
                throw new Exception('Dados inválidos', 400);
            }

            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }

            $atualizar = $this->db->prepare('UPDATE usuario set nome = :nome, email = :email, senha = :senha, cpf = :cpf, cargo = :cargo WHERE email = :email_antigo');

            $atualizar->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo'],
                ':email_antigo' => $emailUsuario
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Usuário atualizado com sucesso'
            ];
        } catch (PDOException $e) {
            if (str_contains($e->getMessage(), 'email')) {
                throw new Exception('Email já em uso', 409);
            }
            if (str_contains($e->getMessage(), 'cpf')) {
                throw new Exception('CPF já em uso', 409);
            }

            throw new Exception('Erro ao atualizar usuário', 500);
        }
    }

    public function deletarUsuario($emailUsuario)
    {
        try {

            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }


            $deletar = $this->db->prepare('DELETE FROM usuario WHERE email = :email');

            $deletar->execute([
                ':email' => $emailUsuario
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Usuário deletado com sucesso'
            ];
        } catch (PDOException $e) {
            if(str_contains($e->getMessage(), 'parent row')){
                throw new Exception('Impossível deletar um usuário referenciado', 409);
            }

            throw new Exception('Erro ao deletar usuário', 500);
        }
    }
}
