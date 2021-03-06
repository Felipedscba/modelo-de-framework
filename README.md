# Projeto - Modelo de framework

O objeto do projeto é criar um modelo de framework simples, que contenha as funções básicas de roteamento, controllers, models, middlewares e views.

Veja abaixo a estrutura de pastas e os arquivos principais do framework

# Diretórios

## App
Representa o conjunto de arquivos criados pelo usuário.
Sendo estes organizados na estrutura
* ***Controllers*** - Arquivos que contém as classes que controlam as requisições
* ***Helpers*** - Arquivos com funções que podem ser acessadas de forma global
* ***Libraries*** - Bibliotecas que podem ser usadas em locais diversos do sistema, ou lógicas de manipulação de dados mais apuradas, como integrações com apis de pagamentos, etc.
* ***Middlewares*** - Os middlewares são 'intermediadores' de requisições, ao acessar uma rota que necessita de autenticação por exemplo, pode existir uma middleware que faz a validação se o usuário está autenticado

## System
Representa o conjunto de arquivos do framework, como o Router, Request, Response e classes base que podem ser usadas em toda a aplicação

# Arquivos

## /index.php
Inicializa os arquivos autoload, config, e o router

## /config.php
Configurações básicas como BASE_URL, API_KEYS, Mapeamentos das middlewares, etc.
Todas as configurações devem ser incluídas nesse arquivo
