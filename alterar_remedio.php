<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário tem permissão de ADM ou Secretária
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Obtendo o Nome do usuario do Usuario Logado
$id_usuario = $_SESSION['usuario'];
$sqlusuario = "SELECT nome FROM usuario WHERE id_usuario = :id_usuario";
$stmtusuario = $pdo->prepare($sqlusuario);
$stmtusuario->bindParam(':id_usuario', $id_usuario);
$stmtusuario->execute();
$perfil = $stmtusuario->fetch(PDO::FETCH_ASSOC);

// Definição das Permissões por usuario
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

// Obtendo as Opções Disponíveis para o usuario Logado
$opcoes_menu = $permissoes[$_SESSION['perfil']];
// Verifica se o usuario tem permissão de ADM
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "Acesso Negado";
    exit();
}
// Inicializa variável
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
$sql_fornecedores = "SELECT id_fornecedor, nome_empresa FROM fornecedor ORDER BY nome_empresa";
$stmt_fornecedores = $pdo->prepare($sql_fornecedores);
$stmt_fornecedores->execute();
$fornecedores = $stmt_fornecedores->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Remédio</title>
    <link rel="stylesheet" href="Estilo/styles.css">
    <link rel="stylesheet" href="Estilo/style.css">
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
        <h2 style="margin: 0;">Alterar Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <form action="alterar_remedio.php" method="POST">
        <label for="busca_remedio">Digite o ID ou Nome:</label>
        <input type="text" id="busca_remedio" name="busca_remedio" value="<?= htmlspecialchars($busca_remedio) ?>" required>
        <button type="submit">Buscar</button>
    </form>
    
    <?php if ($remedio): ?>
        <form action="processa_alteracao_remedio.php" method="POST">
            <input type="hidden" name="id_remedio" value="<?= htmlspecialchars($remedio['id_remedio']) ?>">
            <br>
            <label for="nome_remedio">Nome:</label> 
            <input type="text" id="nome_remedio" name="nome_remedio" value="<?= htmlspecialchars($remedio['nome_remedio']) ?>" required>
            <br>
            <label for="descricao">Descrição:</label> 
            <input type="text" id="descricao" name="descricao" value="<?= htmlspecialchars($remedio['descricao']) ?>" required>
            <br>
            <label for="validade">Validade:</label>
            <input type="date" id="validade" name="validade" value="<?= htmlspecialchars($remedio['validade']) ?>" required>
            <br>
            <label for="qnt_estoque">Quantidade:</label> 
            <input type="number" id="qnt_estoque" name="qnt_estoque" value="<?= htmlspecialchars($remedio['qnt_estoque']) ?>" required>
            <br>
            <label for="preco_unit">Preço Unitário:</label> 
            <input type="number" step="0.01" id="preco_unit" name="preco_unit" value="<?= htmlspecialchars($remedio['preco_unit']) ?>" required>
            <br>
            
            <label for="tipo">Tipo:</label>
            <select id="tipo" name="tipo" required>
                <option value="Comprimido" <?= ($remedio['tipo'] == 'Comprimido') ? 'selected' : '' ?>>Comprimido</option>
                <option value="Gota" <?= ($remedio['tipo'] == 'Gota') ? 'selected' : '' ?>>Gota</option>
                <option value="Creme" <?= ($remedio['tipo'] == 'Creme') ? 'selected' : '' ?>>Creme</option>
                <option value="Injeção" <?= ($remedio['tipo'] == 'Injeção') ? 'selected' : '' ?>>Injeção</option>
                <option value="Inalação" <?= ($remedio['tipo'] == 'Inalação') ? 'selected' : '' ?>>Inalação</option>
            </select>

            <label for="id_fornecedor">Empresas:</label>
            <select id="id_fornecedor" name="id_fornecedor" required>
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <option value="<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>"
                        <?= $remedio['id_fornecedor'] == $fornecedor['id_fornecedor'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($fornecedor['nome_empresa']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca_remedio'])): ?>
        <a href="alterar_remedio.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>