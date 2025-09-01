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

// Definição das Permissões por Perfil
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

// Verifica se o usuario tem permissão para excluir remedios
if ($_SESSION['perfil'] != 1 && $_SESSION['perfil'] != 2) {
    echo "<script>alert('Acesso Negado!');window.location.href='principal.php';</script>";
    exit();
}

// Inicializa a variável de remédios
$remedios = [];

// Busca todos os remédios cadastrados em ordem alfabética, incluindo o nome do fornecedor
$sql = "SELECT r.*, f.nome_fornecedor FROM remedio r JOIN fornecedor f ON r.id_fornecedor = f.id_fornecedor ORDER BY r.nome_remedio ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$remedios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Se um id for passado via GET, exclui o remedio
if(isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_remedio = $_GET['id'];
    
    $sql = "DELETE FROM remedio WHERE id_remedio = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id_remedio, PDO::PARAM_INT);

    if($stmt->execute()) {
        echo "<script>alert('Remédio Excluído Com Sucesso!');window.location.href='excluir_remedio.php';</script>";
    } else {
        echo "<script>alert('Erro ao excluir o Remédio.');window.location.href='excluir_remedio.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Remédio</title>
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
        <h2 style="margin: 0;">Excluir Remédios:</h2>
        <div class="logout" style="position: absolute; right: 0; top: 10%; transform: translateY(-75%);">
            <form action="logout.php" method="POST">
                <button type="submit">Logout</button>
            </form>
        </div>
    </div>

    <?php if(!empty($remedios)): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Validade</th>
                <th>Qtd Estoque</th>
                <th>Preço Unit.</th>
                <th>Fornecedor</th>
                <th>Ações</th>
            </tr>

            <?php foreach($remedios as $remedio): ?>
                <tr>
                    <td><?= htmlspecialchars($remedio['id_remedio']); ?></td>
                    <td><?= htmlspecialchars($remedio['nome_remedio']); ?></td>
                    <td><?= htmlspecialchars($remedio['descricao']); ?></td>
                    <td><?= htmlspecialchars($remedio['validade']); ?></td>
                    <td><?= htmlspecialchars($remedio['qnt_estoque']); ?></td>
                    <td><?= htmlspecialchars($remedio['preco_unit']); ?></td>
                    <td><?= htmlspecialchars($remedio['nome_fornecedor']); ?></td>
                    <td>
                        <a href="excluir_remedio.php?id=<?= htmlspecialchars($remedio['id_remedio']); ?>" onclick="return confirm('Tem Certeza que você deseja excluir este remédio?')">Excluir</a>
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