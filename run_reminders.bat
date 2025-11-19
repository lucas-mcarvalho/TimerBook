@echo off
REM Ajuste o caminho do PHP se necessário
set PHP_EXE=C:\xampp\php\php.exe
set SCRIPT=%~dp0cli_send_reminders.php

REM Cria pasta de logs se não existir
if not exist "%~dp0logs" mkdir "%~dp0logs"

REM Executa o script CLI e redireciona saída para o log
"%PHP_EXE%" -f "%SCRIPT%" %* >> "%~dp0logs\reminder.log" 2>&1
