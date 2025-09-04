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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Inicializa a variavel para evitar Erros
$remedios = [];

// Se o Formulário for Enviado, Busca pelo id ou nome do remedio
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])) {
    $busca = trim($_POST['busca']);
    $sql = "SELECT r.*, f.nome_fornecedor FROM remedio r 
            LEFT JOIN fornecedor f ON r.id_fornecedor = f.id_fornecedor 
            WHERE r.id_remedio = :busca OR r.nome_remedio LIKE :busca_nome 
            ORDER BY r.nome_remedio ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':busca', $busca, PDO::PARAM_INT);
    $stmt->bindValue(':busca_nome', "%$busca%", PDO::PARAM_STR);
} else {
    $sql = "SELECT r.*, f.nome_fornecedor FROM remedio r 
            LEFT JOIN fornecedor f ON r.id_fornecedor = f.id_fornecedor 
            ORDER BY r.nome_remedio ASC";
    $stmt = $pdo->prepare($sql);
}

$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Remédios</title>
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
        <h2 style="margin: 0;">Buscar Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form action="buscar_remedio.php" method="POST">
        <label for="busca">Digite o ID ou o Nome do Remédio:</label>
        <input type="text" id="busca" name="busca">
        <button type="submit">Pesquisar</button>
    </form>

    <?php if (!empty($remedios)): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Validade</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Tipo</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>
        <?php foreach ($remedios as $remedio): ?>
            <tr>
                <td><?= htmlspecialchars($remedio['id_remedio']) ?></td>
                <td><?= htmlspecialchars($remedio['nome_remedio']) ?></td>
                <td><?= htmlspecialchars($remedio['descricao']) ?></td>
                <td><?= htmlspecialchars($remedio['validade']) ?></td>
                <td><?= htmlspecialchars($remedio['qnt_estoque']) ?></td>
                <td><?= htmlspecialchars($remedio['preco_unit']) ?></td>
                <td><?= htmlspecialchars($remedio['tipo']) ?></td>
                <td><?= htmlspecialchars($remedio['nome_fornecedor']) ?></td>
                <td>
                    <a href="alterar_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']) ?>">Alterar</a>
                    <a href="excluir_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']) ?>" onclick="return confirm('Tem certeza que deseja excluir este remédio?')">Excluir</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum Remédio Encontrado.</p>
    <?php endif; ?>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['busca'])): ?>
        <a href="buscar_remedio.php">Voltar</a>
    <?php else: ?>
        <a href="principal.php">Voltar para o Menu</a>
    <?php endif; ?>
</body>
</html>