<?php
require 'conexao.php'; // Inclua o arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_fornecedor = $_POST['id_fornecedor'];
    $nome_fornecedor = $_POST['nome_fornecedor'];
    $endereco = $_POST['endereco'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    // Adicionado: Pega o valor da permissão enviado pelo formulário
    $permissao = $_POST['permissao'];

    // Validação básica
    if (empty($id_fornecedor) || empty($nome_fornecedor) || empty($email) || empty($permissao)) {
        echo "Por favor, preencha todos os campos obrigatórios.";
        exit;
    }

    // Adicionado: Atualiza a permissão do fornecedor no banco de dados.
    // Lembre-se: o nome da coluna no banco de dados deve ser 'permissao'
    // ou o nome que você usou para armazenar a permissão.
    $sql = "UPDATE fornecedor SET 
                nome_fornecedor = :nome_fornecedor, 
                endereco = :endereco, 
                telefone = :telefone, 
                email = :email,
                permissao = :permissao
            WHERE id_fornecedor = :id_fornecedor";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_fornecedor', $nome_fornecedor);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':permissao', $permissao); // Adicionado: Vincula o parâmetro da permissão
    $stmt->bindParam(':id_fornecedor', $id_fornecedor, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Fornecedor atualizado com sucesso!";
        // Redireciona de volta para a página de busca, indicando sucesso.
        header("Location: buscar_fornecedor.php?status=success");
        exit;
    } else {
        echo "Erro ao atualizar fornecedor.";
        // Redireciona com um status de erro
        header("Location: buscar_fornecedor.php?status=error");
        exit;
    }
} else {
    echo "Método de requisição inválido.";
}
?>