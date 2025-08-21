<?php 
session_start();
require 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSAO
if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2 && $_SESSION['perfil']!=3){
    echo "<script>alert('Acesso Negado');window.location.href='principal.php'</script>";
    exit();
}

//INICIALIZA VARIAVEL PARA ARMAZENAR PRODUTOS
$produtos = [];

//BUSCA TODOS OS PRODUTOS CADASTRADOS EM ORDEM ALFABETICA
$sql = "SELECT * FROM produto ORDER BY nome_prod ASC";
$stmt =$pdo->prepare($sql);
$stmt->execute();
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);

//SE UM ID FOR PASSADO VIA get EXCLUI O PRODUTO
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $id_produto = $_GET['id'];

    //EXCLUI O PRODUTO DO BANCO DE DADOS
    $sql = "DELETE FROM produto WHERE id_produto = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam('id',$id_produto,PDO::PARAM_INT);

    if($stmt->execute()){
        echo "<script>alert('Produto excluido com Sucesso!');window.location.href='excluir_produto.php'</script>";
    }else{
        echo "<script>alert('Erro ao excluir Produto!');</script>";
    }
}
$id_perfil = $_SESSION['perfil'];
$sqlPerfil = "SELECT nome_perfil FROM perfil WHERE id_perfil = :id_perfil";
$stmtPerfil=$pdo->prepare($sqlPerfil);
$stmtPerfil->bindParam(':id_perfil',$id_perfil);
$stmtPerfil->execute();
$perfil = $stmtPerfil->fetch(PDO::FETCH_ASSOC);
$nome_perfil = $perfil['nome_perfil'];
$permissoes = [
    1 => [
        "Cadastrar" => ["cadastro_usuario.php","cadastro_perfil.php","cadastro_cliente.php","cadastro_fornecedor.php","cadastro_produto.php","cadastro_funcionario.php"],
        "Buscar"    => ["buscar_usuario.php","buscar_perfil.php","buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php","buscar_funcionario.php"],
        "Alterar"   => ["alterar_usuario.php","alterar_perfil.php","alterar_cliente.php","alterar_fornecedor.php","alterar_produto.php","alterar_funcionario.php"],
        "Excluir"   => ["excluir_usuario.php","excluir_perfil.php","excluir_cliente.php","excluir_fornecedor.php","excluir_produto.php","excluir_funcionario.php"]
    ],

    2 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],

    3 => [
        "Cadastrar" => ["cadastro_fornecedor.php","cadastro_produto.php"],
        "Buscar"    => ["buscar_cliente.php","buscar_fornecedor.php","buscar_produto.php"],
        "Alterar"   => ["alterar_fornecedor.php","alterar_produto.php"],
        "Excluir"   => ["excluir_produto.php"]
    ],

    4 => [
        "Cadastrar" => ["cadastro_cliente.php"],
        "Buscar"    => ["buscar_produto.php"],
        "Alterar"   => ["alterar_cliente.php"]
    ]
];

// OBTENDO AS OPÇÕES DISPONÍVEIS PARA O PERFIL LOGADO
$opcoes_menu = $permissoes[$id_perfil];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Produto</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel = "stylesheet" href = "styles.css">
    
</head>
<body>
<nav>
        <ul class="menu">
            <?php foreach($opcoes_menu as $categoria =>$arquivos):?>
            <li class="dropdown">
                <a href="#"><?=$categoria?></a>
                <ul class="dropdown-menu">
                    <?php foreach($arquivos as $arquivo):?>
                    <li>
                    <a href="<?= $arquivo ?>"><?= ucfirst(str_replace("_", " ", basename($arquivo, ".php"))); ?></a>
                    </li>
                    <?php endforeach;?>
                </ul>
            </li>
            <?php endforeach;?>
        </ul>
    </nav>
    <h2>Excluir Produto</h2>
    <?php if(!empty($produtos)):?>
        <table class="table table-success table-striped-columns">
            <tr>
                <th>ID</th>
                <th>Nome produto</th>
                <th>Descrição</th>
                <th>Quantidade</th>
                <th>Valor unitario</th>
                <th>Ações</th>
            </tr>
            <?php foreach($produtos as $produto):?>
                <tr>
                    <td><?=htmlspecialchars($produto['id_produto'])?></td>
                    <td><?=htmlspecialchars($produto['nome_prod'])?></td>
                    <td><?=htmlspecialchars($produto['descricao'])?></td>
                    <td><?=htmlspecialchars($produto['qtde'])?></td>
                    <td><?=htmlspecialchars($produto['valor_unit'])?></td>
                    <td><a href="excluir_produto.php?id=<?=htmlspecialchars($produto['id_produto'])?>" onclick="return confirm('Tem certeza que deseja excluir este Produto?')">Excluir</a></td>
                </tr>
                <?php endforeach;?>
        </table>
        <?php else:?>
            <p>Nenhum Produto encontrado</p>
        <?php endif;?>

        <a href="principal.php">Voltar</a>
        <center> <address> Matheus dela libera dos anjos/ Estudante / Tecnico em Deenvolvimento de Sistemas </address> </center>

</body>
</html>