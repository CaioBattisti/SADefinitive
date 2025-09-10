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

$opcoes_menu = $permissoes[$id_perfil];

// Verifica se o usuário tem permissão de ADM

// Inicializa variáveis
$funcionario = null;
$id_busca = "";

// Se recebeu ID pela URL (via buscar_funcionario.php) ou via formulário de busca
if (isset($_GET['id']) || isset($_POST['id_busca'])) {
    $id = isset($_GET['id']) ? $_GET['id'] : $_POST['id_busca'];
    $id_busca = $id;

    $stmt = $pdo->prepare("SELECT * FROM funcionario WHERE id_funcionario = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Busca todos os perfis para o dropdown
$stmtPerfis = $pdo->query("SELECT nome_perfil FROM perfil ORDER BY nome_perfil ASC");
$perfis = $stmtPerfis->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Alterar Funcionário</title>
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
        <h2 style="margin: 0;">Alterar Funcionario(a):</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form method="POST" action="alterar_funcionario.php">
        <label for="id_busca">Digite o ID do Funcionário:</label>
        <input type="number" name="id_busca" id="id_busca" value="<?= htmlspecialchars($id_busca) ?>">
        <button type="submit"><i class="fa-solid fa-search"></i> Buscar <i class="fa-solid fa-search"></i></button>
    </form>
    <br>

    <?php if ($funcionario): ?>
        <form method="POST" action="processa_alterar_funcionario.php">
            <input type="hidden" name="id_funcionario" value="<?= $funcionario['id_funcionario'] ?>">

            <label>Nome do Funcionário(a):</label>
            <input type="text" name="nome_funcionario" value="<?= htmlspecialchars($funcionario['nome_funcionario']) ?>" required>

            <label>Endereço:</label>
            <input type="text" name="endereco" value="<?= htmlspecialchars($funcionario['endereco']) ?>">

            <label>Telefone:</label>
            <input type="text" id="telefone" name="telefone" value="<?= htmlspecialchars($fornecedor['telefone']) ?>">
            <label>Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($funcionario['email']) ?>" required>

            <label>Permissão:</label>
            <select name="permissao" required>
                <?php foreach ($perfis as $p): ?>
                    <option value="<?= htmlspecialchars($p['nome_perfil']) ?>" <?= ($p['nome_perfil'] == $funcionario['permissao']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['nome_perfil']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit"><i class="fa-solid fa-check"></i> Salvar Alterações</button>
            <button type="button" onclick="window.location.href='buscar_funcionario.php'"><i class="fa-solid fa-ban"></i> Cancelar</button>
        </form>
    <?php elseif ($id_busca !== ""): ?>
        <p>Nenhum funcionário encontrado para o ID informado!</p>
    <?php endif; ?>

    <a href="principal.php">Voltar Para o Menu</a>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const telefoneInput = document.getElementById('telefone');

        if (telefoneInput) {
            telefoneInput.addEventListener('input', function (e) {
                let valor = telefoneInput.value;

                // Remove tudo que não for número
                valor = valor.replace(/\D/g, '');

                // Limita o tamanho máximo (2 para DDD + 9 para número)
                if (valor.length > 11) {
                    valor = valor.substring(0, 11);
                }

                // Monta a máscara: (XX) XXXXX-XXXX
                if (valor.length > 0) {
                    valor = '(' + valor;
                }
                if (valor.length > 3) {
                    valor = valor.slice(0, 3) + ') ' + valor.slice(3);
                }
                if (valor.length > 10) {
                    valor = valor.slice(0, 10) + '-' + valor.slice(10);
                }

                telefoneInput.value = valor;
            });
        }
    });
</script>

</body>
</html>