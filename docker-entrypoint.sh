#!/bin/bash

# Iniciar cron
service cron start

# Garantir que diretório de logs existe e é escrevível
mkdir -p /var/www/html/logs
chmod -R 777 /var/www/html/logs

# Iniciar Apache
apache2-foreground
