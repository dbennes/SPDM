<?php
// Verifique se o parâmetro de consulta 'codigo' está presente
if (isset($_GET['codigo'])) {
    // Realize uma conexão com o banco de dados e consulte a quantidade de estoque com base no código fornecido
    $codigo = $_GET['codigo'];

    // Substitua as credenciais do banco de dados pelas suas
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "pdm";

    // Crie uma conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verifique a conexão
    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    // Consulta a ser executada para buscar a quantidade de estoque com base no código
    $sql = "SELECT * FROM bd_estoque WHERE CODIGO = '$codigo'";
    $result = $conn->query($sql);

    // Se o resultado existir, retorne-o como JSON
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        // Se não houver resultado, retorne uma resposta indicando que o código não foi encontrado
        echo json_encode(array("qtd_estoque" => "Código não encontrado"));
    }

    // Feche a conexão com o banco de dados
    $conn->close();
}
?>
