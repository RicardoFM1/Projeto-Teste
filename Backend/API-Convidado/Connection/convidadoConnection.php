<?php


function dbConvidadoConnection () {
    try{
    return new PDO("mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DBCONVIDADO_NAME'], 
    $_ENV['DB_USER'], $_ENV['DB_PASS'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    }catch(PDOException $e){
        die("Erro ao conectar ao banco do convidado/acompanhante" . $e->getMessage());
    }
}