<?php

use Firebase\JWT\JWT;


require_once __DIR__ . "/../../Connection/connection.php";

class UsuarioService
{
    private $Db;


    public function __construct()
    {
        $this->Db = dbConnection();
    }

    public function buscarUsuarioPorEmail($emailUsuario)
    {
        if (empty($emailUsuario)) {
            throw new Exception('Email do usuário não fornecido', 400);
        }

        $buscarUsuario = $this->Db->prepare("SELECT * FROM usuario WHERE email = :email");
        $buscarUsuario->execute([
            ':email' => $emailUsuario
        ]);

        $usuario = $buscarUsuario->fetch();

        if (empty($usuario)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Usuário não encontrado pelo email',
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
        $query = $this->Db->query("SELECT nome, email, cpf, cargo FROM usuario");
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

            $criarUsuario = $this->Db->prepare("INSERT INTO usuario (nome, email, senha, cpf, cargo)
            VALUES (:nome, :email, :senha, :cpf, :cargo)");

            $criarUsuario->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo'],

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

            throw new Exception('Erro ao criar usuário', 500);
        }
    }


    public function fazerLogin($usuarioDados, $chaveSecreta)
    {
        $usuario = $this->buscarUsuarioPorEmail($usuarioDados['email']);

        if ($usuario['sucesso'] === false) {
            throw new Exception('Credenciais inválidas', 401);
        }

        $senhaCorreta = password_verify($usuarioDados['senha'], $usuario['dados']['senha']);

        if (!$senhaCorreta) {
            throw new Exception('Credenciais inválidas', 401);
        }

        $payload = [
            'exp' => time() + 3600,
            'dados' => [
                'id_usuario' => $usuario['dados']['id_usuario'],
                'cargo_usuario' => $usuario['dados']['cargo'],

            ]
        ];

        $jwt = JWT::encode($payload, $chaveSecreta, 'HS256');

        return [
            'sucesso' => true,
            'mensagem' => 'Usuário logado com sucesso',
            'token' => $jwt
        ];
    }


    public function atualizarUsuario($usuarioDados, $emailUsuario)
    {
        try {

            if (empty($emailUsuario)) {
                throw new Exception('Email do usuário não fornecido', 400);
            }

            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }


            // Isso não precisa existir em usuário, apenas em CHECKIN
            // if ($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $usuario['dados']['id_usuario']) {
            //     throw new Exception('Sem permissão para atualizar esse usuário', 403);
            // }

            $atualizarUsuario = $this->Db->prepare("UPDATE usuario SET nome = :nome, email = :email,
            senha = :senha, cpf = :cpf, cargo = :cargo WHERE email = :email_antigo");

            $atualizarUsuario->execute([
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
                throw new Exception('Cpf já em uso', 409);
            }

            throw new Exception('Erro ao criar usuário', 500);
        }
    }

    public function deletarUsuario($emailUsuario)
    {
        if (empty($emailUsuario)) {
            throw new Exception('Email do usuário não fornecido', 400);
        }


        $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

        if ($usuario['sucesso'] === false) {
            throw new Exception($usuario['mensagem'], $usuario['codigo']);
        }



        // Isso não precisa existir em usuário, apenas em CHECKIN
        // if ($tokenJWT->dados->cargo_usuario !== "admin" && $tokenJWT->dados->id_usuario !== $usuario['dados']['id_usuario']) {
        //     throw new Exception('Sem permissão para atualizar esse usuário', 403);
        // }

        $deletarUsuario = $this->Db->prepare('DELETE FROM usuario WHERE email = :email_antigo');
        $deletarUsuario->execute([
            ':email_antigo' => $emailUsuario
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Usuário deletado com sucesso'
        ];
    }
}
