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
                                <button id="importButton" onclick="showLoadingModal()">Importar e Exibir</button>

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
           


    <script>

        function hideLoadingModal() {
            $('#loadingModal').modal('hide');
        }

        function showLoadingModal() {
            $('#loadingModal').modal('show');
            handleFile();
        }

        //ISSO AQUI É A PARTE DO CARRINHO E CONECXAO COM O SERVIDOR

        // Isso aqui é a parte do carrinho e conexão com o servidor

        let cartItems = [];
        let estoque = {}; // Declaração do objeto de estoque vazio
        let groupedItems = {};  




        function handleFile() {
    // Limpar a solicitação de retirada (carrinho)
    clearCartView();

    // Exibir o modal de carregamento
    //$('#loadingModal').modal('show');

    const fileInput = document.getElementById('file');
    const file = fileInput.files[0];

    // Verificar se um arquivo foi selecionado
    if (!file) {
        alert('Por favor, selecione um arquivo.');
        // Ocultar o modal em caso de erro
        hideLoadingModal();
        return;
    }

    const reader = new FileReader();

    reader.onload = function (e) {
        try {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });

            const sheetName = workbook.SheetNames[0];
            const sheet = workbook.Sheets[sheetName];

            // Converter para o formato JSON
            const json = XLSX.utils.sheet_to_json(sheet);

            // Limpar carrinho e estoque antes de adicionar novos itens
            cartItems = [];
            estoque = {};

            // Atualizar o modal para indicar que o processo está em andamento
            $('#loadingText').text('Processando...');

            // Enviar os dados para o servidor para obter as quantidades do banco de dados
            $.ajax({
                type: 'POST',
                url: 'php/funcoes/scripts/quantidades.php',
                data: { data: JSON.stringify(json) },
                success: function (response) {
                    $('#results').html(response);
                    addToCartAll();

                    // Ler o arquivo como base64
                    const fileReader = new FileReader();
                    fileReader.onload = function (fileEvent) {
                        // Salvar os dados e o arquivo na sessionStorage
                        const savedData = {
                            cartItems: cartItems,
                            estoque: estoque,
                            file: {
                                name: file.name,
                                content: fileEvent.target.result.split(',')[1]
                            }
                        };

                        sessionStorage.setItem('savedData', JSON.stringify(savedData));

                        // Adicione um atraso antes de fechar o modal (500 milissegundos)
                        setTimeout(function () {
                            // Ocultar o modal após o carregamento bem-sucedido
                            hideLoadingModal();
                        }, 5000);
                    };
                    fileReader.readAsDataURL(file);
                },
                error: function () {
                    // Adicione um atraso antes de fechar o modal (500 milissegundos)
                    setTimeout(function () {
                        // Em caso de erro, ocultar o modal
                        hideLoadingModal();
                    }, 5000);
                }
            });
        } catch (error) {
            console.error('Erro ao processar o arquivo:', error);

            // Adicione um atraso antes de fechar o modal (500 milissegundos)
            setTimeout(function () {
                // Em caso de erro, ocultar o modal
                hideLoadingModal();
            }, 3000);
        }
    };

    reader.readAsArrayBuffer(file);
}

