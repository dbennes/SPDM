<div class="container-fluid" style="min-height: 75vh">
    
    <div class="row mb-3">
        
        <div class="col-lg-12">


        

            <div class="card shadow mb-3" style="border: none; background: #f7f7f7;">
                
                <div class="card-body" style="border: none;">
                    <div class=" d-flex ">
                        <div style="color: #111211; font-weight: bold" class="col-8 d-flex align-items-center"  role="alert">
                            Registrar a solicitação de materiais!
                        </div>

                        <div class="col-4 d-flex justify-content-end">
                            
                            <button id="registrar_dados" class="" onclick="downloadExcelModel()" style="margin-right: 10px; background: #4f4f4f; color: white; width: 210px; height: 35px; border: none">BAIXAR MODELO</button>

                            <button id="registrar_dados" class="" onclick="sendTablesToPhp()" style="background: #3ca354; color: white; width: 210px; height: 35px; border: none">REGISTRAR DADOS</button>

                        </div>
                    </div>
                </div>

            </div>


            
            <div id="alertMessages"></div>

        </div>

        <div class="col-lg-12">
        <div class="row d-flex">

            <div class="col-md-5 col-xs-5">

                <div class="col-md-12 col-xs-12">
                    <div class="card shadow mb-3" style="border: none; background: #181818;">
                        <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                            <p class=" m-0 fw-bold">Dados do Material</p>
                        </div>
                        <div class="card-body" style="border: none; background: #f3f3f3;">
                            <div class="row d-flex">

                                <div>
                                <input type="file" id="file" name="file">
                                <button id="importButton" onclick="sendTablesToPhp()">Importar e Exibir</button>

                                <!-- Modal de Carregamento com ajustes -->
                                <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalLabel" aria-hidden="true" style="display: none;">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="loadingModalLabel">Carregando...</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #3ca354;"></i>
                                                <span id="loadingText">Aguarde, carregando...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xs-12">
                    <div class="card shadow mb-3" style="border: none; background: #f3f3f3;">
                        <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                            <p class=" m-0 fw-bold">Dados da Planilha</p>
                        </div>
                        <div class="card-body" style="border: none; background: #f3f3f3;">
                            <div class="row d-flex">

                                <div id="results"></div>

                            </div>

                            </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7 col-xs-7">
                <div class="col-md-12 col-xs-12">
                    <div class="card shadow mb-3" style="border: none; background: #f3f3f3;">
                        <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                            <p class=" m-0 fw-bold">Solicitação de Retirada</p>
                        </div>
                        <div class="card-body" style="border: none; background: #f3f3f3;">
                            <div class="row d-flex">
                                <div id="cart">
                                    
                                    <table class="table my-0" id="cartTable" >
                                        
                                    <tr class="details-row-${ref}" style="display: none;">
                                                <th>CODIGO</th>
                                                <th>QTD</th>
                                                <th>ORDEM</th>
                                                <th>REF</th>
                                                <th>AÇÃO</th>
                                            </tr>
                                       
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="card shadow mb-3" style="border: none; background: #f3f3f3;">
                        <div class="card-header py-3" style="border: none; background: #f7f7f7;">
                            <p class=" m-0 fw-bold">Dados do Estoque</p>
                        </div>
                        <div class="card-body" style="border: none; background: #f3f3f3;">
                            <div class="row d-flex">
                                <div id="estoqueView">
                                    <div id="estoque"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

           <!-- Adicione esta função JavaScript no seu código -->
