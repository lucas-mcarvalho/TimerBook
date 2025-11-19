# Imagem base: PHP 8.3 com Apache
FROM php:8.3-apache

# Instalar extensões necessárias
RUN docker-php-ext-install pdo pdo_mysql mysqli

RUN apt-get update && apt-get install -y cron \
    && rm -rf /var/lib/apt/lists/*

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Copiar todos os arquivos do projeto
COPY . /var/www/html/

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

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

RUN echo "0 0 * * * root php /var/www/html/reminder.php >> /var/www/html/logs/cron.log 2>&1" \
    > /etc/cron.d/reminder-cron \
    && chmod 0644 /etc/cron.d/reminder-cron \
    && crontab /etc/cron.d/reminder-cron


ENTRYPOINT ["docker-entrypoint.sh"]
# Definir diretório de trabalho (dentro da pasta public)
WORKDIR /var/www/html/public

# Expor porta padrão do Apache
EXPOSE 80