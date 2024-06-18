<?php
// processar_dados.php

// Conecte-se ao banco de dados
include 'conect_new.php';

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$transacoesConfirmadas = 0;
$transacoesRevertidas = 0;

set_time_limit(1000);

// Verificar se o corpo da solicitação contém dados JSON
$json = file_get_contents('php://input');
$dataArray = json_decode($json, true);

// Definir cabeçalho para indicar que a resposta é JSON
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o array foi recebido com sucesso
    if ($dataArray) {
        // Antes de começar a transação, adicione um log para verificar se o array foi recebido corretamente
        error_log("Array recebido: " . print_r($dataArray, true));

        // Iniciar uma transação
        $conn->begin_transaction();

        

        try {
            // Organizar o array por PRIORIDADE e REF
            $organizadoPorBloco = [];
            foreach ($dataArray as $item) {
                $organizadoPorBloco[$item['PRIORIDADE']][$item['REF']][] = $item;
            }

            // Iterar sobre cada bloco
            foreach ($organizadoPorBloco as $prioridade => $blocosPorPrioridade) {
                foreach ($blocosPorPrioridade as $ref => $materiaisNoBloco) {
                    
                    $blocoTemPendencia = false;
                    
                    $materiais_okay = [];
                    $materiais_pendentes = [];
                    $estoquePendencia = [];

                    // ESTOQYE ESTAVA AQUIII
                    // Executar a consulta
                    $query = "SELECT CODIGO, DESCRICAO, QTD_DISPONIVEL FROM bd_estoque";
                    $result = $conn->query($query);

                    // Verificar se a consulta foi bem-sucedida
                    if ($result) {
                        // Obter todos os resultados como um array associativo
                        $estoque = $result->fetch_all(MYSQLI_ASSOC);

                        // Agora, $resultArray contém os resultados da consulta
                         //print_r($estoque);

                    } else {
                        // Tratar o erro, se houver
                        echo "Erro na consulta: " . $conn->error;
                    }
                    

                    $qtdDescontada = null;
                    $qtdSolicitadaSomada = null;

                    $atualizarEstoqueArray = null;


                    $updates = [];

                    
                    foreach ($materiaisNoBloco as $item) {
                        
                        $query = "SELECT QTD_DISPONIVEL FROM bd_estoque WHERE CODIGO = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $item['CODIGO']);
                        $stmt->execute();
                        $stmt->bind_result($quantidadeAtual);
                        $stmt->fetch();
                        $stmt->close();

                        $qtdSolicitado = $item['QTD'];
                        $codigoSolicitado = $item['CODIGO'];
                        
                        $qtdSolicitadaSomada += $qtdSolicitado;

                        foreach ($estoque as &$itemEstoque) {

                            $codigoEstoque = $itemEstoque['CODIGO'];
                            $quantidadeEstoque = $itemEstoque['QTD_DISPONIVEL']; 

                            $quantidadeEncontrada = null;
                            $Procurado = $item['CODIGO'];

                            if ($codigoEstoque == $codigoSolicitado) {
                                // Se o código for encontrado, armazene a quantidade e saia do loop
                                $quantidadeEncontrada = $itemEstoque['QTD_DISPONIVEL'];
                                break;
                            }

                        }

                        if ($quantidadeEncontrada >= $qtdSolicitado) {
                            $atualizarEstoqueArray = bcsub($quantidadeEncontrada, $qtdSolicitado, 3);
                            $qtdDescontada += bcadd($qtdSolicitado,0, 3);
                            $itemEstoque['QTD_DISPONIVEL'] = $atualizarEstoqueArray;
                            //$item['CODIGO'] = $atualizarEstoqueArray;

                            $novo = $atualizarEstoqueArray;

                            $updates[] = [
                                'novaQuantidade' => $atualizarEstoqueArray,
                                'codigo' => $item['CODIGO']
                            ];
                        }else{

                            $blocoTemPendencia = true;

                            $atualizarEstoqueArray1 = bcsub($quantidadeEncontrada, $qtdSolicitado, 3);
                            //$voltarEstoque = bcadd($quantidadeEncontrada, $qtdSolicitado, 3);

                            //$itemEstoque['QTD_DISPONIVEL'] = $voltarEstoque;

                            //testeeeeeeeeeeeeee
                            if ($itemEstoque['CODIGO'] == "J32230B200-00:LL") {
                                echo "nao encontrei: " . $atualizarEstoqueArray1;
                                //echo "voltaaaaarrr: " . $voltarEstoque;
                            }

                            $estoquePendencia[] = [
                                'codigo' => $item['CODIGO'],
                                'qtd_pendente' => $atualizarEstoqueArray1,
                                'ref' => $item['REF'],
                                'prioridade' => $item['PRIORIDADE']
                            ];

                        }
                        
                    }


                        //print_r($estoque);
                        


                    if($blocoTemPendencia == false ) {
                        
                        foreach ($materiaisNoBloco  as $itemBloco) {

                            $itemBlocoCodigo = $itemBloco['CODIGO'];
                            $qtdBlocoCodigo = $itemBloco['QTD'];
                            $prioridadeBlocoCodigo = $itemBloco['PRIORIDADE'];
                            $refBlocoCodigo = $itemBloco['REF'];

                            // AQUI PEGO A DESCRIÇÃO DO MATERIAL NO BD_ESTOQUE
                            $queryDescricao = "SELECT DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                            $stmtDescricao = $conn->prepare($queryDescricao);
                            $stmtDescricao->bind_param("s",  $itemBloco['CODIGO']);
                            $stmtDescricao->execute();
                            $stmtDescricao->bind_result($descricao22);
                            $stmtDescricao->fetch();
                            $stmtDescricao->close();

                            // Registre no bd_historico
                            $insertHistoricoQuery = "INSERT INTO bd_historico (CODIGO, QTD_RETIRADA, REF, PRIORIDADE, DESCRICAO, DATA_RG) VALUES (?, ?, ?, ?, ?,  NOW())";
                            $stmtInsertHistorico = $conn->prepare($insertHistoricoQuery);
                            $stmtInsertHistorico->bind_param("sdsss", $itemBlocoCodigo, $qtdBlocoCodigo, $refBlocoCodigo, $prioridadeBlocoCodigo, $descricao22);
                            $stmtInsertHistorico->execute();

                            if ($itemBlocoCodigo == "J32230B200-00:LL") {
                                echo "ATUALIZADOOOOO: " . $atualizarEstoqueArray;
                                echo "- TESTEEEEEEEEEEEEEEEEEEEEE: " . $novo;
                            }

                        }

                        

                      

                    }elseif ($blocoTemPendencia == true) {
                        
                        $estoquePendenciaSumarizado = [];

                        // ESSE LOOP É PARA SUMARIZAR AS PENDENCIAS
                        foreach ($estoquePendencia as $itemBlocoPendencia) {
                            $itemBlocoCodigo = $itemBlocoPendencia['codigo'];
                            $qtdBlocoCodigo = $itemBlocoPendencia['qtd_pendente'];
                            $prioridadeBlocoCodigo = $itemBlocoPendencia['prioridade'];
                            $refBlocoCodigo = $itemBlocoPendencia['ref'];

                            // Verificar se o código já existe no array sumarizado
                            $index = array_search($itemBlocoCodigo, array_column($estoquePendenciaSumarizado, 'codigo'));

                            if ($index !== false) {
                                // Se existir, somar as quantidades pendentes
                                $estoquePendenciaSumarizado[$index]['qtd_pendente'] += $qtdBlocoCodigo;
                            } else {
                                // Se não existir, adicionar ao array sumarizado
                                $estoquePendenciaSumarizado[] = [
                                    'codigo' => $itemBlocoCodigo,
                                    'qtd_pendente' => $qtdBlocoCodigo,
                                    'ref' => $refBlocoCodigo,
                                    'prioridade' => $prioridadeBlocoCodigo
                                ];

                                //print_r($estoquePendenciaSumarizado);

                            }
                        }

                        foreach ($estoquePendenciaSumarizado  as $itemPendenciaSumarizado) {

                            $itemBlocoCodigo1 = $itemPendenciaSumarizado['codigo'];
                            $qtdBlocoCodigo1 = $itemPendenciaSumarizado['qtd_pendente'];
                            $prioridadeBlocoCodigo1 = $itemPendenciaSumarizado['prioridade'];
                            $refBlocoCodigo1 = $itemPendenciaSumarizado['ref'];

                            // AQUI PEGO A DESCRIÇÃO DO MATERIO NO BD_ESTOQUE
                            $queryDescricao = "SELECT DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                            $stmtDescricao = $conn->prepare($queryDescricao);
                            $stmtDescricao->bind_param("s",  $itemBlocoCodigo1);
                            $stmtDescricao->execute();
                            $stmtDescricao->bind_result($descricao222);
                            $stmtDescricao->fetch();
                            $stmtDescricao->close();

                            //if ($descricao222 === null) {
                            //    $descricao222 = ""; // Define a descrição como vazia
                            //}
                               

                                $insertPendenciaQuery = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, REF, PRIORIDADE, DESCRICAO, DATA_RG) VALUES (?, ?, ?, ?, ?, NOW())";
                                $stmtInsertPendencia = $conn->prepare($insertPendenciaQuery);
                                $stmtInsertPendencia->bind_param("sdsss", $itemBlocoCodigo1, $qtdBlocoCodigo1, $refBlocoCodigo1, $prioridadeBlocoCodigo1, $descricao222);
                                $stmtInsertPendencia->execute();
                                $stmtInsertPendencia->close();

                           
                            
                        }

                        

                    }

                    foreach($updates as $atualizarEstoque){

                       $codigoAtualizar = $atualizarEstoque['codigo'];
                       $novaQtd = $atualizarEstoque['novaQuantidade'];

                        //print_r($estoquePendenciaSumarizado);

                        if ($blocoTemPendencia == false) {

                            // AQUI ATUALIZA O ESTOQUE DOS ITENS QUE NAO TEM PENDENCIA
                            $queryAtualizacaoEstoque = "UPDATE bd_estoque SET QTD_DISPONIVEL = ? WHERE CODIGO = ?";
                            $stmtAtualizacaoEstoque = $conn->prepare($queryAtualizacaoEstoque);
                            $stmtAtualizacaoEstoque->bind_param("ds", $novaQtd, $codigoAtualizar);
                            $stmtAtualizacaoEstoque->execute();

                            //echo "cheguei aqui!";
                        }
                    }
                }
            }

           

            $conn->commit();
            
            // Envie uma resposta de sucesso para o JavaScript
            echo json_encode(["success" => true, "message" => "Processamento concluído com sucesso"]);

            $transacoesConfirmadas++;

        } catch (Exception $e) {
            // Em caso de exceção, faça o rollback da transação
            $conn->rollback();

            // Adicione logs para a exceção
            error_log("Erro durante transação: " . $e->getMessage());

            $transacoesRevertidas++;

            // Envie uma resposta de erro para o JavaScript
            http_response_code(400);
            echo json_encode(["error" => "Erro no servidor: " . $e->getMessage()]);
        }
    } else {
        // Responda com um erro se o array não for recebido
        http_response_code(400);
        echo json_encode(["error" => "Nenhum array recebido."]);
    }
} else {
    // Responda com um erro se a requisição não for POST
    http_response_code(405);
    echo json_encode(["error" => "Método não permitido"]);
}

// Fechar a conexão
$conn->close();

// No final do script
echo "Transações Confirmadas: $transacoesConfirmadas\n";
echo "Transações Revertidas: $transacoesRevertidas\n";

?>