<script>
    function sendTablesToPhp() {
    // Suponha que você tenha um input de arquivo no seu HTML com id 'file'
    var fileInput = document.getElementById('file');
    var file = fileInput.files[0];

    if (!file) {
        console.error("Selecione um arquivo antes de enviar.");
        return;
    }

    // Use FileReader para ler o arquivo
    var reader = new FileReader();
    reader.onload = function (e) {
        var data = e.target.result;
        var workbook = XLSX.read(data, { type: 'binary' });

        // Suponha que sua planilha tenha apenas uma folha
        var sheetName = workbook.SheetNames[0];
        var sheet = workbook.Sheets[sheetName];

        // Converta a planilha para um array de objetos com nomes de colunas
        var dataArray = XLSX.utils.sheet_to_json(sheet, { header: 1 });

        // Obtenha os nomes das colunas da primeira linha
        var columnNames = dataArray[0];

        // Remova a primeira linha do array, pois ela agora contém os nomes das colunas
        dataArray.shift();

        // Crie um array associativo usando os nomes das colunas
        var finalArray = dataArray.map(function (row) {
            var obj = {};
            for (var i = 0; i < columnNames.length; i++) {
                obj[columnNames[i]] = row[i];
            }
            return obj;
        });

        // Limpar o conteúdo atual da tabela
        $("#cartTable").empty();

        // Adicionar a linha de cabeçalho
        var headerRow = $("<tr>");
        headerRow.append("<th>CODIGO</th>");
        headerRow.append("<th>QTD</th>");
        headerRow.append("<th>ORDEM</th>");
        headerRow.append("<th>REF</th>");
        headerRow.append("<th>AÇÃO</th>");
        $("#cartTable").append(headerRow);

        // Adicionar linhas com base no array
        finalArray.forEach(function (item) {
            var newRow = $("<tr>");
            newRow.append("<td>" + item.CODIGO + "</td>");
            newRow.append("<td>" + item.QTD + "</td>");
            newRow.append("<td>" + item.PRIORIDADE + "</td>");
            newRow.append("<td>" + item.REF + "</td>");
            newRow.append("<td>Ação</td>"); // Você pode personalizar essa coluna conforme necessário
            $("#cartTable").append(newRow);
        });

        // Agora, vamos enviar os dados para o PHP
        $.ajax({
            type: "POST",
            url: "php/funcoes/scripts/processar_arquivo2.php", // Atualize o caminho conforme necessário
            data: { dataArray: finalArray },
            success: function (response) {
                // Lidar com a resposta do servidor (PHP)
                console.log(response);
                // Suponha que 'response' seja o array processado enviado do PHP

                // Limpar o conteúdo atual da tabela
                $("#cartTable").empty();

                // Adicionar a linha de cabeçalho
                var headerRow = $("<tr>");
                headerRow.append("<th>CODIGO</th>");
                headerRow.append("<th>QTD</th>");
                headerRow.append("<th>ORDEM</th>");
                headerRow.append("<th>REF</th>");
                headerRow.append("<th>AÇÃO</th>");
                $("#cartTable").append(headerRow);

                // Adicionar linhas com base no array
                response.forEach(function (item) {
                    var newRow = $("<tr>");
                    newRow.append("<td>" + item.CODIGO + "</td>");
                    newRow.append("<td>" + item.QTD + "</td>");
                    newRow.append("<td>" + item.PRIORIDADE + "</td>");
                    newRow.append("<td>" + item.REF + "</td>");
                    newRow.append("<td>Ação</td>"); // Você pode personalizar essa coluna conforme necessário
                    $("#cartTable").append(newRow);
                });
            },
            error: function (xhr, status, error) {
                // Lidar com erros
                console.error("Erro na requisição AJAX: ", status, error);
            }
        });
    };

    // Leia o arquivo como binário
    reader.readAsBinaryString(file);
}


function downloadExcelModel() {
            // Crie um objeto de dados de exemplo para o modelo Excel
            const sampleData = [
                ['CODIGO', 'QTD', 'PRIORIDADE', 'REF'],
               
                // Adicione mais linhas conforme necessário
            ];

            // Crie uma planilha Excel usando a biblioteca XLSX.js
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.aoa_to_sheet(sampleData);
            XLSX.utils.book_append_sheet(wb, ws, 'Modelo');

            // Salve a planilha como um arquivo Excel
            XLSX.writeFile(wb, 'modelo_excel.xlsx');
        }

</script>


    