<?php
// inserir_usuario.php

require_once 'conexao.php'; // garante a conexão PDO

// Dados do usuário a serem cadastrados
$nome = "Junior Silva";
$email = "junior@empresa.com";
$senha = "12345678";
$id_perfil = 1; // exemplo: 1=Admin, 2=Secretaria, 3=Funcionário, 4=Fornecedor

try {
    // Verificar se já existe email cadastrado
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuario WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        echo "❌ O email '$email' já está cadastrado.";
        exit;
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserção
    $sql = "INSERT INTO usuario (nome, email, senha, id_perfil) 
            VALUES (:nome, :email, :senha, :id_perfil)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senhaHash);
    $stmt->bindParam(':id_perfil', $id_perfil);

    if ($stmt->execute()) {
        echo "✅ Usuário cadastrado com sucesso!";
    } else {
        echo "❌ Erro ao cadastrar usuário.";
    }

} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
