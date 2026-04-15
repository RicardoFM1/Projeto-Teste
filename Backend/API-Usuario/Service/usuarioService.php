<?php

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . "/../Connection/usuarioConnection.php";

class UsuarioService
{
    private $usuarioDb;
    private $chaveSecreta;

    public function __construct()
    {
        $this->usuarioDb = dbUsuarioConnection();
        $this->chaveSecreta = $_ENV['JWT_SECRET_KEY'];
    }

    public function buscarUsuarioPorEmail($emailUsuario)
    {
        if (empty($emailUsuario)) {
            throw new Exception('Email do usuário não fornecido', 400);
        }

        $buscarUsuario = $this->usuarioDb->prepare("SELECT * FROM usuario WHERE email = :email");
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

    public function validarToken($tokenJWT)
    {
        if (empty($tokenJWT)) {
            throw new Exception('Usuário não autenticado', 401);
        }

        try {

            $partesToken = explode(' ', $tokenJWT);

            if (count($partesToken) !== 2) {
                throw new Exception('Formato de token inválido, aceito apenas: Bearer {token}', 401);
            }

            return JWT::decode($partesToken[1], new Key($this->chaveSecreta, 'HS256'));
        } catch (ExpiredException $e) {
            throw new Exception('Token expirado', 401);
        }
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

            $criarUsuario = $this->usuarioDb->prepare("INSERT INTO usuario (nome, email, senha, cpf, cargo)
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


    public function fazerLogin($usuarioDados)
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

        $jwt = JWT::encode($payload, $this->chaveSecreta, 'HS256');

        return [
            'sucesso' => true,
            'mensagem' => 'Usuário logado com sucesso',
            'token' => $jwt
        ];
    }


    public function atualizarUsuario($usuarioDados, $emailUsuario, $tokenJWT)
    {
        try {

            if (empty($emailUsuario)) {
                throw new Exception('Email do usuário não fornecido', 400);
            }

            if (empty($tokenJWT)) {
                throw new Exception('Usuário não autenticado', 401);
            }

            $usuarioDados['cpf'] = preg_replace('/\D/', '', $usuarioDados['cpf']);

            $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

            if ($usuario['sucesso'] === false) {
                throw new Exception($usuario['mensagem'], $usuario['codigo']);
            }

            $jwt = $this->validarToken($tokenJWT);

            if ($jwt->dados->cargo_usuario !== "admin" && $jwt->dados->id_usuario !== $usuario['dados']['id_usuario']) {
                throw new Exception('Sem permissão para atualizar esse usuário', 403);
            }

            $atualizarUsuario = $this->usuarioDb->prepare("UPDATE usuario SET nome = :nome, email = :email,
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

    public function deletarUsuario ($emailUsuario, $tokenJWT){
        if (empty($emailUsuario)) {
                throw new Exception('Email do usuário não fornecido', 400);
            }

            if (empty($tokenJWT)) {
                throw new Exception('Usuário não autenticado', 401);
            }

        $usuario = $this->buscarUsuarioPorEmail($emailUsuario);

        if($usuario['sucesso'] === false){
            throw new Exception($usuario['mensagem'], $usuario['codigo']);
        }

        $jwt = $this->validarToken($tokenJWT);

        if ($jwt->dados->cargo_usuario !== "admin" && $jwt->dados->id_usuario !== $usuario['dados']['id_usuario']) {
                throw new Exception('Sem permissão para deletar esse usuário', 403);
            }

        $deletarUsuario = $this->usuarioDb->prepare('DELETE FROM usuario WHERE email = :email_antigo');
        $deletarUsuario->execute([
            ':email_antigo' => $emailUsuario
        ]);

        return [
            'sucesso' => true,
            'mensagem' => 'Usuário deletado com sucesso'
        ];
    }
}
