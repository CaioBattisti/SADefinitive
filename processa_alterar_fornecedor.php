<?php
require 'conexao.php'; // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_fornecedor = $_POST['id_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];

    // Validação básica
    if (empty($id_fornecedor) || empty($nome_fornecedor) || empty($email)) {
        echo "Por favor, preencha todos os campos obrigatórios.";
        exit;
    }

    // Atualizar os dados do fornecedor no banco de dados
    $sql = "UPDATE fornecedor SET 
                nome_fornecedor = :nome_fornecedor, 
                endereco = :endereco, 
                telefone = :telefone, 
                email = :email 
            WHERE id_fornecedor = :id_fornecedor";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id_fornecedor', $id_fornecedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Fornecedor atualizado com sucesso!";
        header("Location: buscar_fornecedor.php");
        exit;
    } else {
        echo "Erro ao atualizar fornecedor.";
    }
} else {
    echo "Método de requisição inválido.";
}
?>