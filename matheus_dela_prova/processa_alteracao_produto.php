<?php 
session_start();
require 'conexao.php';

if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2 && $_SESSION['perfil']!=3){
    echo "<script>alert('Acesso Negado');window.location.href='principal.php'</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $id_produto = $_POST['id_produto'];
    $nomeprod = $_POST['nomeprod'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor'];

    //atualiza no banco de dados
    $sql = "UPDATE produto SET nome_prod = :nomeprod, descricao = :descricao, qtde = :quantidade, valor_unit = :valor WHERE id_produto = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nomeprod', $nomeprod);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Produto atualizado com sucesso!');window.location.href='buscar_produto.php'</script>";
    } else {
        echo "<script>alert('Erro ao atualizar produto!');window.location.href='buscar_produto.php?id=$id_produto';</script>";
    }
}
?>
