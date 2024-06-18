<div id="content">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card shadow mb-3" style="border: none; background: #f7f7f7;">
                <div class="card-body" style="border: none;">
                    <div class="d-flex">
                        <div style="color: #111211; font-weight: bold" class="col-8 d-flex align-items-center" role="alert">
                            Quantidades Pendentes
                        </div>
                        <div class="col-4 d-flex justify-content-end">
                            <button id="limpar_bd" class="" onclick="limparBanco()" style="margin-right: 10px; background: #e95858; color: white; width: 210px; height: 35px; border: none">LIMPAR BANCO</button>

                            <button id="registrar_dados" class="" onclick="downloadExcelModel()" style="margin-right: 10px; background: #4f4f4f; color: white; width: 210px; height: 35px; border: none">BAIXAR DADOS</button>

                        </div>
                    </div>
                </div>
            </div>
            <div id="alertMessages"></div>
            <div class="card shadow" style="border: none; background: #181818;">
                <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                    <p class="m-0 fw-bold" style="color: #777777;"></p>
                </div>
                <div class="card-body" style="border: none; background: #f3f3f3;">
                    <div class="table-responsive table mt-2" id="dataTableContainer" role="grid" aria-describedby="dataTable_info">
                        <table class="table my-0" id="dataTable">
                            <thead>
                                <tr>
                                    <th style="font-size: 12px;">ID
                                        
                                    </th>
                                    <th style="font-size: 12px;">CODIGO
                                        
                                    </th>
                                    <th style="font-size: 12px;">DESCRIÇÃO
                                        
                                    </th>
                                    <th style="font-size: 12px;">REF
                                        
                                    </th>
                                    <th style="font-size: 12px;">QTD
                                       
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $GetDados2 = ("SELECT * FROM bd_pendencia ");
                                    $GetDadosquery2 = mysqli_query($conecta, $GetDados2) or mysqli_error($conecta);
                                    $row2 = mysqli_num_rows ($GetDadosquery2);
                                    if ($row2 > 0) {
                                        while ($GetDadosline2 = mysqli_fetch_array($GetDadosquery2)) {     
                                            $ID = $GetDadosline2 ['ID'];
                                            $CODIGO = $GetDadosline2 ['CODIGO'];
                                            $DESCRICAO = $GetDadosline2 ['DESCRICAO'];
                                            $REF = $GetDadosline2 ['REF'];
                                            $QTD = $GetDadosline2 ['QTD_PENDENTE'];
                                ?>
                                <tr>
                                    <td class="text-center" style="font-size: 11px; width: 5%"><?php echo $ID; ?></td>
                                    <td style="font-size: 11px; width: 5%"><?php echo $CODIGO; ?></td>
                                    <td style="font-size: 11px; width: 80px"><?php echo $DESCRICAO; ?></td>
                                    <td style="font-size: 11px; width: 80px"><?php echo $REF; ?></td>
                                    <td style="font-size: 11px; width: 10%;text-align: right">
                                        <?php echo number_format($QTD, 3, ',', '.'); ?>
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
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<script>
    $(document).ready(function () {
        // Inicialize a DataTable com personalizações de idioma
        $('#dataTable').DataTable({
            "order": [[0, 'asc']], // Ordena por ID ascendente
            "pageLength": 10, // Defina o número de linhas por página
            "lengthMenu": [10, 25, 50, 75, 100, -1], // Opções de quantidade de linhas por página
            "language": {
                "lengthMenu": "Mostrar _MENU_ entradas por página",
                "zeroRecords": "Nenhum resultado encontrado",
                "info": "Mostrando página _PAGE_ de _PAGES_",
                "infoEmpty": "Sem registros disponíveis",
                "infoFiltered": "(filtrado de um total de _MAX_ registros)",
                "search": "Pesquisar:",
                "paginate": {
                    "first": "Primeira",
                    "last": "Última",
                    "next": "Próxima",
                    "previous": "Anterior"
                }
            }
        });
    });


    $(document).ready(function () {
        $("#registrar_dados").on("click", function () {
            exportToExcel();
        });

        function exportToExcel() {
            /* Obtenha os dados filtrados da tabela e coloque em um array */
            var data = [];
            var headers = [];

            // Obtenha os cabeçalhos da tabela
            $("#dataTable thead tr th").each(function () {
                headers.push($(this).text().trim()); // Remova espaços em branco
            });
            data.push(headers);

            // Obtenha os dados das linhas visíveis
            $("#dataTable tbody tr:visible").each(function () {
                var rowData = [];
                $(this).find('td').each(function () {
                    rowData.push($(this).text().trim()); // Remova espaços em branco
                });
                data.push(rowData);
            });

            /* Crie um objeto de folha de cálculo e adicione os dados */
            var ws = XLSX.utils.aoa_to_sheet(data);
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Planilha");

            /* Salve o arquivo Excel */
            XLSX.writeFile(wb, "dados_pendencia.xlsx");
        }

        // Para cada input de filtro
        $('input[type="text"]').on('input', function () {
            // Obtém o valor do input
            var value = $(this).val().toLowerCase();

            // Obtém o índice da coluna
            var index = $(this).closest('th').index();

            // Filtra as linhas da tabela
            $('table tbody tr').filter(function () {
                // Obtém o texto da célula na coluna correspondente
                var cellText = $(this).find('td').eq(index).text().toLowerCase();

                // Exibe ou oculta a linha com base na correspondência do texto
                $(this).toggle(cellText.indexOf(value) > -1);
            });
        });
    });


    function limparBanco() {
        // Exibe um diálogo de confirmação
        var confirmacao = confirm("Tem certeza de que deseja limpar o banco?");

        // Se o usuário clicar em "OK", prossiga com a limpeza
        if (confirmacao) {
            // Faça uma requisição AJAX para seu script PHP que limpa a tabela bd_pendencia
            $.ajax({
                url: 'php/funcoes/scripts/limpar_bd_pendencia.php',
                type: 'POST',
                success: function(response) {
                    // Atualize a tabela ou faça outras ações necessárias
                    alert("Banco limpo com sucesso!");
                    location.reload();  // Recarrega a página
                },
                error: function(error) {
                    console.error("Erro ao limpar o banco: " + error);
                    alert("Erro ao limpar o banco. Consulte o console para obter detalhes.");
                }
            });
        }
    }


</script>

            
<!-- FOOTER -->
