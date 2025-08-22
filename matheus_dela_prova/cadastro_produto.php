<?php 
session_start();
require_once 'conexao.php';


$sqlFornecedores = "SELECT id_fornecedor, nome_fornecedor FROM fornecedor";
$stmtFornecedores = $pdo->prepare($sqlFornecedores);
$stmtFornecedores->execute();
$fornecedores = $stmtFornecedores->fetchAll(PDO::FETCH_ASSOC);

//VERIFICA SE O USUARIO TEM PERMISSAO
 

if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=3){
    echo "<script>alert('Acesso Negado');window.location.href='principal.php'</script>";
    exit();
}


if($_SERVER["REQUEST_METHOD"]=="POST"){
    $nome_prod = $_POST['nome_prod'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor'];
    $fornecedor_id = $_POST['fornecedor'];

    $sql="INSERT INTO produto(nome_prod,descricao,qtde,valor_unit) VALUES (:nome_prod,:descricao,:quantidade,:valor)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome_prod',$nome_prod);
    $stmt->bindParam(':descricao',$descricao);
    $stmt->bindParam(':quantidade',$quantidade);
    $stmt->bindParam(':valor',$valor);

    if($stmt->execute()){
        echo "<script>alert('Produto cadastrado com sucesso!');</script>";
        $produto_id = $pdo->lastInsertId();

        $sqlFornecedorProduto = "INSERT INTO fornecedor_produto(id_produto, id_fornecedor) VALUES (:id_produto, :id_fornecedor)";
        $stmtFP = $pdo->prepare($sqlFornecedorProduto);
        $stmtFP->bindParam(':id_produto', $produto_id);
        $stmtFP->bindParam(':id_fornecedor', $fornecedor_id);
        $stmtFP->execute();
    }else{
        echo "<script>alert('Erro ao cadastrar produto!');</script>";
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

$opcoes_menu = $permissoes[$id_perfil];


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Produto</title>
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
    <h2>Cadastrar Produto</h2>
    <form action="cadastro_produto.php" method="POST">

        <label for="nome_prod">Nome do produto:</label>
        <input type="text" id="nome_prod" name="nome_prod" required 
       oninput="this.value = this.value.replace(/[^a-zA-ZÀ-ÿ\s]/g,'')">

        
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" required>

        <label for="quantidade">Quantidade:</label>
        <input type="text" id="quantidade" name="quantidade" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>

        <label for="valor">Valor unitario:</label>
        <input type="text" id="valor" name="valor" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" / required>

        <label for="fornecedor">Fornecedor:</label>
    <select id="fornecedor" name="fornecedor" required>
        <option value="">Selecione um fornecedor</option>
        <?php foreach($fornecedores as $fornecedor): ?>
            <option value="<?= $fornecedor['id_fornecedor'] ?>">
                <?= htmlspecialchars($fornecedor['nome_fornecedor']) ?>
            </option>
        <?php endforeach; ?>
    </select>

        <button type="submit">Salvar</button>
        <button type="reset">Cancelar</button>
    </form>

    <a href="principal.php" class="btn btn-outline-primary">Voltar</a>
    <center> <address> Matheus dela libera dos anjos/ Estudante / Tecnico em Deenvolvimento de Sistemas </address> </center>
</body>
</html>