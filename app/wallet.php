<!DOCTYPE html>
<html lang="pt-br">

    <?php 

        session_start();

        //Head do Site
        include('php/template/head.php'); 

        //Conecta ao Banco
        include ('../php/conect/conect.php');

        // Raizes
        include ('../routes.php');

        //VERIFICA LOGIN
        include('../php/funcoes/login/verificalogin.php'); 

        $email = $_SESSION ['email'] ;
        //$cpf = $_SESSION ['cpf'];

        //INCLUSAO DE CONSULTA
        //include('php/funcoes/queries/consulta.php'); 
       

    ?>

<body id="page-top">
    
    <div id="wrapper">

        <!-- MENU LATERAL -->
        <?php include('php/template/menu.php');?>

        <div class="d-flex flex-column" id="content-wrapper" style="background: #f8f9fa;">
            <div id="content">

                    <!-- MENU TOP -->
                    <?php include('php/template/menu_lateral.php');?>

                    <!-- CONTEUDO -->
                    <?php include('php/template/wallet/body.php');?>
                
            </div>
            
                <!-- RODAPE -->
                <?php include('php/template/footer.php');?>

        </div>
        
        <a class="border rounded d-inline scroll-to-top" href="#page-top"><i class="fas fa-angle-up"></i></a>

    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/theme.js"></script>
</body>

</html>