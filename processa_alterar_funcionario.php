<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de ADM
if ($_SESSION['perfil'] != 1) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_funcionario = $_POST['id_funcionario'];
    $nome = trim($_POST['nome_funcionario']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $permissao = trim($_POST['permissao']);
    
    // Verifica se o email já existe para outro funcionário
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM funcionario WHERE email = :email AND id_funcionario != :id_funcionario");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id_funcionario', $id_funcionario);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('Este email já está sendo usado por outro funcionário.');history.back();</script>";
        exit;
    }

    $sql = "UPDATE funcionario 
            SET nome_funcionario = :nome, endereco = :endereco, telefone = :telefone, email = :email, permissao = :permissao
            WHERE id_funcionario = :id_funcionario";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':permissao', $permissao);
    $stmt->bindParam(':id_funcionario', $id_funcionario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Funcionário atualizado com sucesso!');window.location.href='buscar_funcionario.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar funcionário.');history.back();</script>";
    }
}
?>