# Usar imagem oficial do PHP com Apache
FROM php:8.2-apache

# Copiar todos os arquivos do projeto para dentro do servidor
COPY . /var/www/html/

# Expor a porta que o Apache vai usar
EXPOSE 80