// Função auxiliar para converter array buffer em base64
function arrayBufferToBase64(buffer) {
    let binary = '';
    const bytes = new Uint8Array(buffer);
    const len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    return window.btoa(binary);
}

        function clearCartView() {
    // Limpar a exibição da solicitação de retirada (carrinho)
    const cartTable = document.getElementById('cartTable');
    cartTable.innerHTML = '';

    // Limpar a tabela de resultados
    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';
}


        function addToCartAll() {
            const resultsDiv = document.getElementById('results');
            const rows = resultsDiv.querySelectorAll('tr');
            const dataToSend = [];

                for (let i = 1; i < rows.length; i++) {
                    const cells = rows[i].querySelectorAll('td');
                    const codigo = cells[0] ? cells[0].innerText : '';
                    const segundaColuna = cells[1] ? parseFloat(cells[1].innerText) : 0;
                    const prioridade = cells[2] ? parseInt(cells[2].innerText) : 0;
                    const ref = cells[3] ? cells[3].innerText : '';
                    //const estoque_disponivel = estoque[codigo] ? estoque[codigo] : 0; // Obter o estoque disponível do objeto 'estoque'
                    
                    //SEGUNDA COLUNA = QTD SOLICITADA NO EXCEL
                    
                    addToCart(codigo, segundaColuna, prioridade, ref ); // Adicione o parâmetro 'estoque_disponivel'
                    
                    const data = {
                            codigo: codigo,
                            segundaColuna: segundaColuna,
                            prioridade: prioridade,
                            ref: ref
                        };
                        dataToSend.push(data);
                    }

            estoquesss(dataToSend);
            
            //const quantidadeDoEstoque = estoque_disponivel; // Alteração feita para obter a quantidade do estoque
            
            //console.log(estoque);
        }


        
        function addToCart(codigo, segundaColuna, prioridade, ref) {
            const existingItemIndex = cartItems.findIndex(item => item.codigo === codigo && item.ref === ref);
            if (existingItemIndex !== -1) {
                cartItems[existingItemIndex].segundaColuna += parseFloat(segundaColuna);
            } else {
                cartItems.push({ codigo: codigo, segundaColuna: parseFloat(segundaColuna), prioridade: parseInt(prioridade), ref: ref });
            }
            updateCartView();
        }

        function removeFromCart(codigo) {
            // Sua lógica para remover do carrinho
            cartItems = cartItems.filter(item => item.codigo !== codigo);
            updateCartView();
           
        }

        function updateCartView() {
    const cartTable = document.getElementById('cartTable');
    cartTable.innerHTML = '';

    const groupedItems = {};

    // Ordenar por prioridade e referência
    cartItems.sort((a, b) => {
        if (a.prioridade !== b.prioridade) {
            return a.prioridade - b.prioridade;
        }
        return a.ref.localeCompare(b.ref);
    });

    // Agrupar itens pelo campo de referência
    cartItems.forEach(item => {
        if (!groupedItems[item.ref]) {
            groupedItems[item.ref] = [];
        }
        groupedItems[item.ref].push(item);
    });

    // Criar tabelas para cada grupo de referência
    Object.keys(groupedItems).forEach(groupRef => {
        cartTable.innerHTML += `
            <table class="table my-0" border="1" style="margin-bottom: 20px;">
                <thead border="1">
                    <tr style="background: #cfcfcf">
                        <th>CODIGO</th>
                        <th>QTD</th>
                        <th>PRIORIDADE</th>
                        <th>REF</th>
                        
                        <th >
                            <button style="border: none; background: #f0ffff00" class="toggle-all-details-btn" onclick="toggleAllDetails(this)">
                                <i class="fas fa-caret-down" style="color: #393939;"></i>
                            </button>
                        </th>
                    </tr>
                </thead>
                <tbody style="background: #e9e9e9;">
                    ${groupedItems[groupRef]
                        .map(item => `
                            <tr class="details-row" style="display: none;">
                                <td>${item.codigo}</td>
                                <td class='text-center'>${item.segundaColuna}</td>
                                <td class='text-center'>${item.prioridade}</td>
                                <td class='text-center'>${item.ref}</td>
                                <td class='text-center d-flex align-items-center' style='width: 100%'>
                                    <button style="border: none; background: #f0ffff00" onclick="removeFromCart('${item.codigo}')">
                                        <i class="fas fa-minus-square" style="color: #ff6868;"></i>    
                                    </button>
                                </td>
                            </tr>
                        `)
                        .join('')}
                </tbody>
            </table>
        `;
    });

    // Salvar os dados do carrinho e do estoque na sessionStorage após cada atualização
    saveDataToSessionStorage();
}


// Função para alternar a visibilidade de todas as linhas de detalhes
function toggleAllDetails(button) {
    const detailsRows = document.querySelectorAll('.details-row');

    detailsRows.forEach(row => {
        row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
    });

    // Atualiza o texto do botão
    
}

function saveDataToSessionStorage() {
    const dataToSave = {
        cartItems: cartItems,
        estoque: estoque,
    };

    sessionStorage.setItem('savedData', JSON.stringify(dataToSave));
}

