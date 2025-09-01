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
$sqlPerfil = "SELECT nome FROM usuario WHERE id_usuario = :id_usuario";
$stmtPerfil = $pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_usuario', $id_perfil);
$stmtPerfil->execute();

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

// Obtendo as Opções Disponiveis para o Perfil Logado
$opcoes_menu = $permissoes[$id_perfil];

// Verifica permissão
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 3) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Lista todos os remedios
$sql = "SELECT r.*, f.nome_empresa FROM remedio r JOIN fornecedor f ON r.id_fornecedor = f.id_fornecedor ORDER BY r.nome_remedio ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se receber id para excluir
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM remedio WHERE id_remedio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Remédio excluído com sucesso!');window.location.href='excluir_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir remédio.');window.location.href='excluir_remedio.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Excluir remédio</title>
    <link rel="stylesheet" href="Estilo/style.css">
    <link rel="stylesheet" href="Estilo/styles.css">
</head>
<body>
    <nav>
        <ul class="menu">
            <?php foreach ($opcoes_menu as $categoria => $arquivos): ?>
                <li class="dropdown">
                    <a href="#"><?= $categoria ?></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($arquivos as $arquivo): ?>
                            <li><a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_"," ",basename($arquivo,".php"))) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <div style="position: relative; text-align: center; margin: 20px 0;">
        <h2 style="margin: 0;">Excluir remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 0%; transform: translateY(-70%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>
    <?php if (!empty($remedios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Validade</th>
                <th>Tipo</th>
                <th>Quantidade</th>
                <th>Valor Unitário</th>
                <th>Empresas</th>
                <th>Ações</th>
            </tr>
            <?php foreach ($remedios as $remedio): ?>
                <tr>
                    <td><?= htmlspecialchars($remedio['id_remedio']) ?></td>
                    <td><?= htmlspecialchars($remedio['nome_remedio']) ?></td>
                    <td><?= htmlspecialchars($remedio['descricao']) ?></td>
                    <td><?= htmlspecialchars($remedio['validade']) ?></td>
                    <td><?= htmlspecialchars($remedio['tipo']) ?></td>
                    <td><?= htmlspecialchars($remedio['qnt_estoque']) ?></td>
                    <td><?= htmlspecialchars($remedio['preco_unit']) ?></td>
                    <td><?= htmlspecialchars($remedio['nome_empresa']) ?></td>
                    <td>
                        <a href="excluir_remedio.php?id=<?= $remedio['id_remedio'] ?>" onclick="return confirm('Tem certeza que deseja excluir este remédio?')">Excluir</a>
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