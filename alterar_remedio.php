<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtendo o Perfil do Usuário Logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);

// Definição das Permissões
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_usuario.php", "cadastro_fornecedor.php", "cadastro_remedio.php", "cadastro_funcionario.php"],
        "Buscar" => ["buscar_usuario.php", "buscar_fornecedor.php", "buscar_remedio.php", "buscar_funcionario.php"],
        "Alterar" => ["alterar_usuario.php", "alterar_fornecedor.php", "alterar_remedio.php", "alterar_funcionario.php"],
        "Excluir" => ["excluir_usuario.php", "excluir_fornecedor.php", "excluir_remedio.php", "excluir_funcionario.php"]
    ],
    2 => [
        "Cadastrar" => ["cadastro_remedio.php"],
        "Buscar" => ["buscar_fornecedor.php", "buscar_remedio.php"],
        "Alterar" => ["alterar_remedio.php"]
    ],
    3 => [
        "Cadastrar" => ["cadastro_remedio.php"],
        "Buscar" => ["buscar_remedio.php"]
    ],
    4 => [
        "Cadastrar" => ["cadastro_remedio.php"]
    ]
];

// Mapeamento de ícones para as categorias de menu
$icones_menu = [
    "Cadastrar" => "fa-solid fa-plus-circle",
    "Buscar" => "fa-solid fa-search",
    "Alterar" => "fa-solid fa-edit",
    "Excluir" => "fa-solid fa-trash-alt"
];

$opcoes_menu = $permissoes[$id_perfil];

// Verifica se o usuário tem permissão de ADM ou Secretária
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa variáveis
$remedio = null;
$id_busca = "";

// Se recebeu ID pela URL (via buscar_remedio.php) ou via formulário de busca
if (isset($_GET['id']) || isset($_POST['id_busca'])) {
    $id = isset($_GET['id']) ? $_GET['id'] : $_POST['id_busca'];
    $id_busca = $id;

    $stmt = $pdo->prepare("SELECT * FROM remedio WHERE id_remedio = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $remedio = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Busca todos os fornecedores para o dropdown
$stmtFornecedores = $pdo->query("SELECT id_fornecedor, nome_fornecedor FROM fornecedor ORDER BY nome_fornecedor ASC");
$fornecedores = $stmtFornecedores->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Remédio</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><i class="<?= $icones_menu[$categoria] ?? 'fa-solid fa-folder-open' ?>"></i> <?= $categoria ?></a>
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
        <h2 style="margin: 0;">Alterar Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form method="POST" action="alterar_remedio.php">
        <label for="id_busca">Digite o ID do Remédio:</label>
        <input type="number" name="id_busca" id="id_busca" value="<?= htmlspecialchars($id_busca) ?>">
        <button type="submit">Buscar</button>
    </form>
    <br>

    <?php if ($remedio): ?>
        <form method="POST" action="processa_alteracao_remedio.php">
            <input type="hidden" name="id_remedio" value="<?= $remedio['id_remedio'] ?>">

            <label>Nome do Remédio:</label>
            <input type="text" name="nome_remedio" value="<?= htmlspecialchars($remedio['nome_remedio']) ?>" required>

            <label>Descrição:</label>
            <textarea name="descricao"><?= htmlspecialchars($remedio['descricao']) ?></textarea>

            <label>Validade:</label>
            <input type="date" name="validade" value="<?= htmlspecialchars($remedio['validade']) ?>" required>

            <label>Quantidade:</label>
            <input type="number" name="qnt_estoque" value="<?= htmlspecialchars($remedio['qnt_estoque']) ?>" required>

            <label>Preço Unitário:</label>
            <input type="number" step="0.01" name="preco_unit" value="<?= htmlspecialchars($remedio['preco_unit']) ?>" required>

            <label>Tipo:</label>
            <input type="text" name="tipo" value="<?= htmlspecialchars($remedio['tipo']) ?>" required>

            <label for="id_fornecedor">Fornecedor:</label>
            <select id="id_fornecedor" name="id_fornecedor" required>
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <option value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"
                        <?= ($fornecedor['id_fornecedor'] == $remedio['id_fornecedor']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Salvar Alterações</button>
            <button type="button" onclick="window.location.href='buscar_remedio.php'">Cancelar</button>
        </form>
    <?php elseif ($id_busca !== ""): ?>
        <p>Nenhum remédio encontrado para o ID informado!</p>
    <?php endif; ?>

    <a href="principal.php">Voltar Para o Menu</a>
</body>
</html>