function loadDataFromSessionStorage() {
    const savedData = sessionStorage.getItem('savedData');

    if (savedData) {
        const jsonData = JSON.parse(savedData);

        if (jsonData.cartItems) {
            cartItems = jsonData.cartItems;
        }

        if (jsonData.estoque) {
            estoque = jsonData.estoque;
        }

        // Atualizar a visualização do carrinho e estoque após carregar os dados
        updateCartView();
        // Chame uma função para atualizar a visualização do estoque, se necessário
        // updateEstoqueView();
    }
}

// Chamar loadDataFromSessionStorage quando o DOM está pronto
document.addEventListener('DOMContentLoaded', function () {
    // Carregar dados do carrinho e do estoque da sessionStorage ao iniciar a página
    loadDataFromSessionStorage();
}); 

function estoquesss(data) {
    data.forEach(item => {
        const { codigo, quantidade, segundaColuna, ref, prioridade } = item;
        if (estoque[codigo]) {
            estoque[codigo].quantidade = quantidade;
            estoque[codigo].segundaColuna = segundaColuna;
            estoque[codigo].ref = ref;
            estoque[codigo].prioridade = prioridade; // Corrigir atributo de prioridade
        } else {
            estoque[codigo] = { codigo, quantidade, segundaColuna, ref, prioridade };
        }
    });

    const updatedData = Object.values(estoque);

    $.ajax({
        type: 'POST',
        url: 'php/funcoes/scripts/atualizar_estoque.php',
        data: { data: JSON.stringify(updatedData) },
        success: function (response) {
            $('#estoque').html(response);
        }
    });
}

function loadDataFromSessionStorage() {
    const savedData = sessionStorage.getItem('savedData');

    if (savedData) {
        const jsonData = JSON.parse(savedData);

        if (jsonData.cartItems) {
            cartItems = jsonData.cartItems;
        }

        if (jsonData.estoque) {
            estoque = jsonData.estoque;
        }

        // Atualizar a visualização do carrinho e estoque após carregar os dados
        updateCartView();
        estoquesss(Object.values(estoque)); // Atualizar a visualização do estoque
    }
}

function clearData() {
    // Limpar carrinho
    cartItems = [];
    updateCartView();

    // Limpar dados da planilha
    clearCartView();

    // Limpar estoque
    estoque = {};
    $('#estoque').html('');

    // Limpar dados salvos na sessionStorage
    sessionStorage.removeItem('savedData');
}


let isProcessing = false;

function sendTablesToPhp() {
    if (isProcessing) {
        return;
    }

    isProcessing = true;

    const cartTableData = [];
    $('#cartTable tbody tr').each(function (index) {
        const obj = {};
        $(this)
            .find('td')
            .each(function (index) {
                obj[index] = $(this).text();
            });
        cartTableData.push(obj);
    });

    const alertMessagesDiv = $('#alertMessages');
    alertMessagesDiv.html(''); // Limpar mensagens existentes

    const groupedCartData = {};
    cartTableData.forEach(item => {
        const ref = item[3]; // Ajuste para a posição correta da referência em sua tabela
        if (!groupedCartData[ref]) {
            groupedCartData[ref] = [];
        }
        groupedCartData[ref].push(item);
    });

    Object.values(groupedCartData).forEach(group => {
        const data = {
            cartTableData: group
        };

        $.ajax({
            type: 'POST',
            url: 'php/funcoes/scripts/registro_bloco.php',
            data: { data: JSON.stringify(data) },
            success: function (response) {
                // Exibir a mensagem de alerta específica para cada item registrado
                alertMessagesDiv.append(`<div class="alert alert-success" role="alert">${response}</div>`);

                // Limpar dados após o registro bem-sucedido
                clearData();

                // Ocultar as mensagens de alerta após alguns segundos (opcional)
                setTimeout(function () {
                    alertMessagesDiv.html('');
                }, 150000);
            },
            error: function () {
                // Exibir mensagem de alerta em caso de erro no registro (opcional)
                alertMessagesDiv.append('<div class="alert alert-danger" role="alert">Erro ao registrar COD.</div>');
            }
        });
    });

    isProcessing = false;
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script></body>
</html>