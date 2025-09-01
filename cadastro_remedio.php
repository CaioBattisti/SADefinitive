<?php
session_start();
require_once 'conexao.php';

// Verifica se usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtendo o Nome do Perfil do Usuario Logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];

// Definição das Permissões por Perfil
$permissoes = [
    1=>["Cadastrar"=>["cadastro_usuario.php","cadastro_fornecedor.php", "cadastro_remedio.php", "cadastro_funcionario.php"],
        "Buscar"=>["buscar_usuario.php","buscar_fornecedor.php", "buscar_remedio.php", "buscar_funcionario.php"],
        "Alterar"=>["alterar_usuario.php","alterar_fornecedor.php", "alterar_remedio.php", "alterar_funcionario.php"],
        "Excluir"=>["excluir_usuario.php","excluir_fornecedor.php", "excluir_remedio.php", "excluir_funcionario.php"]],

    2=>["Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar"=>["alterar_remedio.php"]],

    3=>["Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_remedio.php"]],

    4=>["Cadastrar"=>["cadastro_remedio.php"]]
];

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Verifica se o usuario tem permissão para cadastrar remedios
if (!in_array("cadastro_remedio.php", $opcoes_menu["Cadastrar"])) {
    echo "Acesso Negado";
    exit();
}

// Processa o formulário
if($_SERVER['REQUEST_METHOD'] =="POST"){
    $nome_remedio = trim($_POST['nome_remedio']);
    $descricao = trim($_POST['descricao']);
    $validade = $_POST['validade'];
    $qnt_estoque = $_POST['qnt_estoque'];
    $preco_unit = $_POST['preco_unit'];
    $id_fornecedor = $_POST['id_fornecedor'];

    $errors = [];

    // Validação da validade
    if (empty($validade) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $validade)) {
        $errors[] = "Digite uma data de validade válida (YYYY-MM-DD)!";
    }

    // Se houver erros, mostra alerta
    if (count($errors) > 0) {
        echo "<script>alert('" . implode("\\n", $errors) . "');history.back();</script>";
        exit;
    }

    // Cadastro do remedio
    $sql = "INSERT INTO remedio (nome_remedio, descricao, validade, qnt_estoque, preco_unit, id_fornecedor) VALUES (:nome_remedio, :descricao, :validade, :qnt_estoque, :preco_unit, :id_fornecedor)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':validade', $validade);
    $stmt->bindParam(':qnt_estoque', $qnt_estoque, PDO::PARAM_INT);
    $stmt->bindParam(':preco_unit', $preco_unit);
    $stmt->bindParam(':id_fornecedor', $id_fornecedor, PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Remédio cadastrado com sucesso!');window.location.href='cadastro_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar remédio!');history.back();</script>";
    }
}

// Obtém a lista de fornecedores para o dropdown
$sql_fornecedores = "SELECT id_fornecedor, nome_fornecedor FROM fornecedor ORDER BY nome_fornecedor";
$stmt_fornecedores = $pdo->prepare($sql_fornecedores);
$stmt_fornecedores->execute();
$fornecedores = $stmt_fornecedores->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Remédio</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php"))) ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div style="position: relative; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0;">Cadastrar Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="cadastro_remedio.php" method="POST" id="formCadastro">
        <label for="nome_remedio">Nome do Remédio:</label>
        <input type="text" id="nome_remedio" name="nome_remedio" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"></textarea>

        <label for="validade">Validade:</label>
        <input type="date" id="validade" name="validade" required>

        <label for="qnt_estoque">Quantidade em Estoque:</label>
        <input type="number" id="qnt_estoque" name="qnt_estoque" min="0" required>

        <label for="preco_unit">Preço Unitário:</label>
        <input type="number" step="0.01" id="preco_unit" name="preco_unit" min="0" required>

        <label for="id_fornecedor">Fornecedor:</label>
        <select id="id_fornecedor" name="id_fornecedor" required>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <option value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">
                    <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <a href="principal.php">Voltar Para o Menu</a>

    <script src="Mascara/script.js"></script>
</body>
</html>