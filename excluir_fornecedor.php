<?php
session_start();
require_once 'conexao.php';

// Verifica se usuário está logado
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
$nome_perfil = $perfil['nome_perfil'];

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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Apenas administrador pode excluir fornecedor
if ($_SESSION['perfil'] == 2) {
    echo "<script>alert('Você não tem Permissão pra excluir esse Fornecedor!');window.location.href='buscar_fornecedor.php';</script>";
    exit();
}

// Exclusão
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "<script>alert('Fornecedor excluído com sucesso!');window.location.href='excluir_fornecedor.php';</script>";
        exit();
    } else {
        echo "Erro ao excluir fornecedor.";
    }
}

// Busca todos os fornecedores para exibir na tabela
$sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>
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
        <h2 style="margin: 0;">Excluir Fornecedor(a):</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <?php if(!empty($fornecedores)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>Empresa</th>
                <th>Ações</th>
            </tr>

            <?php foreach($fornecedores as $fornecedor): ?>
                <tr>
                    <td><?= htmlspecialchars($fornecedor['id_fornecedor']); ?></td>
                    <td><?= htmlspecialchars($fornecedor['nome_fornecedor']); ?></td>
                    <td><?= htmlspecialchars($fornecedor['email']); ?></td>
                    <td><?= htmlspecialchars($fornecedor['telefone']); ?></td>
                    <td><?= htmlspecialchars($fornecedor['nome_empresa']); ?></td>
                    <td>
                        <a href="excluir_fornecedor.php?id=<?= htmlspecialchars($fornecedor['id_fornecedor']); ?>" onclick="return confirm('Tem certeza que deseja excluir este fornecedor?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum fornecedor encontrado.</p>
    <?php endif; ?>
    <a href="principal.php">Voltar para o Menu</a>
</body>
</html>