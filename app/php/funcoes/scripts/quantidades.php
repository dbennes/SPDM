<?php
// Conectar ao banco de dados
include 'conect_new.php';

// Recuperar os dados do Excel do corpo da solicitação POST
$data = json_decode($_POST['data'], true);

// Criar uma conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Iniciar a string de resultados com a estrutura da tabela HTML
$results = "<table border='1' class='table my-0'>
<tr>
<th>CODIGO</th>
<th class='text-center'>QTD</th>
<th class='text-center'>ORDEM</th>
<th class='text-center'>REF</th>
<th class='text-center'>ESTOQUE</th>
</tr>";

// Preparar a consulta SQL com uma declaração preparada
$sql = "SELECT QTD_DISPONIVEL FROM bd_estoque WHERE CODIGO = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $codigo); // "s" indica que $codigo é uma string

// Loop através dos dados do Excel e consultar o banco de dados para obter as quantidades
foreach ($data as $row) {
    $codigo = trim($row['CODIGO']); // Remover espaços em branco extras
    $QTD_EXCEL = $row['QTD']; // Substitua SEGUNDA_COLUNA pelo nome real da segunda coluna
    $prioridade = $row['PRIORIDADE']; // Substitua PRIORIDADE pelo nome real da coluna de prioridade
    $ref = $row['REF']; // Substitua REF pelo nome real da coluna de referência

    // Executar a consulta preparada
    if ($stmt->execute()) {
        // Verificar se $stmt resultou em um objeto mysqli_result
        $result = $stmt->get_result();

        if ($result instanceof mysqli_result) {
            // Fetch os resultados
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $QTD_DISPONIVEL = $row["QTD_DISPONIVEL"];

                $results .= "<tr>";
                $results .= "<td>" . $codigo . "</td>";
                $results .= "<td class='text-center'>" . $QTD_EXCEL . "</td>"; 
                $results .= "<td class='text-center'>" . $prioridade . "</td>";
                $results .= "<td class='text-center'>" . $ref . "</td>";
                $results .= "<td class='text-center'>" . $QTD_DISPONIVEL . "</td>";
                $results .= "</tr>";
            } else {
                $results .= "<tr>";
                $results .= "<td >" . $codigo . "</td>";
                $results .= "<td class='text-center'>" . $QTD_EXCEL . "</td>"; 
                $results .= "<td class='text-center'>" . $prioridade ."</td>";
                $results .= "<td class='text-center'>" . $ref . "</td>";
                $results .= "<td class='text-center'>" . " N. CADASTRADO</td>";
                $results .= "</tr>";
            }
        } else {
            // Lidar com o caso em que $stmt não resultou em um objeto mysqli_result
            die("Erro na execução da consulta: " . $conn->error);
        }
    } else {
        die("Erro na execução da consulta: " . $conn->error);
    }

    // Restaurar o cursor do resultado (caso você precise repetir a execução da mesma consulta)
    $stmt->store_result();
}

// Fechar a tag da tabela HTML
$results .= "</table>";

// Fechar a conexão com o banco de dados
$stmt->close();
$conn->close();

// Enviar os resultados de volta para o JavaScript
echo $results;
?>
