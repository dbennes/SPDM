<?php

    // Conectar ao banco de dados
    include 'conect_new.php';

    // Criar uma conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Receber os dados enviados do JavaScript
    $data = json_decode($_POST['data'], true);

    // Dados da tabela do carrinho
    $cartTableData = array_map('array_values', $data['cartTableData']);
    echo "Dados da tabela do carrinho:<br>";
    print_r($cartTableData);

    $registrationStatus = array();

    // Loop sobre os dados da tabela do carrinho
    foreach ($cartTableData as $item) {
        $codigo = $item[0];
        $quantidade = $item[1];
        $prioridade = $item[2];
        $ref = $item[3];

        $data_registro = date('Y-m-d H:i:s'); // Obtém a data e hora atuais


        
        // Verificar se o dado existe no bd_estoque
        $query = "SELECT * FROM bd_estoque WHERE CODIGO = '$codigo'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            // Adicionar no bd_historico
            while ($row = $result->fetch_assoc()) {
                $DESCRICAO = $row["DESCRICAO"];

                $query = "INSERT INTO bd_reserva (CODIGO, QTD_RESERVADA, REF, DESCRICAO,  PRIORIDADE, DATA_SOLICITADA) VALUES ('$codigo', '$quantidade', '$ref', '$DESCRICAO', '$prioridade', '$data_registro')";
                if (mysqli_query($conn, $query)) {
                    array_push($registrationStatus, "Registro bem-sucedido para o item de código: " . $codigo);
                } else {
                    array_push($registrationStatus, "Falha ao registrar para o item de código: " . $codigo);
                }
            }
        } else {
            // Adicionar no bd_pendencia
            $query = "INSERT INTO bd_pendencia (CODIGO, QTD_PENDENTE, PRIORIDADE, REF, DATA_RG, DESCRICAO) VALUES ('$codigo', '$quantidade', '$prioridade', '$ref', '$data_registro', '$DESCRICAO')";
            if (mysqli_query($conn, $query)) {
                array_push($registrationStatus, "Item de código " . $codigo . " registrado em pendência.");
            } else {
                array_push($registrationStatus, "Falha ao registrar o item de código: " . $codigo . " em pendência.");
            }
        }
    }

  
    // Retornar o status do registro para o JavaScript
    echo json_encode($registrationStatus);

?>
