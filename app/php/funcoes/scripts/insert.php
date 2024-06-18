<?php
// Conexão com o banco de dados
include 'conect_new.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verifica se há dados a serem inseridos
if (isset($_POST['items']) && isset($_POST['items2']) && isset($_POST['items3']) && !empty($_POST['items']) && !empty($_POST['items2']) && !empty($_POST['items3'])) {
    $items = $_POST['items'];   //COD
    $items2 = $_POST['items2']; //REF
    $items3 = $_POST['items3']; // QTD_RETIRADA

    $values = array();

    $sql_pesquisa = "SELECT * FROM bd_estoque WHERE CODIGO = '".$conn->real_escape_string($items[0])."'"; // Ajuste feito para pegar o primeiro item do array 'items'

    $result = $conn->query($sql_pesquisa);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $QTD_DISPONIVEL = $row["QTD_DISPONIVEL"];
            $DESCRICAO = $row["DESCRICAO"];

            if ($QTD_DISPONIVEL >= $items3[0] && $items3[0] > 0) {
                $novo_saldo = $QTD_DISPONIVEL - $items3[0];
                if ($novo_saldo < 0) {
                    echo "Saldo insuficiente para retirada";
                } else {
                    $novo_saldo = max(0, $novo_saldo); // Garante que o saldo não será negativo
                    // Restante do seu código aqui
                    $sql_atualizar_estoque = "UPDATE bd_estoque SET QTD_DISPONIVEL = $novo_saldo WHERE CODIGO = '".$conn->real_escape_string($items[0])."'";
                    if ($conn->query($sql_atualizar_estoque) === TRUE) {
                        echo "Estoque atualizado com sucesso";
                    } else {
                        echo "Erro ao atualizar estoque: " . $conn->error;
                    }
                }
            } elseif ($items3[0] <= 0) {
                echo "Quantidade informada é zero ou negativa, não é possível fazer a retirada.";
            } else {
                echo "Saldo insuficiente para retirada";

                $diferenca = $items3[0] - $QTD_DISPONIVEL;
                $sql_insert_out_of_stock = "INSERT INTO BD_PENDENCIA (CODIGO, REF, QTD_PENDENTE, DESCRICAO) VALUES ('".$conn->real_escape_string($items[0])."', '".$conn->real_escape_string($items2[0])."', '$diferenca', '".$conn->real_escape_string($DESCRICAO)."')";

                if ($conn->query($sql_insert_out_of_stock) === TRUE) {
                    echo "Dados inseridos na tabela de falta de estoque com sucesso.";
                } else {
                    echo "Erro ao inserir na tabela de falta de estoque: " . $conn->error;
                }

                $sql_atualizar_estoque = "UPDATE bd_estoque SET QTD_DISPONIVEL = 0 WHERE CODIGO = '".$conn->real_escape_string($items[0])."'";
                if ($conn->query($sql_atualizar_estoque) === TRUE) {
                    echo "Estoque atualizado com sucesso";
                } else {
                    echo "Erro ao atualizar estoque: " . $conn->error;
                }
            }
        }
    } else {
        echo "0 resultados";
    }
    $conn->close();
} else {
    echo "Nenhum item para inserir";
}
?>
