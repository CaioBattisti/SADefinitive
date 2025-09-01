<?php
session_start();
require_once 'conexao.php';

// Verifica se usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtendo Perfil do Usuário Logado
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil', $id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);

// Definição das Permissões
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

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa variáveis
$remedio = null;
$busca_remedio = "";

// Se recebeu ID pela URL (via buscar_remedio.php) ou via formulário de busca
if (isset($_GET['id']) || isset($_POST['busca_remedio'])) {
    $busca = isset($_GET['id']) ? $_GET['id'] : $_POST['busca_remedio'];
    $busca_remedio = $busca;

    $stmt = $pdo->prepare("SELECT * FROM remedio WHERE id_remedio = :busca");
    $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    $stmt->execute();
    $remedio = $stmt->fetch(PDO::FETCH_ASSOC);
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
    <title>Alterar Remédio</title>
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
                        <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php"))) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>

<div style="position: relative; text-align: center; margin: 20px 0;">
    <h2 style="margin: 0;">Alterar Remédio:</h2>
    <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
        <form action="logout.php" method="POST">
            <button type="submit">Logout</button>
        </form>
    </div>
</div>

<form method="POST" action="alterar_remedio.php">
    <label for="busca_remedio">Digite o ID do Remédio:</label>
    <input type="number" name="busca_remedio" id="busca_remedio" value="<?= htmlspecialchars($busca_remedio) ?>">
    <button type="submit">Buscar</button>
</form>
<br>

<?php if ($remedio): ?>
<form method="POST" action="processa_alteracao_remedio.php">
    <input type="hidden" name="id_remedio" value="<?= $remedio['id_remedio'] ?>">

    <label>Nome do Remédio:</label>
    <input type="text" name="nome_remedio" value="<?= htmlspecialchars($remedio['nome_remedio']) ?>" required>

    <label>Descrição:</label>
    <input type="text" name="descricao" value="<?= htmlspecialchars($remedio['descricao']) ?>">

    <label>Validade:</label>
    <input type="date" name="validade" value="<?= htmlspecialchars($remedio['validade']) ?>" required>

    <label>Quantidade em Estoque:</label>
    <input type="number" name="qnt_estoque" value="<?= htmlspecialchars($remedio['qnt_estoque']) ?>" required>
    
    <label>Preço Unitário:</label>
    <input type="number" step="0.01" name="preco_unit" value="<?= htmlspecialchars($remedio['preco_unit']) ?>" required>

    <label for="id_fornecedor">Fornecedor:</label>
    <select id="id_fornecedor" name="id_fornecedor" required>
        <?php foreach ($fornecedores as $fornecedor): ?>
            <option value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>" 
                <?= $remedio['id_fornecedor'] == $fornecedor['id_fornecedor'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    
    <button type="submit">Salvar Alterações</button>
    <button type="button" onclick="window.location.href='buscar_remedio.php'">Cancelar</button>
</form>
<?php elseif ($busca_remedio !== ""): ?>
    <p>Nenhum remédio encontrado para o ID informado!</p>
<?php endif; ?>

<a href="principal.php">Voltar Para o Menu</a>
</body>
</html>