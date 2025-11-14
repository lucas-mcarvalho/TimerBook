# Imagem base: PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar todos os arquivos do projeto
COPY . /var/www/html/

# Alterar o DocumentRoot do Apache para apontar para /var/www/html/public
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess dentro da pasta public
RUN echo "<Directory /var/www/html/public>\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>" > /etc/apache2/conf-available/app.conf && \
    a2enconf app

# Ajustar permissões
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html


    # Criar alias para servir JS que está fora do /public
RUN echo "Alias /js /var/www/html/App/views/javascript\n\
<Directory /var/www/html/App/views/javascript>\n\
    Require all granted\n\
</Directory>" \
>> /etc/apache2/conf-available/app.conf
# Definir diretório de trabalho (dentro da pasta public)
WORKDIR /var/www/html/public

# Expor porta padrão do Apache
EXPOSE 80