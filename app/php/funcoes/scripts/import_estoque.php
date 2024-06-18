<?php
// import.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $excelData = json_decode($_POST['excelData'], true);

    // Conecte-se ao banco de dados
    include 'conect_new.php';


    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifique a conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Limpe a tabela antes de importar
    $truncateQuery = "TRUNCATE TABLE bd_estoque";
    mysqli_query($conn, $truncateQuery);

    // Inicialize um array para armazenar os itens importados
    $importedItems = array();

    // Insira os dados do Excel no banco de dados e adicione os itens ao array
    foreach ($excelData as $row) {
        $codigo = mysqli_real_escape_string($conn, $row[0]); // Substitua pelo índice correto da coluna de código
        $descricao = mysqli_real_escape_string($conn, $row[1]); // Substitua pelo índice correto da coluna de descrição
        $qtd = mysqli_real_escape_string($conn, $row[2]); // Substitua pelo índice correto da coluna de quantidade

        $insertQuery = "INSERT INTO bd_estoque (CODIGO, DESCRICAO, QTD_DISPONIVEL) VALUES ('$codigo', '$descricao', '$qtd')";
        mysqli_query($conn, $insertQuery);

        // Adicione os itens ao array
        $importedItems[] = array('CODIGO' => $codigo, 'DESCRICAO' => $descricao, 'QTD_DISPONIVEL' => $qtd);
    }

    // Feche a conexão
    mysqli_close($conn);

    // Responda com os itens importados
    echo json_encode(array('success' => true, 'message' => 'Importação concluída com sucesso.', 'importedItems' => $importedItems));
} else {
    echo json_encode(array('success' => false, 'message' => 'Acesso não autorizado.'));
}
?>
