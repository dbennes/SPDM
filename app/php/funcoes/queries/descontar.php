<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>EXBANK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  
</head>

<body>
    

<?php

    session_start();

    //Conecta ao Banco
    include ('../../../../php/conect/conect.php');

    //Verifica o Login
    include('../../../../php/funcoes/login/verificalogin.php');

    $cpf = $_POST['cpf'];
    $valor = $_POST['valor'];
    $jogo = $_POST['jogo'];

    //VERIFICA DADOS DO CARTAO ENVIADO DE ACORDO COM A NUMERACAO
    //PROCURA O DONO PARA VERIFICAR SALDO
    $GetDados2 = ("SELECT * FROM user WHERE cpf = '$cpf'");
    $GetDadosquery2 = mysqli_query($conecta, $GetDados2) or mysqli_error($conecta);
    $row2 = mysqli_num_rows ($GetDadosquery2);
    

    if ($row2 > 0) {
        while ($GetDadosline2 = mysqli_fetch_array($GetDadosquery2)) {     
            $saldo = $GetDadosline2 ['saldo'];
            $acesso = $GetDadosline2 ['acesso'];
        }
        
        //VERIFICAR ESSA PARTE DE ACESSO
        if ($acesso>=0) {

            //FAZ O DEBITO DO VALOR DO SALDO MENOS VALOR ENVIADO
            $novo_total = $saldo - $valor;
            //echo $novo_total . "<br>";
    
            if ($saldo >= $valor) {

            ?>

                <section class="py-4 py-xl-5" style="width: 100%;height: 100VH;background: #70ff87;">
                    <div class="container h-100">
                        <div class="row h-100">
                            <div class="col-md-10 col-xl-8 text-center d-flex d-sm-flex d-md-flex justify-content-center align-items-center mx-auto justify-content-md-start align-items-md-center justify-content-xl-center">
                                <div>

            <?php
                //INSERE NA TABELA EXTRATO OS DADOS
                $sql = "INSERT INTO extrato(cpf, jogo, valor_descontado) VALUES ('$cpf','$jogo','$valor') ";

                if (mysqli_query($conecta, $sql)) {

                    $cor= "color: rgb(255,255,255);";
                    $alerta= "VALOR DESCONTADO!";

                    echo "<h2 class='text-uppercase fw-bold ' style='$cor;'>R$ $novo_total<br/></h2>";
                    echo "<h2 class='text-uppercase fw-bold mb-3' style='$cor'>$alerta<br/></h2>";

                } else {

                    echo "Erro para gravar: " . mysqli_error($conecta);

                }

                //DESCONTA NA TABELA USER O SALDO
                $sql_descontar = "UPDATE user SET saldo='$novo_total' WHERE cpf = '$cpf' ";

                if (mysqli_query($conecta, $sql_descontar)) {

                    //echo "VALOR DESCONTADO!";

                } else {

                    echo "Erro para gravar: " . mysqli_error($conecta);

                }

            }else{

            ?>

                <section class="py-4 py-xl-5" style="width: 100%;height: 100VH;background: #ff7070;">
                    <div class="container h-100">
                        <div class="row h-100">
                            <div class="col-md-10 col-xl-8 text-center d-flex d-sm-flex d-md-flex justify-content-center align-items-center mx-auto justify-content-md-start align-items-md-center justify-content-xl-center">
                                <div>

            <?php

                $cor= "color: rgb(255,255,255);";
                $alerta= "Saldo Insuficiente!";

                echo "<h2 class='text-uppercase fw-bold ' style='$cor;'>R$ $saldo<br/></h2>";
                echo "<h2 class='text-uppercase fw-bold mb-4' style='$cor'>$alerta<br/></h2>";

            }
            

            //header('Location: ../../../perfil.php');

            //mysqli_close($conecta);
        }else{
            echo "sem acesso";
        }
}else{

    echo "<h2 class='text-uppercase fw-bold mb-4' >Conta não encontrada!<br/></h2>";;
}




?>

                <a class="btn btn-light fs-5 me-2 py-2 px-4" role="button" href="../../../adm.php">VOLTAR</a>
                <a class="btn fs-5 py-2 px-4" role="button" href="../../../adm.php" style="border: 2px solid #ffffff;color: rgb(255,255,255);">RECARREGAR</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>


</html>