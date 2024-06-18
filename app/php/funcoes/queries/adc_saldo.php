<html lang="pt-br">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no" />
    <title>EXBANK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
  
</head>

<body  style="background: #212121;">
    
<section class="py-4 py-xl-5" style="width: 100%;height: 100VH;background: #212121;">
                    <div class="container h-100">
                        <div class="row h-100">
                            <div class="col-md-10 col-xl-8 text-center d-flex d-sm-flex d-md-flex justify-content-center align-items-center mx-auto justify-content-md-start align-items-md-center justify-content-xl-center">
                                <div>
    
    <?php
    session_start();

    //Conecta ao Banco
    include ('../../../../php/conect/conect.php');

    //Verifica o Login
    include('../../../../php/funcoes/login/verificalogin.php');

    $cpf = $_POST['cpf'];
    $novo_saldo = $_POST['novo_saldo'];
    
    //echo $cpf;
    //echo $novo_saldo;
    
    //$adc_saldo = "UPDATE user SET saldo='$novo_saldo' WHERE cpf= '$cpf' ";

    //AQUI PRECISO VERIFICAR O SALDO PARA SOMAR E NAO SUBSTUIR O VALOR

    $GetDados = ("SELECT * FROM user WHERE cpf = '$cpf'");
    $GetDadosquery = mysqli_query($conecta, $GetDados) or die ("Não foi possivel conectar.");
    $row = mysqli_num_rows ($GetDadosquery);

    if ($row > 0) {
        while ($GetDadosline = mysqli_fetch_array($GetDadosquery)) {      
            $saldo = $GetDadosline ['saldo'];
            
            $novo_saldo_carteira = $saldo+$novo_saldo;
            $adc_saldo = "UPDATE user SET saldo='$novo_saldo_carteira' WHERE cpf= '$cpf' ";
            $add = mysqli_query($conecta,$adc_saldo) or die ("Não foi possivel conectar.");
            
            
        }
    }else{
        $cor= "color: rgb(255,255,255);";
        $alerta= "Usuario não cadastrado!";

        echo "<h2 class='text-uppercase fw-bold mb-4' style='$cor'>$alerta<br/></h2>";
    }

    //echo $saldo+$novo_saldo;
?>



                <a class="btn btn-light fs-5 me-2 py-2 px-4" role="button" href="../../../adc_saldo.php">VOLTAR</a>
                <a class="btn fs-5 py-2 px-4" role="button" href="../../../adc_saldo.php" style="border: 2px solid #ffffff;color: rgb(255,255,255);">RECARREGAR</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
</body>


</html>