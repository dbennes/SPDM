<!-- Certifique-se de incluir a biblioteca jQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<!-- Inclua a biblioteca xlsx -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.17.4/dist/xlsx.full.min.js"></script>
<!-- Inclua a biblioteca DataTables -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>

<div id="content">
    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="card shadow mb-3" style="border: none; background: #f7f7f7;">
                <div class="card-body" style="border: none;">
                    <div class="d-flex">
                        <div style="color: #111211; font-weight: bold" class="col-8 d-flex align-items-center" role="alert">

                            <input type="file" id="fileInput" accept=".xlsx, .xls">

                        </div>


                        <div class="col-4 d-flex justify-content-end">

                            <button onclick="downloadExcelModel()" style="background: #8d8d8d; color: white; width: 210px; height: 35px; border: none; margin-right:10px">MODELO</button>


                            <button id="fileInput" class="" onclick="importExcel()" style="background: #575757; color: white; width: 210px; height: 35px; border: none; margin-right:10px">IMPORTAR</button>

                            <button id="registrar_dados" class="" onclick="sendTablesToPhp()" style="background: #3ca354; color: white; width: 210px; height: 35px; border: none">EXPORTAR</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="alertMessages"></div>
        </div>
        <div class="card shadow" style="border: none; background: #181818;">
            <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                <p class="m-0 fw-bold" style="color: #777777;">Controle de Estoque</p>
            </div>
            <div class="card-body" style="border: none; background: #f3f3f3;">
                <table class="table my-0 dataTable" id="dataTable">
                    <thead>
                        <tr>
                            <th class="text-center" style="font-size: 12px;">ID
                                
                            </th>
                            <th class="text-center" style="font-size: 12px;">CODIGO
                              
                            <th style="font-size: 12px;">DESCRIÇÃO
                               
                            </th>
                            <th class="text-center" style="font-size: 12px;">QTD
                                
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $GetDados2 = ("SELECT * FROM bd_estoque ");
                        $GetDadosquery2 = mysqli_query($conecta, $GetDados2) or mysqli_error($conecta);
                        $row2 = mysqli_num_rows($GetDadosquery2);
                        if ($row2 > 0) {
                            while ($GetDadosline2 = mysqli_fetch_array($GetDadosquery2)) {
                                $ID = $GetDadosline2['ID'];
                                $CODIGO = $GetDadosline2['CODIGO'];
                                $DESCRICAO = $GetDadosline2['DESCRICAO'];
                                $QTD = $GetDadosline2['QTD_DISPONIVEL'];
                        ?>
                                <tr>
                                    <td class="text-center" style="font-size: 11px; width: 5%"><?php echo $ID; ?></td>
                                    <td style="font-size: 11px; width: 10%"><?php echo $CODIGO; ?></td>
                                    <td style="font-size: 11px; width: 50%"><?php echo $DESCRICAO; ?></td>
                                    <td style="font-size: 11px; width: 10%;text-align: right">
                                        <?php echo number_format($QTD, 3, ',', '.'); ?>
                                    </td>
                                </tr>
                        <?php }
                        } ?>
                    </tbody>
                    <tfoot>
                        <tr></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div><br>

<!-- Adicione este script para exportar para Excel -->
<script>
    $(document).ready(function () {
        // Inicialize a DataTable com personalizações de idioma
        $('#dataTable').DataTable({
            
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
            XLSX.writeFile(wb, "dados_filtrados.xlsx");
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

    function importExcel() {
        // Obtém o arquivo selecionado
        var fileInput = document.getElementById('fileInput');
        var file = fileInput.files[0];

        // Verifica se um arquivo foi selecionado
        if (file) {
            var reader = new FileReader();

            // Lê o conteúdo do arquivo
            reader.onload = function (e) {
                var data = e.target.result;

                // Converte os dados para array
                var workbook = XLSX.read(data, { type: 'binary' });
                var sheet_name_list = workbook.SheetNames;
                var excelData = XLSX.utils.sheet_to_json(workbook.Sheets[sheet_name_list[0]], { header: 1 });

                // Envia os dados para o PHP para processamento
                $.ajax({
                    type: 'POST',
                    url: 'php/funcoes/scripts/import_estoque.php',
                    data: { excelData: JSON.stringify(excelData) },
                    success: function (response) {

                        alert("Estoque registrado com sucesso!");
                        var result = JSON.parse(response);

                        if (result.success) {
                            alert("Dados importados com sucesso!");
                        } else {
                            $('#alertMessages').html(`<div class="alert alert-danger">${result.message}</div>`);
                        }
                    }
                });
            };

            // Lê o arquivo como binário
            reader.readAsBinaryString(file);
        } else {
            alert('Selecione um arquivo para importar.');
        }
    }

    function downloadExcelModel() {
        // Crie um objeto de dados de exemplo para o modelo Excel
        const sampleData = [
            ['CODIGO', 'DESCRICAO', 'QTD_DISPONIVEL'],

            // Adicione mais linhas conforme necessário
        ];

        // Crie uma planilha Excel usando a biblioteca XLSX.js
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(sampleData);
        XLSX.utils.book_append_sheet(wb, ws, 'Modelo');

        // Salve a planilha como um arquivo Excel
        XLSX.writeFile(wb, 'modelo_importacao_estoque.xlsx');
    }
</script>
