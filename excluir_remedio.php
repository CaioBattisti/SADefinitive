<?php
session_start();
require_once 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

// Obtendo o Nome do Perfil do Usuário Logado
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

// Obtendo as opções disponíveis para o perfil logado
$opcoes_menu = $permissoes[$id_perfil];

if ($_SESSION['perfil'] != 2 && $_SESSION['perfil'] = 3 && $_SESSION['perfil'] = 4) {
    echo "<script>alert('Você não tem Permissão pra excluir esse Remédio!');window.location.href='buscar_remedio.php';</script>";
    exit();
}

// Exclusão
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM remedio WHERE id_remedio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "<script>alert('Remédio excluído com sucesso!');window.location.href='excluir_remedio.php';</script>";
        exit();
    } else {
        echo "Erro ao excluir remédio.";
    }
}

// Busca todos os remédios para exibir na tabela
$sql = "SELECT r.*, f.nome_fornecedor FROM remedio r 
        LEFT JOIN fornecedor f ON r.id_fornecedor = f.id_fornecedor
        ORDER BY r.nome_remedio ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Remédio</title>
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
        <h2 style="margin: 0;">Excluir Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>
    
    <?php if(!empty($remedios)): ?>
        <table border="1">
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
                        <a href="excluir_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']) ?>" onclick="return confirm('Tem certeza que deseja excluir este remédio?')"><i class="fa-solid fa-trash-açt"></i> Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum remédio encontrado.</p>
    <?php endif; ?>
    <a href="principal.php">Voltar para o Menu</a>
</body>
</html>