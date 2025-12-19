# Imagem base PHP com Apache
FROM php:8.2-apache

# Habilita mod_rewrite (boa prática)
RUN a2enmod rewrite

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . /var/www/html/

# Ajusta permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expõe a porta usada pelo Render
EXPOSE 80

# Inicia o Apache
CMD ["apache2-foreground"]
