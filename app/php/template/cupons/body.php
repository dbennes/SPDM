<div class="container-fluid">
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark mb-0">CUPONS<br>DISPONÍVEIS</h3>
        <p>#THEFUTUREISNOW</p><a class="btn btn-dark btn-sm d-none d-sm-inline-block" role="button" href="#"><i class="fas fa-download fa-sm text-white-50"></i>&nbsp;Generate Report</a>
    </div>
    <div class="row">
        
      <?php
      
            //Variaveis
            $empresa1;
            $cupom;
            $logo;

            //LISTA
            $GetDados1 = ("SELECT * FROM cupons");
            $GetDadosquery1 = mysqli_query($conecta, $GetDados1) or die ("Ainda não disponível.");
            $row1 = mysqli_num_rows ($GetDadosquery1);

            if ($row1 > 0) {
                while ($GetDadosline1 = mysqli_fetch_array($GetDadosquery1)) {
                $empresa1 = $GetDadosline1 ['empresa'];
                $cupom = $GetDadosline1 ['cupom'];
                $logo = $GetDadosline1 ['logo'];
                //$saldo = $GetDadosline ['saldo'];
                //$card = $GetDadosline ['card'];
       
       ?>
      <div class="col-md-6 col-xl-3 mb-4">
            <div class="card shadow border-start-primary py-2">
                <div class="card-body">
                    <div class="row align-items-center no-gutters">
                        <div class="col me-2">
                            <div class="text-uppercase fw-bold text-xs mb-1" style="color: rgb(62,86,173);">
                                <span style="color: rgb(30,30,30);">
                                    <?php echo $empresa1; ?>
                                </span>
                            </div>
                            <div class="text-dark fw-bold h5 mb-0">
                                <span>
                                    <?php echo $cupom; ?>&nbsp;
                                </span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="#">
                                <img src="../app/assets/img/empresas/<?php echo $logo;?>.png" style="width: 50px;height: 50px;">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php 
        
            
        }

     
    }

?>
        
    </div>
</div>