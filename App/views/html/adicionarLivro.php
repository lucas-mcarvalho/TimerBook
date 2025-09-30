<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Novo Livro</title>
    <style>
        /* Estilos simples para o exemplo */
        body { font-family: sans-serif; max-width: 600px; margin: 2em auto; }
        form { display: flex; flex-direction: column; gap: 10px; }
        input, button { padding: 10px; }
        #feedback { margin-top: 15px; padding: 10px; border-radius: 5px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Cadastrar Novo Livro</h1>
    
    <form id="bookForm">
        <label for="titulo">Título:</label>
        <input type="text" id="titulo" name="titulo" required>

        <label for="autor">Autor:</label>
        <input type="text" id="autor" name="autor">

        <label for="ano_publicacao">Ano de Publicação:</label>
        <input type="number" id="ano_publicacao" name="ano_publicacao">

        <label for="user_id">ID do Usuário:</label>
        <input type="number" id="user_id" name="user_id" required>

        <label for="caminho_arquivo">Arquivo do Livro (PDF, EPUB, etc.):</label>
        <input type="file" id="caminho_arquivo" name="caminho_arquivo" required>

        <button type="submit">Salvar Livro</button>
    </form>
    
    <div id="feedback"></div>

    
    <script src="/TimerBook/App/views/javascript/livro.js"></script>


</html>