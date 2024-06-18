<?php

// Conectar ao banco de dados
include 'conect_new.php';

// Criar uma conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Iniciar uma transação
$conn->begin_transaction();

// Receber os dados enviados do JavaScript
$data = json_decode($_POST['data'], true);

// Dados da tabela do carrinho
$cartTableData = $data['cartTableData'];
echo "<b>Dados Registrados:</b><br><br>";

$item_codigo = $cartTableData[0];



// Organize os dados do carrinho com base na prioridade
usort($cartTableData, function ($a, $b) {
    if ($a[2] == $b[2]) {
        return strcmp($a[3], $b[3]); // Ordenar por referência em caso de empate na prioridade
    }
    return ($a[2] < $b[2]) ? -1 : 1;
});

// Variável para controlar se todos os itens do bloco estão disponíveis no estoque
$allAvailable = true;

foreach ($cartTableData as $item) {
    $codigo = $item[0];
    $quantidade = $item[1];
    $prioridade = $item[2];
    $referencia = $item[3];

    // Verificar se o dado existe no bd_estoque
    $query = "SELECT * FROM bd_estoque WHERE CODIGO = '$codigo' FOR UPDATE";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $quantidadeAtual = $row['QTD_DISPONIVEL'];
        $descricao = $row['DESCRICAO']; // Obtém a descrição corretamente

        if ($quantidadeAtual >= $quantidade) {
            // Adicionar no bd_historico
            $query = "INSERT INTO bd_historico (CODIGO, QTD_RETIRADA, DATA_RG, PRIORIDADE, REF, DESCRICAO) VALUES ('$codigo', '$quantidade', NOW(), '$prioridade', '$referencia', '$descricao')";
            mysqli_query($conn, $query);

            // Subtrair a quantidade do estoque
            // Ver quantidade se estao sendo atualizadas ou se tem erro

            $novaQuantidade = $quantidadeAtual - $quantidade;
            $query = "UPDATE bd_estoque SET QTD_DISPONIVEL = '$novaQuantidade' WHERE CODIGO = '$codigo'";
            mysqli_query($conn, $query);

            // Armazene a nova quantidade atualizada no array $item
            $item['quantidade_atual'] = $novaQuantidade;

            echo "<b> COD: </b> " . $codigo . " - <b> QTD: </b>". $quantidade ."<br> Dados adicionados com sucesso!<br><br> ";
        } else {
            // Se um item não estiver disponível, marque a flag como falso
            $allAvailable = false;

            // Adicionar no bd_pendencia
            $query = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, DATA_RG, PRIORIDADE, REF, DESCRICAO) VALUES ('$codigo', '$quantidade', NOW(), '$prioridade', '$referencia', '$descricao')";
            if (mysqli_query($conn, $query)) {
                echo "<b> COD: </b> " . $codigo . " - <b> QTD: </b>". $quantidade ."<br> Quantidade insuficiente. Item adicionado ao bd_pendencia.<br><br>";
            } else {
                echo  "<b> COD: </b> " . $codigo . " Erro ao adicionar dados ao bd_pendencia: " . mysqli_error($conn) . "<br><br> ";
            }
        }
    } else {
        // Se um item não estiver disponível, marque a flag como falso
        $allAvailable = false;

        // Adicionar no bd_pendencia
        $query = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, DATA_RG,  PRIORIDADE, REF, DESCRICAO) VALUES ('$codigo', '$quantidade', NOW(), '$prioridade', '$referencia', 'Descrição não encontrada')";
        if (mysqli_query($conn, $query)) {
            echo  "<b> COD: </b> " . $codigo . " - <b> QTD: </b>". $quantidade ." <br>Item não encontrado no estoque. Adicionado ao bd_pendencia.<br><br> ";
        } else {
            echo "Erro ao adicionar dados ao bd_pendencia: " .  mysqli_error($conn) . "<br>";
        }
    }
}

// Verifique a flag após o loop
if ($allAvailable) {
    // Confirmar a transação se todos os itens estiverem disponíveis
    mysqli_commit($conn);
    echo "Todos os itens do bloco estão disponíveis. A transação foi confirmada.";

    // Atualizar bd_estoque com as novas quantidades
    foreach ($cartTableData as &$item) {
        $codigo = $item[0];
        $quantidadeAtualizada = $item['quantidade_atual'];

        // Atualizar bd_estoque com a nova quantidade
        $query = "UPDATE bd_estoque SET QTD_DISPONIVEL = '$quantidadeAtualizada' WHERE CODIGO = '$codigo'";
        mysqli_query($conn, $query);
    }
    // Limpar a referência para evitar efeitos colaterais inesperados
    unset($item);
} else {

    // Rollback se um ou mais itens não estiverem disponíveis
    mysqli_rollback($conn);
    echo "Um ou mais itens do bloco não estão disponíveis no estoque. A transação foi cancelada.";

    // Adicione uma lógica para registrar a quantidade real faltante no bd_pendencia
    foreach ($cartTableData as $item) {
        $codigo = $item[0];
        $quantidade = $item[1];
        //$descricao = $row['DESCRICAO'];

        // Verificar se o dado existe no bd_estoque
        $query = "SELECT * FROM bd_estoque WHERE CODIGO = '$codigo'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $quantidadeAtual = $row['QTD_DISPONIVEL'];
            $descricao = $row['DESCRICAO'];
            

            if ($quantidadeAtual < $quantidade) {
                $quantidadeFaltante = $quantidade - $quantidadeAtual;

                // Adicionar no bd_pendencia a quantidade real faltante
                $query = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, DATA_RG, PRIORIDADE, REF, DESCRICAO) VALUES ('$codigo', '$quantidadeFaltante', NOW(), '$prioridade', '$referencia', '$descricao')";
                if (mysqli_query($conn, $query)) {
                    echo "<b> COD: </b> " . $codigo . " - <b> QTD: </b>" . $quantidadeFaltante . "<br> Quantidade real faltante registrada no bd_pendencia.<br><br>";
                } else {
                    echo "<b> COD: </b> " . $codigo . " Erro ao registrar quantidade real faltante no bd_pendencia: " . mysqli_error($conn) . "<br><br> ";
                }
            }
        }
    }
}

// Dados da tabela de estoque
//$estoqueData = $data['estoqueData'];
//echo "Dados
