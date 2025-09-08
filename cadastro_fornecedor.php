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

// Definição das Permissões por Perfil (idêntico ao cadastrar_usuario.php)
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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Apenas Administrador pode cadastrar fornecedor
if ($_SESSION['perfil'] != 1) {
    echo "Acesso Negado";
    exit();
}

// Processa o formulário
if($_SERVER['REQUEST_METHOD'] == "POST"){
    $nome = trim($_POST['nome_fornecedor']);
    $endereco = trim($_POST['endereco']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $nome_empresa = trim($_POST['nome_empresa']);

    // Aqui estava: $permissao = "fornecedor";
    // Corrigido para valor válido da FK na tabela perfil:
    $permissao = "Fornecedor: Nível de Muito Baixo Acesso";

    $errors = [];

    if (!preg_match("/^[A-Za-zÀ-ÿ\s]+$/", $nome)) {
        $errors[] = "O nome do Fornecedor não pode conter números ou caracteres especiais!";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Digite um email válido!";
    }

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fornecedor WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    if ($stmt->fetchColumn() > 0) {
        $errors[] = "Este email já está cadastrado!";
    }

    if (!empty($errors)) {
        // Corrigido para imprimir o alert corretamente em JavaScript
        echo "<script>alert('". implode("\\n", $errors) ."');history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO fornecedor (nome_fornecedor, endereco, telefone, email, nome_empresa, permissao) 
            VALUES (:nome, :endereco, :telefone, :email, :nome_empresa, :permissao)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':endereco', $endereco);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':nome_empresa', $nome_empresa);
    $stmt->bindParam(':permissao', $permissao);

    if($stmt->execute()){
        echo "<script>alert('Fornecedor cadastrado com sucesso!');window.location.href='cadastro_fornecedor.php';</script>";
    }else{
        echo "<script>alert('Erro ao cadastrar fornecedor!');history.back();</script>";
    }
}
?>
<!DOCTYPE ahtml>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Fornecedor</title>
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
        <h2 style="margin: 0;">Cadastro de fornecedor(a):</h2>
        <div class="logout" style="position: absolute; right: 0; top: 100%; transform: translateY(-70%);">
            <a href="logout.php">
                <button type="button"><i class="fa-solid fa-right-from-bracket"></i> Logout</button>
            </a>
        </div>
    </div>

    <form action="cadastro_fornecedor.php" method="POST">
        <label>Nome do Fornecedor(a):</label>
        <input type="text" name="nome_fornecedor" required>
        <label>Nome da Empresa:</label>
        <input type="text" name="nome_empresa">
        <label>Endereço:</label>
        <input type="text" name="endereco">
        <label>Telefone:</label>
        <input type="text" id="telefone" name="telefone" placeholder="(XX) XXXXX-XXXX" maxlength="15" required>
        <label>Email:</label>
        <input type="email" name="email" required>

        <button type="submit"><i class="fa-solid fa-check"></i> Salvar</button>
        <button type="reset"><i class="fa-solid fa-ban"></i> Cancelar</button>
    </form>

    <a href="principal.php">Voltar para o Menu</a>

<script>
    const telefoneInput = document.getElementById('telefone');

    telefoneInput.addEventListener('input', function(e) {
        let valor = telefoneInput.value;

        // Remove tudo que não for número
        valor = valor.replace(/\D/g, '');

        // Limita o tamanho máximo (2 para DDD + 9 para número)
        if (valor.length > 11) {
            valor = valor.substring(0, 11);
        }

        // Monta a máscara: (+XX) XXXXX-XXXX
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
</script>
</body>
</html>