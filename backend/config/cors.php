<?php

//Configurações de CORS para permitir requisições do frontend
return [

    //Definir os endpoints da API que podem ser acessados pelo frontend
    'paths' => ['api/*'],
    
    //Definir os métodos HTTP permitidos (GET, POST, PUT, DELETE, etc.)
    'allowed_methods' => ['*'],
    
    //Definir os domínios que podem acessar a API (ex: http://localhost:3000)
    'allowed_origins' => ['http://localhost:3000'],

    //Patterns de origens permitidas (ex: *.meudominio.com)
    'allowed_origins_patterns' => [],

    //Definir os headers permitidos nas requisições
    'allowed_headers' => ['*'],

    //expor os headers de resposta para o frontend
    'exposed_headers' => [],

    //Permitir ou não o envio de cookies e credenciais nas requisições
    'max_age' => 0,

    //Permitir ou não o envio de cookies e credenciais nas requisições
    'supports_credentials' => false,
];