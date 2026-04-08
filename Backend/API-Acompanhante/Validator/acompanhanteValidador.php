<?php

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class AcompanhanteValidador
{
    public static function validarAcompanhante($acompanhanteDados)
    {

       
        $acompanhanteDados['cpf'] = str_replace([' ', '.', '-'], '', $acompanhanteDados['cpf']);
        $acompanhanteDados['telefone'] = str_replace([' ', '.', '-', '(', ')', '+'], '', $acompanhanteDados['telefone']);


        $esquema = v::key('nome', v::stringVal()->length(5, 50)->notEmpty())
            ->key('sobrenome', v::stringVal()->length(5, 50)->notEmpty())
            ->key('cpf', v::cpf())
            ->key('telefone', v::phone())
            ->key('convidado_idconvidado', v::intVal());

        try {
            
            $esquema->assert($acompanhanteDados);

        } catch (NestedValidationException $e) {
            $mensagemPersonalizada = [
                'nome' => 'Nome inválido, mínimo 5 caracteres e máximo 50',
                'sobrenome' =>  'Sobrenome inválido, mínimo 5 caracteres e máximo 50',
                'cpf' => 'CPF inválido',
                'telefone' => 'Telefone inválido',
                'convidado_idconvidado' => 'Id do convidado deve ser um número'
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
