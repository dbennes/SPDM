<?php

    session_start();

    //Conecta ao Banco
    include ('../../../../php/conect/conect.php');

    //Verifica o Login
    include('../../../../php/funcoes/login/verificalogin.php');

    $nome = $_GET['nome'];
    $sobrenome = $_GET['sobrenome'];
    $senha = MD5($_GET['senha']);
    $email = $_GET['email'];   
    $cpf = $_GET['cpf'];    
    
    $saldo = $_GET['saldo'];
    
    $contato = $_GET['contato'];
    $acesso = $_GET['acesso'];

    //echo $nome ;

    $sql = "INSERT INTO 
        user(nome, sobrenome, senha, email, cpf, saldo, contato, acesso) 
        VALUES ('$nome', '$sobrenome', '$senha','$email','$cpf','$saldo','$contato','$acesso')";

    if (mysqli_query($conecta, $sql)) {
        echo "Usuario Criado com Sucesso";
        header('Location: ../../../perfil.php');
    } else {
        echo "Erro para gravar: " . mysqli_error($conecta);
    }
    

?>