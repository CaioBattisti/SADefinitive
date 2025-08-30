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
        "Cadastrar"=>["cadastro_usuario.php","cadastro_secretaria.php","cadastro_funcionario.php","cadastro_fornecedor.php","cadastro_remedio.php"],
        "Buscar"=>["buscar_usuario.php","buscar_secretaria.php","buscar_funcionario.php","buscar_fornecedor.php","buscar_remedio.php"],
        "Alterar"=>["alterar_usuario.php","alterar_secretaria.php","alterar_funcionario.php","alterar_fornecedor.php","alterar_remedio.php"],
        "Excluir"=>["excluir_usuario.php","excluir_secretaria.php","excluir_funcionario.php","excluir_fornecedor.php","excluir_remedio.php"]
    ],
    2 => [
        "Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_remedio.php","buscar_funcionario.php","buscar_fornecedor.php"],
        "Alterar"=>["alterar_funcionario.php","alterar_fornecedor.php"]
    ],
    3 => [
        "Cadastrar"=>["cadastro_remedio.php"],
        "Buscar"=>["buscar_cliente.php","buscar_fornecedor.php","buscar_remedio.php"],
        "Alterar"=>["alterar_fornecedor.php","alterar_remedio.php"],
        "Excluir"=>["excluir_remedio.php"]
    ],
    4 => [
        "Cadastrar"=>["cadastro_cliente.php"],
        "Buscar"=>["buscar_remedio.php"],
        "Alterar"=>["alterar_cliente.php"]
    ],
];

// Obtendo as opções disponíveis para o perfil logado
$opcoes_menu = $permissoes[$id_perfil];

// Verifica se o usuário tem permissão de ADM ou Secretária
if ($id_perfil != 1 && $id_perfil != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa a variável de fornecedores
$fornecedores = [];

// Busca todos os fornecedores cadastrados em ordem alfabética
$sql = "SELECT * FROM fornecedor ORDER BY nome_fornecedor ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$fornecedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um id for passado via GET, exclui o fornecedor
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_fornecedor = $_GET['id'];
    
    $sql = "DELETE FROM fornecedor WHERE id_fornecedor = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_fornecedor, PDO::PARAM_INT);

    if($stmt->execute()) {
        echo "<script>alert('Fornecedor excluído com sucesso!');window.location.href='excluir_fornecedor.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o fornecedor.');window.location.href='excluir_fornecedor.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Fornecedor</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <!-- Menu Dropdown -->
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
        <h2 style="margin: 0;">Excluir Fornecedores:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 10%; transform: translateY(-75%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
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