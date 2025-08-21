<?php 
session_start();
require_once 'conexao.php';

//VERIFICA SE O USUARIO TEM PERMISSÃO DE ADM
if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2 && $_SESSION['perfil']!=3){
    echo "<script>alert('Acesso negado!');window.location.href='principal.php';</script>";
    exit();
}

//INICIALIZA VARIAVEIS
$produto = null;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(!empty($_POST['busca_produto'])){
        $busca = trim($_POST['busca_produto']);

        // VERIFICA SE A BUSCA É UM NÚMERO (id) OU UM nome
        if(is_numeric($busca)){
            $sql = "SELECT * FROM produto WHERE id_produto = :busca";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':busca',$busca,PDO::PARAM_INT);
        }else{
            $sql="SELECT * FROM produto WHERE nome_prod LIKE :busca_produto ORDER BY nome_prod ASC" ;
            $stmt=$pdo->prepare($sql);
            $stmt->bindValue(':busca_produto',"$busca%",PDO::PARAM_STR);
        }
        $stmt->execute();
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        //SE O USUARIO NÃO FOR ENCONTRADO, EXBIBE UM ALERTA
        if(!$produto){
            echo "<script>alert('Produto não encontrado!');</script>";
        }
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
    <title>Alterar Produto</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <script src="bootstrap/js/bootstrap.bundle.min.js"></script>
    <link rel = "stylesheet" href = "styles.css">
    <script src="scripts.js"></script>
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
    <h2>Alterar Produto</h2>

    <form action="alterar_produto.php" method="POST">
        <label for="busca_produto">Digite o id ou nome do produto</label>
        <input type="text" id="busca_produto" name="busca_produto" required onkeyup="buscarSugestoes()">
        <!-- div para exibir sugestoes de usuarios -->
        <div id="sugestoes"></div>
        <button type="submit">Buscar</button>
    </form>

    <?php if($produto): ?>
        <!-- FORMULARIO PARA ALTERAR PRODUTO-->
        <form action="processa_alteracao_produto.php" method="POST">

            <input type="hidden" name="id_produto" value="<?=htmlspecialchars($produto['id_produto'])?>">

            <label for="nome_prod">Nome Produto:</label>
            <input type="text" id="nome_prod" name="nome_prod" value="<?=htmlspecialchars($produto['nome_prod'])?>" required>

            <label for="descricao">Descrição:</label>
            <input type="text" id="descricao" name="descricao" value="<?=htmlspecialchars($produto['descricao'])?>" required>

            <label for="quantidade">Quantidade:</label>
            <input type="number" id="quantidade" name="quantidade" value="<?=htmlspecialchars($produto['qtde'])?>" required>

            <label for="valor">Valor unitario:</label>
            <input type="text" id="valor" name="valor" value="<?=htmlspecialchars($produto['valor_unit'])?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');" / required>


            <button type="submit">Alterar</button>
            <button type="reset">Cancelar</button>
        </form>
        <?php endif;?>
        <a href="principal.php">Voltar</a>
        <center> <address> Matheus dela libera dos anjos/ Estudante / Tecnico em Deenvolvimento de Sistemas </address> </center>

</body>
</html>