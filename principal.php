<?php
session_start();
require_once 'conexao.php';

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

// Obtendo as Opções Disponíveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estoque de Farmácia</title>
    <link rel="stylesheet" href="Estilo/styles.css">
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="Mascara/scripts.js"></script>
</head>
<body>
    <header class="conteudo">
        <h1><i class="fa-solid fa-prescription-bottle-medical"></i> Sistema de Estoque da Farmácia</h1>
    </header>
    <header class="topo">
        <div class="saudacao">
            <h2><i class="fa-solid fa-user"></i> Bem-vindo, <?php echo $_SESSION["usuario"]; ?></h2>
            <h3><i class="fa-solid fa-id-badge"></i> <?php echo $nome_perfil; ?></h3>
        </div>
        <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
            <p style="font-size: 18px;">
                Gerenciamento de <b>remédios</b>, <b>fornecedores</b>, <b>funcionários</b> e <b>usuários</b> de forma eficiente e organizada.
            </p>
        </div>
        <div class="logout" style="right: 0; top: 100%; translate: 3% -200%;">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </header>

    <div class="layout">
        <nav class="sidebar">
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
    </div>
</body>
</html>