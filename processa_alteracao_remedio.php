<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de ADM ou Secretária
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_remedio   = $_POST['id_remedio'];
    $nome_remedio = trim($_POST['nome_remedio']);
    $descricao    = trim($_POST['descricao']);
    $validade     = trim($_POST['validade']);
    $qnt_estoque  = trim($_POST['qnt_estoque']);
    $preco_unit   = trim($_POST['preco_unit']);
    $tipo         = trim($_POST['tipo']);
    $id_fornecedor= trim($_POST['id_fornecedor']);

    // Verifica se o nome do remédio já existe para outro remédio
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM remedio WHERE nome_remedio = :nome_remedio AND id_remedio != :id_remedio");
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->bindParam(':id_remedio', $id_remedio);
    $stmt->execute();
    
    if ($stmt->fetchColumn() > 0) {
        echo "<script>alert('Este nome de remédio já está sendo usado por outro remédio.');history.back();</script>";
        exit;
    }

    // Busca a imagem atual do banco (caso não seja enviada nova)
    $stmtImg = $pdo->prepare("SELECT imagem FROM remedio WHERE id_remedio = :id_remedio");
    $stmtImg->bindParam(':id_remedio', $id_remedio, PDO::PARAM_INT);
    $stmtImg->execute();
    $dados = $stmtImg->fetch(PDO::FETCH_ASSOC);
    $imagem = $dados['imagem'];

    // Verifica se foi enviada nova imagem
    if (!empty($_FILES['imagem']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = time() . "_" . basename($_FILES["imagem"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $targetFilePath)) {
            $imagem = $fileName;
        }
    }

    // Atualiza os dados do remédio, incluindo a imagem
    $sql = "UPDATE remedio 
            SET nome_remedio = :nome_remedio, 
                descricao = :descricao, 
                validade = :validade, 
                qnt_estoque = :qnt_estoque, 
                preco_unit = :preco_unit, 
                tipo = :tipo, 
                id_fornecedor = :id_fornecedor, 
                imagem = :imagem
            WHERE id_remedio = :id_remedio";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':validade', $validade);
    $stmt->bindParam(':qnt_estoque', $qnt_estoque, PDO::PARAM_INT);
    $stmt->bindParam(':preco_unit', $preco_unit);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':id_fornecedor', $id_fornecedor, PDO::PARAM_INT);
    $stmt->bindParam(':imagem', $imagem);
    $stmt->bindParam(':id_remedio', $id_remedio, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Remédio atualizado com sucesso!');window.location.href='buscar_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar remédio.');history.back();</script>";
    }
}
?>