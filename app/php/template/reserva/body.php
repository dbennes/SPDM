<div id="content">
    <div class="container-fluid">
        <div class="card shadow" style="border: none; background: #181818;">
            <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                <p class="m-0 fw-bold" style="color: #777777;">RESERVA DE MATERIAIS</p>
            </div>
            <div class="card-body" style="border: none; background: #f3f3f3;">
                
                <div class="table-responsive table mt-2" id="dataTable" role="grid" aria-describedby="dataTable_info" style="height: 350px">
                    <table class="table my-0" id="dataTable">
                        <thead>
                            <tr>
                                <th  style="font-size: 12px;">
                                    <input type="checkbox" aria-label="">
                                </th>
                                <th class='text-center' style="font-size: 12px;">ID
                                    <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">

                                </th>
                                <th  style="font-size: 12px;">CODIGO
                                    <i class="fas fa-filter " style="color: #878787"></i>   
                                    <input type="text" class="form-control">
                                </th>
                                <th  style="font-size: 12px;">DESCRIÇÃO
                                    <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">
                                </th>
                                <th class='text-center' style="font-size: 12px;">QTD
                                <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">
                                
                                </th>

                                <th class='text-center' style="font-size: 12px;">REF
                                <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">
                                </th>
                                <th class='text-center' style="font-size: 12px;">SMA
                                <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">
                                </th>
                                <th class='text-center' style="font-size: 12px;">PRIORIDADE
                                <i class="fas fa-filter " style="color: #878787"></i>
                                    <input type="text" class="form-control">
                                </th>
                                <th class='text-center' style="font-size: 12px;">REGISTRADO
                                    <i class="fas fa-filter " style="color: #878787"></i>
                                    <input  type="date" class="form-control">
                                </th>
                                <th class='text-center' style="font-size: 12px;">TODOS
                                    <button id="registrar_dados" class="" onclick="sendTablesToPhp()" style="background: #878787; color: white; height: 35px; width:35px; border: none">
                                        <i class="fas fa-check" style="color: white;"></i>
                                    </button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            <?php 
                            
                                $GetDados2 = ("SELECT * FROM bd_reserva ");
                                $GetDadosquery2 = mysqli_query($conecta, $GetDados2) or mysqli_error($conecta);
                                $row2 = mysqli_num_rows ($GetDadosquery2);
                            
                                if ($row2 > 0) {
                                    while ($GetDadosline2 = mysqli_fetch_array($GetDadosquery2)) {     
                                        $ID = $GetDadosline2 ['ID'];
                                        
                                        $CODIGO = $GetDadosline2 ['CODIGO'];
                                        $DESCRICAO = $GetDadosline2 ['DESCRICAO'];
                                        $QTD = $GetDadosline2 ['QTD_RESERVADA'];

                                        $REF = $GetDadosline2 ['REF'];
                                        $SMA = $GetDadosline2 ['SMA'];
                                        $PRIORIDADE = $GetDadosline2 ['PRIORIDADE'];
                                        $DATA_SOLICITADA = $GetDadosline2 ['DATA_SOLICITADA'];
                                        
                                        
                                
                            
                            ?>

                            <tr>
                                <td class='text-center' style="font-size: 11px; width: 2%">
                                    <input type="checkbox" aria-label="">
                                </td>
                                <td class='text-center' style="font-size: 11px; width: 5%"><?php echo $ID; ?></td>
                                <td  style="font-size: 11px; width: 15%"><?php echo $CODIGO; ?></td>
                                <td style="font-size: 11px; width: 80px"><?php echo $DESCRICAO; ?></td>
                                <td class='text-center' style="font-size: 11px; width: 10%"><?php echo $QTD; ?></td>

                                <td class='text-center' style="font-size: 11px; width: 10%"><?php echo $REF; ?></td>
                                <td class='text-center' style="font-size: 11px; width: 10%"><?php echo $SMA; ?></td>
                                <td class='text-center' style="font-size: 11px; width: 10%"><?php echo $PRIORIDADE; ?></td>
                                <td class='text-center' style="font-size: 11px; width: 10%"><?php echo date('d/m/Y', strtotime($DATA_SOLICITADA)); ?></td>
                                <td class='text-center' style="font-size: 11px; width: 5%">
                                    <button id="registrar_dados" class="" onclick="sendTablesToPhp()" style="background: #c4f7a3; color: white; height: 35px; width:35px; border: none">
                                    <i class="fas fa-check" style="color: #878787;"></i>
                                    </button>
                                </td>
                            </tr>

                            <?php }} ?>
                        </tbody>
                        <tfoot>
                            <tr></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
            
<!-- FOOTER -->
