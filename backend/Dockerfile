# Usa a imagem oficial do PHP 8.2 com PHP-FPM
# PHP-FPM é usado para processar requisições PHP junto com Nginx
FROM php:8.2-fpm

# Atualiza lista de pacotes do sistema
# Instala dependências necessárias para Laravel e extensões PHP
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev

# Instala extensões PHP necessárias
# pdo → camada de abstração para banco
# pdo_mysql → suporte ao MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Copia o Composer da imagem oficial para dentro do container
# Evita precisar instalar manualmente
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho dentro do container
# Todos os comandos seguintes serão executados a partir daqui
WORKDIR /var/www