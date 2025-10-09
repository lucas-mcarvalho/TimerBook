<?php
require_once __DIR__ . '/../../models/Admin.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../models/Books.php';
$id = $_GET['id'] ?? null;
$user = null;
if ($id) {
    $books = Admin::getUserBooks($id);
}
else {
    $books = ["error" => "ID do usuário não fornecido"];   
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/editarLivroAdmin.css">
    <title>Admin - Gerenciar Livros</title>
</head>
<body>

    <header class="main-header">
        <div class="header-logo">
            <a href="index.php?action=home" class="logo-link">
                <img src="uploads/main_logo.png" alt="Logo Timerbook" class="logo-img">
                <h1>TimerBook</h1>
            </a>
        </div>
        <div class="header-buttons">
            <button class="nav1-button" onclick="window.history.back()">Voltar</button> 
            <button class="nav-button" onclick="window.location.href='index.php?action=home'">Tela Principal</button> 
        </div>
    </header>

    <main class="admin-container">
        <h2 class="title">GERENCIAR LIVROS DO USUÁRIO</h2>
        
        <table class="books-table">
            <thead>
                <tr>
                    <th>Capa</th>
                    <th>Título</th>
                    <th>Autor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!isset($books)) {
                    // tenta carregar o model Book e buscar pelo id passado na query string
                    require_once __DIR__ . '/../../models/Books.php';
                    $userId = $_GET['id'] ?? null;
                    $books = $userId ? Book::getByUser($userId) : [];
                }

                if (is_array($books) && count($books) > 0):
                    foreach ($books as $book):
                        $cover = !empty($book['capa_livro']) ? $book['capa_livro'] : 'https://placehold.co/50x75';
                ?>
                <tr>
                    <td><img src="<?php echo htmlspecialchars($cover); ?>" alt="Capa" class="book-cover"></td>
                    <td><?php echo htmlspecialchars($book['titulo'] ?? '—'); ?></td>
                    <td><?php echo htmlspecialchars($book['autor'] ?? '—'); ?></td>
                    <td class="actions-cell">
                        <a href="index.php?action=editar_livro&book-id=<?php echo urlencode($book['id']); ?>"><button class="edit-btn">Editar</button></a>
                        
                        <a href="index.php?action=adm_excluirLivro&book-id=<?php echo urlencode
                        ($book['id']);?>&user-id=<?php echo urlencode($id);?>" onclick="return confirm('Tem certeza que deseja excluir?')"><button class="delete-btn">Excluir</button></a>
                    </td>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                <tr>
                    <td colspan="4">Nenhum livro encontrado para este usuário.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="add-button-container">
            <a href="index.php?action=Adicionar_Livro" class="add-book-btn">
                + Adicionar Novo Livro
            </a>
        </div>
    </main>

</body>
</html>