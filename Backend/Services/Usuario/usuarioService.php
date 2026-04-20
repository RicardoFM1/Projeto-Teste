<?php

use Firebase\JWT\JWT;
require_once __DIR__ . "/../../Connection/connection.php";

class UsuarioService
{
    private $usuarioDb;

    public function __construct()
    {
        $this->usuarioDb = dbConnection();
    }


    public function buscarUsuarioPorEmail($emailUsuario)
    {
        if (empty($emailUsuario)) {
            throw new Exception('Dados inválidos', 400);
        }

        $buscarUsuario = $this->usuarioDb->prepare("SELECT * FROM usuario WHERE email = :email");

        $buscarUsuario->execute([
            ':email' => $emailUsuario
        ]);

        $usuario = $buscarUsuario->fetch();

        if (empty($usuario)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Usuário não encontrado pelo id',
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
        $query = $this->usuarioDb->query("SELECT nome, email, cpf, cargo FROM usuario");
        $usuarios = $query->fetchAll();

        return [
            'sucesso' => true,
            'dados' => $usuarios
        ];
    }

    public function criarUsuario($usuarioDados)
    {
        try {
            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $criarUsuario = $this->usuarioDb->prepare('INSERT INTO usuario (nome, email, senha, cpf, cargo)
        VALUES (:nome, :email, :senha, :cpf, :cargo)');

            $criarUsuario->execute([
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
                throw new Exception('Cpf já em uso', 409);
            }

            throw new Exception('Erro ao criar usuário' . $e->getMessage(), 500);
        }
    }


    public function fazerLogin($usuarioDados, $chaveSecreta)
    {

        $usuario = $this->buscarUsuarioPorEmail($usuarioDados['email']);

        if ($usuario['sucesso'] === false) {
            throw new Exception('Credenciais inválidas', 401);
        }

        $senhaValida = password_verify($usuarioDados['senha'], $usuario['dados']['senha']);

        if (!$senhaValida) {
            throw new Exception('Credenciais inválidas', 401);
        }

        $payload = [
            'exp' => time() + 3600,
            'dados' => [
                'id_usuario' => $usuario['dados']['id_usuario'],
                'cargo_usuario' => $usuario['dados']['cargo']
            ]
        ];

        $jwt = JWT::encode($payload, $chaveSecreta, 'HS256');

        return [
            'sucesso' => true,
            'mensagem' => 'Login realizado com sucesso',
            'token' => $jwt
        ];
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

            $atualizarUsuario = $this->usuarioDb->prepare("UPDATE usuario SET nome = :nome, email = :email,
            senha = :senha, cpf = :cpf, cargo = :cargo WHERE email = :email_usuario");

            $atualizarUsuario->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo'],
                ':email_usuario' => $emailUsuario
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
                throw new Exception('Cpf já em uso', 409);
            }

            throw new Exception('Erro ao atualizar usuário' . $e->getMessage(), 500);
        }
    }

    public function deletarUsuario($emailUsuario)
    {
        try {

            if (empty($emailUsuario)) {
                throw new Exception('Dados inválidos', 400);
            }

            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }

            $deletarUsuario = $this->usuarioDb->prepare("DELETE FROM usuario WHERE email = :email_usuario");

            $deletarUsuario->execute([

                ':email_usuario' => $emailUsuario
            ]);

            return [
                'sucesso' => true,
                'mensagem' => 'Usuário deletado com sucesso'
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao deletar usuário' . $e->getMessage(), 500);
        }
    }
}
