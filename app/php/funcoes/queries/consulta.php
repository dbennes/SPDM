<?php

    //Variaveis
    $nome;
    $cpf;
    $saldo;
    $acesso;

    //LISTA
    $GetDados = ("SELECT * FROM user WHERE email = '$email'");
    $GetDadosquery = mysqli_query($conecta, $GetDados) or die ("Não foi possivel conectar.");
    $row = mysqli_num_rows ($GetDadosquery);

    if ($row > 0) {
        while ($GetDadosline = mysqli_fetch_array($GetDadosquery)) {
        $nome = $GetDadosline ['nome'];
        $cpf = $GetDadosline ['cpf'];
        $email = $GetDadosline ['email'];
        $saldo = $GetDadosline ['saldo'];
        $card = $GetDadosline ['hash_cartao'];
        $acesso = $GetDadosline ['acesso'];

    }

    //echo  $nome;
    //echo  $cpf;
    //echo  $emailcpf;
    


    
}

?>