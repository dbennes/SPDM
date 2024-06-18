<!DOCTYPE html>
<html lang="pt-br">

    <?php 

        session_start();
        $email = $_SESSION ['email'] ;

        //Head do Site
        include('php/template/head.php'); 

        //Conecta ao Banco
        include ('../php/conect/conect.php');

        //VERIFICA LOGIN
        include('../php/funcoes/login/verificalogin.php');

        // Raizes
        include ('../routes.php');

        //INCLUSAO DE CONSULTA
        include('php/funcoes/queries/consulta.php');

    ?>


<body id="page-top">

    <?php    
        if ($acesso>=2) {   
    ?>
    
            <div id="wrapper">

                <!-- MENU LATERAL -->
                <?php include('php/template/menu.php');?>

                <div class="d-flex flex-column" id="content-wrapper" style="background: #212121;">
                    <div id="content">

                            <!-- MENU TOP -->
                            <?php include('php/template/menu_lateral.php');?>

                            <!-- CONTEUDO -->
                            <?php include('php/template/adc_saldo/body.php');?>
                        
                    </div>
                    
                        <!-- RODAPE -->
                        <?php include('php/template/footer.php');?>

                </div>
                
                <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>

            </div>

    <?php

        }else{
            echo "Você não possui acesso!";
        }
  
    ?>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>