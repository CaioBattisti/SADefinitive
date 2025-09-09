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

// Definição das Permissões por Perfil
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_fornecedor.php", "cadastro_remedio.php", "cadastro_funcionario.php"],
        "Buscar" => ["buscar_usuario.php", "buscar_fornecedor.php", "buscar_remedio.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_usuario.php", "alterar_fornecedor.php", "alterar_remedio.php", "alterar_funcionario.php"],
        "Excluir" => ["excluir_usuario.php", "excluir_fornecedor.php", "excluir_remedio.php", "excluir_funcionario.php"]
    ],
    2 => [
        "Cadastrar" => ["cadastro_remedio.php"],
        "Buscar" => ["buscar_fornecedor.php","buscar_funcionario.php","buscar_remedio.php"],
        "Alterar" => ["alterar_fornecedor.php", "alterar_funcionario.php", "alterar_remedio.php"]
    ],
    3 => [
        "Cadastrar" => ["cadastro_remedio.php"],
        "Buscar" => ["buscar_remedio.php"]
    ],
    4 => [
        "Cadastrar" => ["cadastro_remedio.php"],
        "Buscar" => ["buscar_remedio.php"]
    ]
];

// Mapeamento de ícones para as categorias de menu
$icones_menu = [
    "Cadastrar" => "fa-solid fa-plus-circle",
    "Buscar" => "fa-solid fa-search",
    "Alterar" => "fa-solid fa-edit",
    "Excluir" => "fa-solid fa-trash-alt"
];

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Processa o formulário
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $nome_remedio = trim($_POST['nome_remedio']);
    $descricao = trim($_POST['descricao']);
    $validade = trim($_POST['validade']);
    $qnt_estoque = trim($_POST['qnt_estoque']);
    $preco_unit = trim($_POST['preco_unit']);
    $tipo = trim($_POST['tipo']);
    $id_fornecedor = trim($_POST['id_fornecedor']);

    $errors = [];

    // Validar se o nome do remédio já existe
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM remedio WHERE nome_remedio = :nome_remedio");
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Este remédio já está cadastrado!";
    }

    if (!empty($errors)) {
        echo "<script>alert('" . implode("\\n", $errors) . "');history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO remedio (nome_remedio, descricao, validade, qnt_estoque, preco_unit, tipo, id_fornecedor) 
            VALUES (:nome_remedio, :descricao, :validade, :qnt_estoque, :preco_unit, :tipo, :id_fornecedor)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_remedio', $nome_remedio);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':validade', $validade);
    $stmt->bindParam(':qnt_estoque', $qnt_estoque);
    $stmt->bindParam(':preco_unit', $preco_unit);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':id_fornecedor', $id_fornecedor);

    if ($stmt->execute()) {
        echo "<script>alert('Remédio cadastrado com sucesso!');window.location.href='cadastro_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar remédio!');history.back();</script>";
    }
}

// Busca todos os fornecedores para o dropdown
$stmtFornecedores = $pdo->query("SELECT id_fornecedor, nome_empresa FROM fornecedor ORDER BY nome_empresa ASC");
$fornecedores = $stmtFornecedores->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Remédio</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><i class="<?= $icones_menu[$categoria] ?? '' ?>"></i> <?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li>
                                <a href="<?= $arquivo ?>">
                                    <?php
                                        $nome = ucfirst(str_replace("_", " ", basename($arquivo, ".php")));
                                        if (strpos($nome, "remedio") !== false) echo "<i class='fa-solid fa-capsules'></i> ";
                                        if (strpos($nome, "usuario") !== false) echo "<i class='fa-solid fa-users'></i> ";
                                        if (strpos($nome, "fornecedor") !== false) echo "<i class='fa-solid fa-truck'></i> ";
                                        if (strpos($nome, "funcionario") !== false) echo "<i class='fa-solid fa-user-nurse'></i> ";
                                        echo $nome;
                                    ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div style="position: relative; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0;">Cadastro de Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form action="cadastro_remedio.php" method="POST" id="formCadastro">
        <label for="nome_remedio">Nome do Remédio:</label>
        <input type="text" id="nome_remedio" name="nome_remedio" required>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"></textarea>

        <label for="validade">Validade:</label>
        <input type="date" id="validade" name="validade" required>

        <label for="qnt_estoque">Quantidade(Caixas):</label>
        <input type="number" id="qnt_estoque" name="qnt_estoque" min="0" required>

        <label for="preco_unit">Preço Unitário:</label>
        <input type="number" step="0.01" id="preco_unit" name="preco_unit" min="0" required>

        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="Comprimido">Comprimido</option>
            <option value="Gota">Gota</option>
            <option value="Creme">Creme</option>
            <option value="Injeção">Injeção</option>
            <option value="Inalação">Inalação</option>
        </select>
        
        <label for="id_fornecedor">Nome da Empresa:</label>
        <select id="id_fornecedor" name="id_fornecedor" required>
            <?php foreach ($fornecedores as $fornecedor): ?>
                <option value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">
                    <?= htmlspecialchars($fornecedor['nome_empresa']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit"><i class="fa-solid fa-check"></i> Salvar</button>
        <button type="reset"><i class="fa-solid fa-ban"></i> Cancelar</button>
    </form>

    <a href="principal.php">Voltar para o Menu</a>
</body>
</html>