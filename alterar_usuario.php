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

// Mapeamento de ícones para as categorias de menu
$icones_menu = [
    "Cadastrar" => "fa-solid fa-plus-circle",
    "Buscar" => "fa-solid fa-search",
    "Alterar" => "fa-solid fa-edit",
    "Excluir" => "fa-solid fa-trash-alt"
];

$opcoes_menu = $permissoes[$id_perfil];

// Inicializa variável
$usuario = null;
$busca_usuario = "";

// Se recebeu ID pela URL (via buscar_usuario.php) ou via formulário de busca
if (isset($_GET['id']) || isset($_POST['busca_usuario'])) {
    $busca = isset($_GET['id']) ? $_GET['id'] : $_POST['busca_usuario'];
    $busca_usuario = $busca;

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE id_usuario = :busca");
    $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar usuario</title>
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
                                    $nome = ucfirst(str_replace("_"," ",basename($arquivo,".php")));
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
    <h2 style="margin: 0;">Alterar Usuário(a):</h2>
    <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-50%);">
        <form action="logout.php" method="POST">
            <button type="submit"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
        </form>
    </div>
</div>

<form method="POST" action="alterar_usuario.php">
    <label for="busca_usuario">Digite o ID do Usuário(a):</label>
    <input type="number" name="busca_usuario" id="busca_usuario" value="<?= htmlspecialchars($busca_usuario) ?>">
    <button type="submit">Buscar</button>
</form>
<br>

<?php if ($usuario): ?>
<form method="POST" action="processa_alteracao_usuario.php">
    <input type="hidden" name="id_usuario" value="<?= $usuario['id_usuario'] ?>">

    <label>Nome do Usuário(a):</label>
    <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>

    <label>Endereço:</label>
    <input type="text" name="email" value="<?= htmlspecialchars($usuario['email']) ?>">

    <label for="id_perfil">Perfil:</label>
        <select id="id_perfil" name="id_perfil">
            <option value="1" <?= $usuario['id_perfil'] == 1 ? 'selected' : '' ?>>Administrador</option>
            <option value="2" <?= $usuario['id_perfil'] == 2 ? 'selected' : '' ?>>Secretária</option>
            <option value="3" <?= $usuario['id_perfil'] == 3 ? 'selected' : '' ?>>Funcionario</option>
            <option value="4" <?= $usuario['id_perfil'] == 4 ? 'selected' : '' ?>>Fornecedor</option>
        </select>

        <?php if ($_SESSION['perfil'] == 1): ?>
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" id="nova_senha" name="nova_senha" placeholder="Digite a nova senha (opcional)">
        <?php endif; ?>

    <button type="submit">Salvar Alterações</button>
    <button type="button" onclick="window.location.href='buscar_usuario.php'">Cancelar</button>
</form>
<?php elseif ($busca_usuario !== ""): ?>
    <p>Nenhum Usuário encontrado para o ID informado!</p>
<?php endif; ?>

<a href="principal.php">Voltar Para o Menu</a>
</body>
</html>