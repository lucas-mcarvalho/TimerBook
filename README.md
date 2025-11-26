# TimerBook

<img src="./TimerbookLogo.png" width="300" />

**Curso: Ciência da Computação**

**Professor: Dr. Edeílson Milhomem**



**GRUPO 1:**

| Nome                           | Perfil GitHub                                 |
|--------------------------------|----------------------------------------------|
| Tiago Barbosa de Castro Souza  | [TiagoBrs](https://github.com/TiagoBrs)     |
| Vitor Kawan Barbosa Borges     | [KawanVitor1](https://github.com/KawanVitor1)|
| Lucas Monteiro de Carvalho     | [lucas-mcarvalho](https://github.com/lucas-mcarvalho)|
| Matheus Silva Pontes           | [matheuspontes01](https://github.com/matheuspontes01)|
| Bruno Henrique Frota Sobral    | [Bruno-uft](https://github.com/Bruno-uft)  |
| Kayk Zago Pinheiro             | [kayke002](https://github.com/kayke002)    |





# DESCRIÇÃO DO PROJETO

O projeto TimerBook tem como objetivo ser um software para gerenciamento de suas leituras. Inclui funcionalidades como o cadastro de livros que o usuário esteja lendo, acesso a banco de dados de livros já cadastrados e sistemas de métricas e lembrentes para auxiliar o usuário na leitura de seus diversos livros e comensurar seu desempenho.

[Clique aqui e veja nossa landing-page do nosso projeto](https://lucas-mcarvalho.github.io/TimerBook/landing-page/)

# O projeto foi feito usando as seguintes tecnologias:
<p align="left">

<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" width="50" />
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/mysql/mysql-original-wordmark.svg" width="50" />
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/amazonwebservices/amazonwebservices-original-wordmark.svg" width= "50"/>
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/docker/docker-original-wordmark.svg" width="50" />
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/html5/html5-original-wordmark.svg" width="50"/>
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/css3/css3-original-wordmark.svg" width="50"/>
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon@latest/icons/javascript/javascript-original.svg" width="50"/>
</p>                                   
          
## 🔗 Links Úteis


* **Slides da apresentação final :** [Ver slide](https://github.com/lucas-mcarvalho/TimerBook/blob/main/docs/apresenta%C3%A7%C3%A3o_final.pdf)
* **Sprints (Drive):** [Ver Documento de Planejamento](https://github.com/lucas-mcarvalho/TimerBook/blob/main/docs/sprints.pdf)
* **Planejamento no Trello:**[Trello](https://trello.com/b/WXQL66Tj/timerbook-sistema-de-gerenciamento-de-leitura)
* **User Stories & Protótipo (Figma):** [Ver Telas no Figma](https://www.figma.com/proto/IihsrG5vSCSRwtLC64WnLd/User-Stories?node-id=0-1&t=epDzeNh9tx9MlHN4-1)
* **Deploy (Sistema no Ar):** [http://15.228.40.136/TimerBook/public/](http://15.228.40.136/TimerBook/public/)
* **Vídeo das Funcionalidades:** [Assista ao vídeo](https://youtu.be/du8Ec8_fWgY)
* **Landing Page:**[Abrir Landing Page](https://lucas-mcarvalho.github.io/TimerBook/landing-page/ )


## 🖥️ Estrutura do Projeto

```
TimerBook/

├── App/
 └── controllers/
 └── models/
 └── views/
 └── cron/
 └── core

├── public/
    └── index.php
    └── api.php
    └── style/
    └── uploads/
    └── .htacces
 
├──.env
├──.gitignore
├──.phpunit.result.cache
├──composer.json
├──composer.lock
├──index.html
├──phpunit.xml
├──README.MD
├──run_reminders.bat
├──TimerbookLogo.png
├──vendor/
├──tests/
├──mysql-init/
├──logs/



```

---

## 🛠️ Como rodar o projeto

Você tem duas opções para rodar o projeto: **Manual (XAMPP)** ou **Via Docker**. Escolha a que preferir abaixo.

  **Pré-requisito: Instalar o Git**
⚠️ Antes de prosseguir, certifique-se de que você tem o **Git** instalado na sua máquina.
* **Windows/Linux/Mac:** [Baixe e instale o Git aqui](https://git-scm.com/downloads).

1.  **Clone o repositório**

    ```bash
    git clone [https://github.com/lucas-mcarvalho/TimerBook.git](https://github.com/lucas-mcarvalho/TimerBook.git)
    cd TimerBook
    ```

2. **Configure o ambiente**
    Crie um arquivo `.env` na raiz do projeto e cole o conteúdo abaixo.

    ⚠️ **Atenção:** Você precisará preencher as chaves da **AWS (S3 Bucket)** e as configurações de **SMTP (E-mail)** com seus próprios dados para que o upload de imagens e envio de e-mails funcionem.

    ```ini
    # Docker (Mantenha comentado para uso local/XAMPP)
    #DB_CONNECTION=mysql
    #DB_HOST=db
    #DB_PORT=3306
    #DB_DATABASE=Users
    #DB_USERNAME=timerbook_user
    #DB_PASSWORD=1234

    # Configuração Local (XAMPP)
    DB_HOST=localhost
    DB_USER=root
    DB_NAME=Users
    DB_PASS=

    # Configuração de E-mail (Necessário configurar SMTP)
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=seu_email@gmail.com
    MAIL_PASSWORD=sua_senha_de_app
    MAIL_FROM=seu_email@gmail.com
    MAIL_FROM_NAME=TimerBook

    # AWS S3 (Necessário criar um Bucket na AWS)
    AWS_ACCESS_KEY_ID=SUA_CHAVE_DE_ACESSO_AQUI
    AWS_SECRET_ACCESS_KEY=SUA_CHAVE_SECRETA_AQUI
    AWS_DEFAULT_REGION=sa-east-1
    S3_BUCKET_NAME=nome-do-seu-bucket

    # Google OAuth
    GOOGLE_CLIENT_ID=seu-client-id.apps.googleusercontent.com
    GOOGLE_CLIENT_SECRET=seu-client-secret
    ```
3.  **Instalação do Servidor e Banco de Dados**
    * Instale o **XAMPP** (que já inclui Apache e MySQL).
    * Instale a versão mais recente do **PHP**.
    * Inicie o Apache e o MySQL através do painel de controle do XAMPP.
    * Vá até a pasta `mysql-init` na raiz do projeto.
    * No seu gerenciador de banco de dados (ex: PHPMyAdmin), crie um banco chamado `Users`.
    * Copie e execute os comandos SQL contidos na pasta `mysql-init` para criar as tabelas e inserir os dados iniciais.

4.  **Mover os arquivos para o servidor**
    Dependendo do seu sistema operacional, mova a pasta do projeto para o diretório do Apache:

    * **🪟 Windows:**
        Copie a pasta `TimerBook` e cole dentro de: `C:\xampp\htdocs\`

    * **🐧 Linux:**
        Execute o comando abaixo no terminal:
        ```bash
        sudo cp -r TimerBook /opt/lampp/htdocs/
        ```
        *Ajuste as permissões com: `sudo chmod -R 777 /opt/lampp/htdocs/TimerBook`*

5.  **Executar o projeto**
    Após copiar a pasta, inicie o Apache pelo painel do XAMPP (se ainda não estiver rodando).
    Abra o navegador e acesse a seguinte URL:
    ```
    http://localhost/TimerBook/
    ```

---
## 🐳 Como rodar com Docker (Alternativa)

Se você preferir não instalar o XAMPP, pode rodar o projeto usando containers Docker.

1.  **Pré-requisitos**
    * Tenha o **Git**, **Docker** e o **Docker Compose** instalados na sua máquina.

2.  **Configuração do Ambiente (.env)**
    Abra o arquivo `.env` e **alterne** as configurações do banco de dados. Comente as linhas do "Localhost" e descomente as linhas do "Docker", para que fique assim:

    ```ini
    # Configuração Docker (DESCOMENTE ESTAS LINHAS)
    DB_CONNECTION=mysql
    DB_HOST=db
    DB_PORT=3306
    DB_DATABASE=Users
    DB_USERNAME=timerbook_user
    DB_PASSWORD=1234

    # Configuração Local (COMENTE ESTAS LINHAS)
    # DB_HOST=localhost
    # DB_USER=root
    # DB_NAME=Users
    # DB_PASS=
    ```

3.  **Subir os Containers**
    Na raiz do projeto, abra o terminal, troque para a branch correta e execute:

    ```bash
    git checkout docker
    docker-compose up -d --build
    ```

    *Aguarde alguns instantes. O banco de dados será criado e configurado automaticamente na primeira execução.*

4.  **Para Acessar o Projeto ,**
    Abra seu navegador e acesse:
    ```
    http://localhost:8080
    ```
    *(Ou a porta definida no seu docker-compose.yml)*.


## 🔔 Configuração dos Lembretes Automáticos

⚠️ **Atenção:** Se você rodou o projeto via **Docker**, pule esta etapa. O container já gerencia isso automaticamente.

Para quem usa **XAMPP/Instalação Manual**, é necessário configurar uma tarefa automática para enviar notificações diariamente.

Para que o sistema envie as notificações de leitura, é necessário configurar uma tarefa automática (Cronjob) que execute diariamente à meia-noite.

### 🪟 No Windows (Script .bat)
O projeto já inclui um arquivo chamado `run_reminders.bat` na raiz.

1.  Abra o arquivo `run_reminders.bat` em um editor de texto e verifique se o caminho para o executável do PHP e para o projeto estão corretos no seu computador.
2.  Pressione `Win + R`, digite `taskschd.msc` e dê Enter para abrir o **Agendador de Tarefas**.
3.  No menu à direita, clique em **"Criar Tarefa Básica"**.
4.  Defina o nome como `TimerBook Lembretes`.
5.  No disparador, escolha **Diariamente** e defina o horário para **00:00:00** (Meia-noite).
6.  Na ação, escolha **"Iniciar um programa"** e selecione o arquivo `run_reminders.bat` que está na pasta do projeto.
7.  Conclua a criação.

### 🐧 No Linux (Cron)
Utilize o Crontab para agendar a execução do script PHP.

⚠️ **Importante:** Certifique-se de que o pacote `cron` está instalado na sua distribuição (algumas versões minimalistas não o trazem por padrão).

**1. Instalação e Inicialização:**
* **Debian/Ubuntu:**
    * Instalar: `sudo apt install cron`
    * Iniciar: `sudo service cron start`
* **Arch Linux:**
    * Instalar: `sudo pacman -S cronie`
    * Iniciar: `sudo systemctl start cronie`
* **Fedora:**
    * Instalar: `sudo dnf install cronie`
    * Iniciar: `sudo systemctl start cronie`
* **Gentoo:**
    * Instalar: `sudo emerge sys-process/cronie`
    * Iniciar: `sudo /etc/init.d/cronie start`
* *Para iniciar o serviço:* `sudo service cron start`

1.  Abra o terminal e digite:
    ```bash
    crontab -e
    ```
2.  Adicione a seguinte linha ao final do arquivo:
    ```bash
    0 0 * * * /opt/lampp/bin/php /opt/lampp/htdocs/TimerBook/App/cron/send_reminders.php >> /opt/lampp/htdocs/TimerBook/logs/cron.log 2>&1
    ```
3.  Salve e saia (`Ctrl+O` e depois `Ctrl+X` se estiver usando Nano).

