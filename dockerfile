# Imagem base: PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite do Apache
RUN a2enmod rewrite

# Copiar todos os arquivos do projeto para o diretório web do container
COPY . /var/www/html/

# Alterar o DocumentRoot do Apache para apontar para a pasta /public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Configurar o Apache para permitir o uso de arquivos .htaccess na pasta public
RUN echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/app.conf && \
    a2enconf app

# Ajustar permissões dos arquivos para o usuário do Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Definir o diretório de trabalho padrão
WORKDIR /var/www/html

# Expor a porta padrão do Apache
EXPOSE 80

# Comando para iniciar o Apache em primeiro plano
CMD ["apache2-foreground"]
