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
                        // Consultar o BD_ESTOQUE para obter a quantidade atual
                        $query = "SELECT QTD_DISPONIVEL, DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param("s", $item['CODIGO']);
                        $stmt->execute();
                        $stmt->bind_result($quantidadeAtual, $descricaoEstoque);
                        $stmt->fetch();
                        $stmt->close();
            
                        // Calcular a nova quantidade
                        $novaQuantidade = $quantidadeAtual - $item['QTD'];
            
                        // Certifique-se de que a nova quantidade não seja negativa
                        $novaQuantidade = max(0, $novaQuantidade);
            
                        if ($novaQuantidade < 0) {
                            // Se não há estoque suficiente, registre no bd_pendencia
                            $insertPendenciaQuery = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, REF, PRIORIDADE, DATA_RG, DESCRICAO) VALUES (?, ?, ?, ?, NOW(), ?)";
                            $stmtInsertPendencia = $conn->prepare($insertPendenciaQuery);
                            $stmtInsertPendencia->bind_param("sisss", $item['CODIGO'], $item['QTD'], $item['REF'], $item['PRIORIDADE'], $descricaoEstoque);
                            $stmtInsertPendencia->execute();
                            $stmtInsertPendencia->close();
            
                            $blocoTemPendencia = true;
                            break;
                        }
            
                        // Iniciar transação apenas se não houver pendência
                        if (!$blocoTemPendencia) {
                            $conn->begin_transaction();
            
                            try {
                                // Atualizar a quantidade no BD_ESTOQUE
                                $query = "UPDATE bd_estoque SET QTD_DISPONIVEL = ? WHERE CODIGO = ?";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param("ss", $novaQuantidade, $item['CODIGO']);
                                $stmt->execute();
                                $stmt->close();
            
                                // Inserir no bd_historico
                                $insertHistoricoQuery = "INSERT INTO bd_historico (CODIGO, DESCRICAO, QTD_RETIRADA, REF, PRIORIDADE, DATA_RG) VALUES (?, ?, ?, ?, ?, NOW())";
                                $stmtInsertHistorico = $conn->prepare($insertHistoricoQuery);
                                $stmtInsertHistorico->bind_param("ssiss", $item['CODIGO'], $descricaoEstoque, $item['QTD'], $item['REF'], $item['PRIORIDADE']);
                                $stmtInsertHistorico->execute();
                                $stmtInsertHistorico->close();
            
                                $conn->commit();
                            } catch (Exception $e) {
                                $conn->rollback();
                                error_log("Erro durante transação para o bloco: " . $e->getMessage());
                            }
                        }
                    }
                }
            }
            
            
            // Envie uma resposta de sucesso para o JavaScript
            echo json_encode(["success" => true, "message" => "Processamento concluído com sucesso"]);

        } catch (Exception $e) {
            // Em caso de exceção, faça o rollback da transação
            $conn->rollback();

            // Adicione logs para a exceção
            error_log("Erro durante transação: " . $e->getMessage());

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
?>