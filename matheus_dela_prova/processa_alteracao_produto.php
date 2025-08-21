<?php 
session_start();
require 'conexao.php';

if($_SESSION['perfil']!=1 && $_SESSION['perfil']!=2 && $_SESSION['perfil']!=3){
    echo "<script>alert('Acesso Negado');window.location.href='principal.php'</script>";
    exit();
}

if($_SERVER["REQUEST_METHOD"]=="POST"){
    $id_produto = $_POST['id_produto'];
    $nomeprod = $_POST['nome_prod'];
    $descricao = $_POST['descricao'];
    $quantidade = $_POST['quantidade'];
    $valor = $_POST['valor'];

//ATUALIZA OS DADOS DO USUARIO

    $sql = "UPDATE produto SET nome = :nomeprod, descricao = :descricao, qntd = :quantidade, valor_unit =:valor WHERE id_produto = :id";
    $stmt = $pdo->prepare($sql);
    
    $stmt->bindParam(':nome_prod', $nomeProd);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':quantidade', $quantidade);
    $stmt->bindParam(':valor', $valor);
    $stmt->bindParam(':id', $id_produto, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script>alert('Usuário atualizado com sucesso!');window.location.href='buscar_usuario.php'</script>";
    } else {
        echo "<script>alert('Erro ao atualizar usuário!');window.location.href='alterar_usuario.php?id=$id_usuario';</script>";
    }
}
?>