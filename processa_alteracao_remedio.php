<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão para alterar remedios
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_remedio = $_POST['id_remedio'];
    $nome_remedio = $_POST['nome_remedio'];
    $descricao = $_POST['descricao'];
    $validade = $_POST['validade'];
    $qnt_estoque = $_POST['qnt_estoque'];
    $preco_unit = $_POST['preco_unit'];
    $id_fornecedor = $_POST['id_fornecedor'];

    $sql = "UPDATE remedio SET nome_remedio = :nome_remedio, descricao = :descricao, validade = :validade, qnt_estoque = :qnt_estoque, preco_unit = :preco_unit, id_fornecedor = :id_fornecedor WHERE id_remedio = :id_remedio";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':validade', $validade);
    $stmt->bindParam(':qnt_estoque', $qnt_estoque, PDO::PARAM_INT);
    $stmt->bindParam(':preco_unit', $preco_unit);
    $stmt->bindParam(':id_fornecedor', $id_fornecedor, PDO::PARAM_INT);
    $stmt->bindParam(':id_remedio', $id_remedio, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Remédio atualizado com sucesso!');window.location.href='buscar_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar remédio.');window.location.href='alterar_remedio.php?id=$id_remedio';</script>";
    }
}
?>