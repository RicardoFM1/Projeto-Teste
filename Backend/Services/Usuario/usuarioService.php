<?php

use Firebase\JWT\JWT;

require_once __DIR__ . "/../../Connection/db.php";

class UsuarioService
{
    protected $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function buscarUsuarioPorEmail($emailUsuario)
    {
        try {
            if (empty($emailUsuario)) {
                throw new Exception('Email não enviado', 400);
            }

            $buscar = $this->db->prepare('SELECT * FROM usuario WHERE email = :email');
            $buscar->execute([
                ':email' => $emailUsuario
            ]);

            $usuario = $buscar->fetch();

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
        } catch (PDOException $e) {
            throw new Exception('Erro ao buscar usuário por email', 500);
        }
    }

    public function listarUsuarios()
    {
        try {

            $query = $this->db->query("SELECT nome, email, cpf, cargo FROM usuario");

            $usuarios = $query->fetchAll();

            return [
                'sucesso' => true,
                'dados' => $usuarios
            ];
        } catch (PDOException $e) {
            throw new Exception('Erro ao tentar listar usuário', 500);
        }
    }

    public function criarUsuario($usuarioDados)
    {
        try {

            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $criar = $this->db->prepare('INSERT INTO usuario (nome, email, cpf, cargo, senha)
        VALUES(:nome, :email, :cpf, :cargo, :senha)');

            $criar->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT)
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

            throw new Exception('Erro ao tentar criar usuário', 500);
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
                    'email_usuario' => $usuario['dados']['email'],
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
            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }

            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $atualizar = $this->db->prepare('UPDATE usuario SET nome = :nome, email = :email, cpf = :cpf,
            cargo = :cargo, senha = :senha WHERE email = :email_usuario');

            $atualizar->execute([
                ':nome' => $usuarioDados['nome'],
                ':email' => $usuarioDados['email'],
                ':cpf' => $usuarioDados['cpf'],
                ':cargo' => $usuarioDados['cargo'],
                ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
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
                throw new Exception('CPF já em uso', 409);
            }

            throw new Exception('Erro ao tentar atualizar usuário', 500);
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
            if (str_contains($e->getMessage(), 'parent row')) {
                throw new Exception('Impossível deletar um usuário referenciado', 409);
            }
            throw new Exception('Erro ao tentar deletar usuário', 500);
        }
    }
}
