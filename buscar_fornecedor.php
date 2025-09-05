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

// Definição das Permissões por Perfil (mesma estrutura do buscar_usuario.php)
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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Inicializa a variavel para evitar Erros
$fornecedores = [];

// Se o Formulário for Enviado, Busca pelo id ou nome do fornecedor
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);

    if (is_numeric($busca)) {
        $sql = "SELECT * FROM fornecedor WHERE id_fornecedor = :busca ORDER BY nome_fornecedor ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':busca', $busca, PDO::PARAM_INT);
    } else {
        // Busca apenas pelo PRIMEIRO nome
        $sql = "SELECT * FROM fornecedor
                WHERE SUBSTRING_INDEX(nome_fornecedor, ' ', 1) LIKE :busca_nome
                ORDER BY nome_fornecedor ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':busca_nome', "$busca%", PDO::PARAM_STR);
    }
} else {
    $sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Fornecedores</title>
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
        <h2 style="margin: 0;">Buscar Fornecedor(a):</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form action="buscar_fornecedor.php" method="POST">
        <label for="busca">Digite o ID ou o Primeiro Nome:</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>

    <?php if (!empty($fornecedores)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome do Fornecedor</th>
                <th>Endereço</th>
                <th>Telefone</th>
                <th>Email</th>
                <th>Nome da Empresa</th>
                <th>Ações</th>
            </tr>
        <?php foreach ($fornecedores as $fornecedor): ?>
            <tr>
                <td><?= htmlspecialchars($fornecedor['id_fornecedor']) ?></td>
                <td><?= htmlspecialchars($fornecedor['nome_fornecedor']) ?></td>
                <td><?= htmlspecialchars($fornecedor['endereco']) ?></td>
                <td><?= htmlspecialchars($fornecedor['telefone']) ?></td>
                <td><?= htmlspecialchars($fornecedor['email']) ?></td>
                <td><?= htmlspecialchars($fornecedor['nome_empresa']) ?></td>
                <td>
                    <a href="alterar_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>">Alterar Fornecedor</a>
                    <a href="excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']) ?>" onclick="return confirm('Tem certeza que deseja excluir esse Fornecedor?')">Excluir Fornecedor</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum Fornecedor Encontrado.</p>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])): ?>
        <a href="buscar_fornecedor.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>