<?php
session_start();
require 'conexao.php';

// Verifica se o usuário tem permissão de ADM ou Secretaria
if (!isset($_SESSION['perfil']) || ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2)) {
    echo "<script>alert('Acesso negado!'); window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitização e validação
    $id = filter_var($_POST["id_produto"], FILTER_SANITIZE_NUMBER_INT);
    $nome = htmlspecialchars(trim($_POST["nome_prod"]));
    $descricao = htmlspecialchars(trim($_POST["descricao"]));
    $qtde = filter_var($_POST["qtde"], FILTER_VALIDATE_INT);
    $valor_unit = filter_var($_POST["valor_unit"], FILTER_VALIDATE_FLOAT);

    // Verificações básicas
    if (!$id || !$nome || !$descricao || $qtde === false || $valor_unit === false) {
        echo "<script>alert('Erro: Dados inválidos. Verifique os campos preenchidos.'); window.history.back();</script>";
        exit();
    }

    try {
        $sql = "UPDATE produto 
                SET nome_prod = :nome, 
                    descricao = :descricao, 
                    qtde = :qtde, 
                    valor_unit = :valor_unit 
                WHERE id_produto = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":nome", $nome);
        $stmt->bindParam(":descricao", $descricao);
        $stmt->bindParam(":qtde", $qtde, PDO::PARAM_INT);
        $stmt->bindParam(":valor_unit", $valor_unit);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        $stmt->execute();

        echo "<script>alert('Produto atualizado com sucesso!'); window.location.href='alterar_produto.php';</script>";
    } catch (PDOException $e) {
        error_log("Erro ao atualizar produto: " . $e->getMessage());
        echo "<script>alert('Erro ao atualizar produto.'); window.history.back();</script>";
    }
}
?>
