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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar se o array foi recebido com sucesso
    if (isset($_POST["dataArray"])) {
        $dataArray = $_POST["dataArray"];

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
        // Verificar saldo suficiente para cada material no bloco
        foreach ($materiaisNoBloco as $item) {
            // Consultar o BD_ESTOQUE para obter a quantidade atual
            $query = "SELECT QTD_DISPONIVEL FROM bd_estoque WHERE CODIGO = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $item['CODIGO']);
            $stmt->execute();
            $stmt->bind_result($quantidadeAtual);
            $stmt->fetch();
            $stmt->close();

            // Verificar se há estoque suficiente
            if ($quantidadeAtual < $item['QTD']) {
                // Se não houver estoque, registrar no bd_pendencia
                $insertPendenciaQuery = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, REF, PRIORIDADE, DATA_RG) VALUES (?, ?, ?, ?, NOW())";
                $stmtInsertPendencia = $conn->prepare($insertPendenciaQuery);
                $stmtInsertPendencia->bind_param("siss", $item['CODIGO'], $item['QTD'], $item['REF'], $item['PRIORIDADE']);
                $stmtInsertPendencia->execute();
                $stmtInsertPendencia->close();
            } else {
                // Se houver estoque, efetuar os descontos e registros
                // Atualizar a quantidade no BD_ESTOQUE
                $novaQuantidade = $quantidadeAtual - $item['QTD'];
                $query = "UPDATE bd_estoque SET QTD_DISPONIVEL = ? WHERE CODIGO = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ss", $novaQuantidade, $item['CODIGO']);
                $stmt->execute();
                $stmt->close();

                // Consultar o BD_ESTOQUE para obter a descrição
                $queryDescricao = "SELECT DESCRICAO FROM bd_estoque WHERE CODIGO = ?";
                $stmtDescricao = $conn->prepare($queryDescricao);
                $stmtDescricao->bind_param("s", $item['CODIGO']);
                $stmtDescricao->execute();
                $stmtDescricao->bind_result($descricao);
                $stmtDescricao->fetch();
                $stmtDescricao->close();

                // Inserir no bd_historico
                $insertHistoricoQuery = "INSERT INTO bd_historico (CODIGO, DESCRICAO, QTD_RETIRADA, REF, PRIORIDADE, DATA_RG) VALUES (?, ?, ?, ?, ?, NOW())";
                $stmtInsertHistorico = $conn->prepare($insertHistoricoQuery);
                $stmtInsertHistorico->bind_param("ssiss", $item['CODIGO'], $descricao, $item['QTD'], $item['REF'], $item['PRIORIDADE']);
                $stmtInsertHistorico->execute();
                $stmtInsertHistorico->close();
            }
        }
    }
}
              

            // Commit da transação
            $conn->commit();

            // Envie uma resposta de sucesso para o JavaScript
            echo json_encode(["success" => true]);
        } catch (Exception $e) {
            // Em caso de exceção, faça o rollback da transação
            $conn->rollback();

            // Envie uma resposta de erro para o JavaScript
            http_response_code(400);
            echo "Erro: " . $e->getMessage();
        }
    } else {
        // Responda com um erro se o array não for recebido
        http_response_code(400);
        echo "Erro: Nenhum array recebido.";
    }
} else {
    // Responda com um erro se a requisição não for POST
    http_response_code(405);
    echo "Método não permitido";
}

// Fechar a conexão
$conn->close();
?>
