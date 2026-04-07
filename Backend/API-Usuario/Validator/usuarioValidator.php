<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class UsuarioValidator
{
    public static function validarDadosUsuario($usuarioDados)
    {

        $cargosPermitidos = ['admin', 'ceremonialista'];

        $esquema = v::key('nome', v::stringVal()->length(3, 50)->notEmpty())
            ->key('email', v::email())
            ->key('senha', v::stringVal()->length(8, 100)->notEmpty()->regex('/\d/')->regex('/[!@#$%¨&*()]/'))
            ->key('cargo', v::in($cargosPermitidos))
            ->key('cpf', v::cpf());

        try {

            $esquema->assert($usuarioDados);

        } catch (NestedValidationException $e) {


            $mensagensPersonalizadas =
                [
                    'senha' => 'O campo deve conter pelo menos um caractere especial',
                    'nome' => 'O campo deve ser válido e presente',
                    'email' => 'O email deve ser um email válido',
                    'cpf' => 'O campo CPF deve ser um CPF válido',
                    'cargo' => 'O campo cargo deve ser apenas Admin ou Ceremonialistas'
                ];
            $mensagensErrosOriginais = $e->getMessages();
            $errosFormatados = [];

            foreach ($mensagensErrosOriginais as $campo => $mensagem) {
                $errosFormatados[$campo] = $mensagensPersonalizadas[$campo] ?? $mensagem;
            }

            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'message' => 'Erro de validação',
                'erros' => $errosFormatados
            ]);
            exit;
        }
    }
}
