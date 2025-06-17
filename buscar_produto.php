<?php
session_start();
require_once 'conexao.php';

//VERIFICA SE USUARIO TEM PERMISSÃO DE ADM OU SECRETARIA
if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2)) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

$produtos = []; //INICIALIZA A VARIAVEL PARA EVITAR ERROS

//SE O FORMULÁRIO FOR ENVIADO, BUSCA O USUÁRIO PELO ID OU NOME
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['busca']))) {
    $busca = trim($_POST['busca']);

    //VERIFICA SE A BUSCA É UM NÚMERO(ID) OU UM NOME
    if (is_numeric($busca)) {
        $sql = "SELECT * FROM produto WHERE id_produto = :busca ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        $sql = "SELECT * FROM produto WHERE nome_prod LIKE :busca_nome ORDER BY nome_prod ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM produto ORDER BY nome_prod ASC";
    $stmt = $pdo->prepare($sql);
}
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="PT-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Bianca de Andrade Genovencio<h1>
    <h2>Lista de Produtos</h2>

<!-- FORMULARIO PARA BUSCAR USUARIOS -->
    <form action="buscar_produto.php" method="POST">
        <label for="busca">Digite o ID ou Nome do Produto(opcional):</label>
        <input type="text" id="busca" name="busca">

        <button type="submit">Pesquisar</button>
    </form>

    <?php if(!empty($produtos)):?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor unitário</th>
                <th>Ações</th>
            </tr>
            <?php foreach($produtos as $produto): ?>
            <tr>
                <td><?=htmlspecialchars($produto['id_produto']) ?></td>
                <td><?=htmlspecialchars($produto['nome_prod']) ?></td>
                <td><?=htmlspecialchars($produto['descricao']) ?></td>
                <td><?=htmlspecialchars($produto['qtde']) ?></td>
                <td><?=htmlspecialchars($produto['valor_unit']) ?></td>
                <td>
                    <a href = "alterar_produto.php?id=<?=htmlspecialchars($produto['id_produto']) ?>">Alterar</a>
                    <a href = "excluir_produto.php?id=<?=htmlspecialchars($produto['id_produto']) ?>"onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
<?php else: ?>
    <p>Nenhum produto encontrado.</p>
<?php endif; ?>

<a href= "principal.php"> VOLTAR </a>
</body>
</html>
