<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão
if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3)) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

$produto = null;

if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    if ($id) {
        $sql = "SELECT * FROM produto WHERE id_produto = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Se o formulário for enviado, busca o usuário pelo ID ou nome
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_produto'])) {
    $busca = trim($_POST['busca_produto']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM produto WHERE id_produto = :busca";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM produto WHERE nome_prod LIKE :busca_nome";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }

    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

// Se o usuário não for encontrado, exibe um alerta
    if (!$produto) {
        echo "<script>alert('Produto não encontrado!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Alterar Produto</title>
    <link rel="stylesheet" href="style.css">
    <script src="scripts.js"></script>
</head>
<body>
    <h1>Bianca de Andrade Genovencio<h1>
    <h2>Alterar Produto</h2>

 <!-- Formulário para buscar usuário pelo ID ou Nome -->
    <form action="alterar_produto.php" method="POST">
        <label for="busca_produto">Digite o ID ou Nome do produto:</label>
        <input type="text" id="busca_produto" name="busca_produto" onkeyup="buscarSugestoes()">

 <!-- Div para exibir sugestões de usuários -->
        <div id="sugestoes"></div>

        <button type="submit">Buscar</button>
    </form>

    <?php if ($produto): ?>
       <!-- Formulário para alterar produto-->
        <form action="processa_alteracao_produto.php" method="POST">
            <input type="hidden" name="id_produto" value="<?= htmlspecialchars($produto['id_produto']) ?>">

            <label for="nome_prod">Nome:</label>
            <input type="text" id="nome_prod" name="nome_prod" value="<?= htmlspecialchars($produto['nome_prod']) ?>" required>

            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?= htmlspecialchars($produto['descricao']) ?>" required>

            <label for="qtde">Quantidade:</label>
            <input type="number" id="qtde" name="qtde" value="<?= htmlspecialchars($produto['qtde']) ?>" min="0" required>

            <label for="valor_unit">Valor Unitário:</label>
            <input type="number" id="valor_unit" name="valor_unit" step="0.01" min="0" value="<?= htmlspecialchars($produto['valor_unit']) ?>" required>

            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <a href="principal.php">Voltar</a>
</body>
</html>
