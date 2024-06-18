<?php

session_start();

include ('../../conect/conect.php');

if(empty($_POST['email']) || empty($_POST['senha'])){
    header('Location: ../../../login.php');
    exit();
}

$email = mysqli_real_escape_string($conecta, $_POST['email']);
$senha = mysqli_real_escape_string($conecta, $_POST['senha']);

$query = "SELECT * FROM user WHERE USUARIO = '{$email}' AND SENHA = md5('{$senha}')";
$result = mysqli_query($conecta, $query) or die ("Não foi possivel conectar.2");
$row = mysqli_num_rows ($result);

if($row == 1){

    while ($GetDadosline = mysqli_fetch_array($result)) {
        //$nome = $GetDadosline ['nome'];
        //$saldo = $GetDadosline ['saldo'];
        //$cpf = $GetDadosline ['cpf'];
       
        
    }

    $_SESSION ['email'] = $email;
    $_SESSION ['nome'] = $nome;
    //$_SESSION ['acesso'] = $acesso;
    
    echo $nome;

    header('Location: ../../../app/estoque.php');
    exit();
}else{
    $_SESSION['nao_autenticado'] = true;
    header('Location: ../../../login.php');
    exit();
}

?>