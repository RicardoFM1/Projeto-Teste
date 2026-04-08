<?php


use Firebase\JWT\JWT;

require_once __DIR__ . "/../Connection/usuarioConnection.php";
require_once __DIR__ . "/../Validator/usuarioValidator.php";

class UsuarioService
{
    protected $usuarioDb;

    public function __construct()
    {
        $this->usuarioDb = dbUsuarioConnection();
    }

    public function listarUsuarios()
    {
        $stmt = $this->usuarioDb->query("SELECT * FROM usuario");
        $usuarios = $stmt->fetchAll();
        return [
            'sucesso' => true,
            'dados' => $usuarios
        ];
    }

    public function buscarUsuarioPorId($idUsuario)
    {
        if (empty($idUsuario)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Id do usuário não informado',
                'codigo' => 400
            ];
        }

        $acharUsuario = $this->usuarioDb->prepare("SELECT id_usuario FROM usuario WHERE id_usuario = :id_usuario");
        $acharUsuario->execute([':id_usuario' => $idUsuario]);
        $usuario = $acharUsuario->fetch();

        if (empty($usuario)) {

            return [
                'sucesso' => false,
                'mensagem' => "Usuário não encontrado",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $usuario
        ];
    }

    public function buscarUsuarioPorEmail($emailUsuario)
    {
        if (empty($emailUsuario)) {

            return [
                'sucesso' => false,
                'mensagem' => 'Email do usuário não informado',
                'codigo' => 400
            ];
        }

        $acharUsuarioEmail = $this->usuarioDb->prepare("SELECT id_usuario, senha, cargo FROM usuario WHERE email = :email");
        $acharUsuarioEmail->execute([':email' => $emailUsuario]);
        $usuario = $acharUsuarioEmail->fetch();

        if (empty($usuario)) {

            return [
                'sucesso' => false,
                'mensagem' => "Usuário não encontrado",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $usuario
        ];
    }

    public function buscarUsuarioPorCPF($cpfUsuario)
    {
        if (empty($cpfUsuario)) {

            return [
                'sucesso' => false,
                'mensagem' => 'CPF do usuário não informado',
                'codigo' => 400
            ];
        }

        $acharUsuarioCPF = $this->usuarioDb->prepare("SELECT id_usuario FROM usuario WHERE cpf = :cpf");
        $acharUsuarioCPF->execute([':cpf' => $cpfUsuario]);
        $usuario = $acharUsuarioCPF->fetch();

        if (empty($usuario)) {

            return [
                'sucesso' => false,
                'mensagem' => "Usuário não encontrado",
                'codigo' => 404
            ];
        }

        return [
            'sucesso' => true,
            'dados' => $usuario
        ];
    }


    public function criarUsuario($usuarioDados)
    {
        
        UsuarioValidador::validarUsuario($usuarioDados);
        // formatar cpf
        $usuarioDados['cpf'] = str_replace([' ', '.', '-'], '', $usuarioDados['cpf']);

        if ($this->buscarUsuarioPorCpf($usuarioDados['cpf'])['sucesso']) {
            throw new Exception("Este CPF já está cadastrado", 409);
        }

        if ($this->buscarUsuarioPorEmail($usuarioDados['email'])['sucesso']) {
            throw new Exception("Este Email já está cadastrado", 409);
        }




        $stmt = $this->usuarioDb->prepare("INSERT INTO usuario(nome, email, senha, cargo, cpf)
        VALUES (:nome, :email, :senha, :cargo, :cpf)");

        $stmt->execute([
            ':nome' => $usuarioDados['nome'],
            ':email' => $usuarioDados['email'],
            ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
            ':cargo' => $usuarioDados['cargo'],
            ':cpf' => $usuarioDados['cpf']
        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Usuario criado com sucesso'
        ];
    }

    public function fazerLogin($usuarioDados)
    {
        if (empty($usuarioDados)) {
            throw new Exception("Os campos devem ser preenchidos", 400);
        }

        $usuario = $this->buscarUsuarioPorEmail($usuarioDados['email']);

        if (isset($usuario) && $usuario['sucesso'] !== true ) {
            throw new Exception("Credenciais inválidas", 401);
        }

        $verificarSenha = password_verify($usuarioDados['senha'], $usuario['dados']['senha']);
        if (!$verificarSenha) {
            throw new Exception("Credenciais inválidas", 401);
        }

        $JWTSecretKey = $_ENV['JWT_SECRET_KEY'];

        $payload = [
            'exp' => time() + 3600,
            'dados' => [
                'id_usuario' => $usuario['dados']['id_usuario'],
                'cargo' => $usuario['dados']['cargo']
            ]
        ];
        $jwt = JWT::encode($payload, $JWTSecretKey, 'HS256');

        
        return [
            'sucesso' => true,
            'mensagem' => 'Usuario logado com sucesso',
            'token' => $jwt
        ];
    }




    public function atualizarUsuario($usuarioDados, $idUsuario, $tokenJWT)
    {

        UsuarioValidador::validarUsuario($usuarioDados);
        // formatar cpf
        $usuarioDados['cpf'] = str_replace([' ', '.', '-'], '', $usuarioDados['cpf']);

        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }

        if ($tokenJWT->dados->id_usuario !== $idUsuario) {
            throw new Exception("Sem permissão para atualizar esse usuário", 401);
        }

        $usuario = $this->buscarUsuarioPorId($idUsuario);

        if (isset($usuario['sucesso']) && $usuario['sucesso'] === false) {
            throw new Exception($usuario['mensagem'], $usuario['codigo']);
        }

        $atualizarUsuario = $this->usuarioDb->prepare("UPDATE usuarios SET nome = :nome, email = :email,
        senha = :senha, cargo = :cargo, cpf = :cpf WHERE id_usuario = :id_usuario");

        $atualizarUsuario->execute([
            ':nome' => $usuarioDados['nome'],
            ':email' => $usuarioDados['email'],
            ':senha' => password_hash($usuarioDados['senha'], PASSWORD_DEFAULT),
            ':cargo' => $usuarioDados['cargo'],
            ':cpf' => $usuarioDados['cpf'],
            ':id_usuario' => $idUsuario
        ]);


        return [
            'sucesso' => true,
            'mensagem' => 'Usuario atualizado com sucesso'
        ];
    }

    public function deletarUsuario($idUsuario, $tokenJWT)
    {

        if (empty($tokenJWT)) {
            throw new Exception("Token JWT não informado", 400);
        }

        if ($tokenJWT->dados->id_usuario !== $idUsuario) {
            throw new Exception("Sem permissão para deletar esse usuário", 401);
        }


        $usuario = $this->buscarUsuarioPorId($idUsuario);

        if (isset($usuario['sucesso']) && $usuario['sucesso'] === false) {
            throw new Exception($usuario['mensagem'], $usuario['codigo']);
        }

        $deletarUsuario = $this->usuarioDb->prepare("DELETE FROM usuario WHERE id_usuario = :id_usuario");
        $deletarUsuario->execute([':id_usuario' => $idUsuario]);


        return [
            'sucesso' => true,
            'mensagem' => 'Usuario deletado com sucesso'
        ];
    }
}
