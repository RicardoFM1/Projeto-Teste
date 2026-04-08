<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class ConvidadoValidador
{
    public static function validarConvidado($convidadoDados)
    {

       
        $convidadoDados['cpf'] = str_replace([' ', '.', '-'], '', $convidadoDados['cpf']);
        $convidadoDados['telefone'] = str_replace([' ', '.', '-', '(', ')', '+'], '', $convidadoDados['telefone']);


        $esquema = v::key('nome', v::stringVal()->length(5, 50)->notEmpty())
            ->key('sobrenome', v::stringVal()->length(5, 50)->notEmpty())
            ->key('email', v::email())
            ->key('cpf', v::cpf())
            ->key('telefone', v::phone())
            ->key('numero_mesa', v::intVal());

        try {
            
            $esquema->assert($convidadoDados);

        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, mínimo 5 caracteres e máximo 50',
                'sobrenome' =>  'Sobrenome inválido, mínimo 5 caracteres e máximo 50',
                'email' => 'Email inválido',
                'cpf' => 'CPF inválido',
                'telefone' => 'Telefone inválido',
                'numero_mesa' => 'Número de mesa precisa ser apenas número'
            ];
            $mensagemOriginal = $e->getMessages();
            $mensagensTraduzidas = [];

            foreach($mensagemOriginal as $campo => $mensagem){
                $mensagensTraduzidas[$campo] = $mensagemPersonalizada[$campo] ?? $mensagem;
            }

            http_response_code(400);
            echo json_encode([
                'sucesso' => false,
                'mensagem' => 'Erro de validação',
                'erros' => $mensagensTraduzidas
            ]);
            exit;
        }
    }
}
