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
            
                    foreach ($materiaisNoBloco as $item) {
                        $queryDescricao = "SELECT DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                        $stmtDescricao = $conn->prepare($queryDescricao);
                        $stmtDescricao->bind_param("s", $item['CODIGO']);
                        $stmtDescricao->execute();
                        $stmtDescricao->bind_result($descricao);
                        $stmtDescricao->fetch();
                        $stmtDescricao->close();
            
                        $query = "SELECT QTD_DISPONIVEL FROM bd_estoque WHERE CODIGO = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $item['CODIGO']);
                        $stmt->execute();
                        $stmt->bind_result($quantidadeAtual);
                        $stmt->fetch();
                        $stmt->close();

                        //PRIMEIRO INPUT
            
                        if ($quantidadeAtual < $item['QTD']) {
                            $insertPendenciaQuery = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, REF, PRIORIDADE, DATA_RG, DESCRICAO) VALUES (?, ?, ?, ?, NOW(), ?)";
                            $stmtInsertPendencia = $conn->prepare($insertPendenciaQuery);
                            $stmtInsertPendencia->bind_param("sdsss", $item['CODIGO'], $item['QTD'], $item['REF'], $item['PRIORIDADE'], $descricao);
                            $stmtInsertPendencia->execute();
                            $stmtInsertPendencia->close();
            
                            $blocoTemPendencia = true;
                            break;
                        }
                    }
            
                    if (!$blocoTemPendencia) {
                        // Realize uma única atualização do estoque para todo o bloco
                        $conn->begin_transaction();
                    
                        try {
                            // Inicialize as variáveis para acumular os valores de atualização
                            $updates = [];
                    
                            foreach ($materiaisNoBloco as $item) {
                                // Obtenha a quantidade atual do estoque
                                $queryQuantidadeAtual = "SELECT QTD_DISPONIVEL, DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                                $stmtQuantidadeAtual = $conn->prepare($queryQuantidadeAtual);
                                $stmtQuantidadeAtual->bind_param("s", $item['CODIGO']);
                                $stmtQuantidadeAtual->execute();
                                $stmtQuantidadeAtual->bind_result($quantidadeAtual, $descricao);
                                $stmtQuantidadeAtual->fetch();
                                $stmtQuantidadeAtual->close();
                    
                                // Verifique se a quantidade disponível é suficiente
                                //if ($quantidadeAtual < $item['QTD']) {
                                    // Se não houver estoque suficiente, registre uma pendência e interrompa o loop  //PRIMEIRO INPUT
                                //    $insertPendenciaQuery = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, REF, PRIORIDADE, DATA_RG, DESCRICAO) VALUES (?, ?, ?, ?, NOW(), ?)";
                                //    $stmtInsertPendencia = $conn->prepare($insertPendenciaQuery);
                                //    $stmtInsertPendencia->bind_param("sdsss", $item['CODIGO'], $item['QTD'], $item['REF'], $item['PRIORIDADE'], $descricao);
                                //    $stmtInsertPendencia->execute();
                                //    $stmtInsertPendencia->close();
                    
                                //    $blocoTemPendencia = true;
                                //    break;
                                //}
                    
                                // Se a quantidade disponível for suficiente, acumule os valores para atualização
                                $novaQuantidade = $quantidadeAtual - $item['QTD'];
                                $updates[] = [
                                    'novaQuantidade' => $novaQuantidade,
                                    'codigo' => $item['CODIGO'],
                                    'descricao' => $descricao,
                                    'qtd_retirada' => $item['QTD'],
                                    'ref' => $item['REF'],
                                    'prioridade' => $item['PRIORIDADE']
                                ];
                            }
                    
                            // Se não houver pendências, atualize o estoque e registre no bd_historico
                            if (!$blocoTemPendencia) {
                                // Atualize o estoque aqui fora do loop interno
                                // Consolidar as atualizações do estoque em uma única operação
                                $queryAtualizacaoEstoque = "UPDATE bd_estoque SET QTD_DISPONIVEL = ? WHERE CODIGO = ?";
                                $stmtAtualizacaoEstoque = $conn->prepare($queryAtualizacaoEstoque);
                                
                    
                                foreach ($updates as $update) {
                                    // Obtenha a quantidade atual do estoque
                                    $queryQuantidadeAtual = "SELECT QTD_DISPONIVEL, DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                                    $stmtQuantidadeAtual = $conn->prepare($queryQuantidadeAtual);
                                    $stmtQuantidadeAtual->bind_param("s", $update['codigo']);
                                    $stmtQuantidadeAtual->execute();
                                    $stmtQuantidadeAtual->bind_result($quantidadeAtual, $descricao);
                                    $stmtQuantidadeAtual->fetch();
                                    $stmtQuantidadeAtual->close();

                                    // Subtraia a quantidade retirada usando BCMath
                                    $novaQuantidade = bcsub($quantidadeAtual, $update['qtd_retirada'], 3);

                                    // Atualize o estoque
                                    $queryAtualizacaoEstoque = "UPDATE bd_estoque SET QTD_DISPONIVEL = ? WHERE CODIGO = ?";
                                    $stmtAtualizacaoEstoque = $conn->prepare($queryAtualizacaoEstoque);
                                    $stmtAtualizacaoEstoque->bind_param("ds", $novaQuantidade, $update['codigo']);
                                    $stmtAtualizacaoEstoque->execute();

                                    // Registre no bd_historico
                                    $insertHistoricoQuery = "INSERT INTO bd_historico (CODIGO, DESCRICAO, QTD_RETIRADA, REF, PRIORIDADE, DATA_RG) VALUES (?, ?, ?, ?, ?, NOW())";
                                    $stmtInsertHistorico = $conn->prepare($insertHistoricoQuery);
                                    $stmtInsertHistorico->bind_param("ssdss", $update['codigo'], $update['descricao'], $update['qtd_retirada'], $update['ref'], $update['prioridade']);
                                    $stmtInsertHistorico->execute();
                                }
                    
                                $stmtAtualizacaoEstoque->close();
                            }
                    
                            $conn->commit();
                        } catch (Exception $e) {
                            $conn->rollback();
                            error_log("Erro durante transação para o bloco: " . $e->getMessage());
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