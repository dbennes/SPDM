<?php
    // Conectar ao banco de dados
    include 'conect_new.php';

    // Decodificar os dados recebidos do JavaScript
    $data = json_decode($_POST['data'], true);

    // Criar uma conexão com o banco de dados
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar a conexão
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Iniciar a string de resultados com a estrutura da tabela HTML
    $results = "<table border='1'  class='table my-0'>
    <thead>
    <tr>
    <th>CODIGO</th>
    <th>RETIRADA</th>
    <th>ESTOQUE</th>
    <th class='text-center'>SALDO</th>
    <!--<th class='text-center'>REF</th>-->
    </tr>
    </thead>";

    // Loop através dos dados e processar cada par de código e quantidade individualmente
    foreach ($data as $rows) {
        $codigo = $conn->real_escape_string($rows['codigo']); // Para evitar injeção de SQL
        $quantidade_excel = $rows['segundaColuna'];
        $ref = $rows['ref'];

        // Consulta para obter a quantidade correspondente ao código usando uma declaração preparada
        $sql = "SELECT QTD_DISPONIVEL FROM bd_estoque WHERE CODIGO = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $codigo);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $QTD_DISPONIVEL = $row["QTD_DISPONIVEL"];
                    $QTD_NOVA = $QTD_DISPONIVEL - $quantidade_excel;

                    $results .= "<tr>";
                    $results .= "<td>" . $codigo . "</td>";
                    $results .= "<td class='text-center'>" . $quantidade_excel . "</td>";
                    $results .= "<td class='text-center'>" . $QTD_DISPONIVEL . "</td>";
                    $results .= "<td class='text-center'>" . $QTD_NOVA . "</td>";
                    //$results .= "<td class='text-center'>" . $ref . "</td>";
                    $results .= "</tr>";
                }
            } else {
                $results .= "<tr>";
                $results .= "<td>" . $codigo . "</td>";
                $results .= "<td class='text-center'>" . $quantidade_excel . "</td>";
                $results .= "<td class='text-center'> N/A </td>";
                $results .= "<td class='text-center'> N/A </td>";
                //$results .= "<td class='text-center'>" . $ref . "</td>";
                $results .= "</tr>";
            }
        } else {
            die("Erro na execução da consulta: " . $stmt->error);
        }

        // Fechar o resultado e a declaração preparada
        $result->close();
        $stmt->close();
    }

    // Fechar a tag da tabela HTML
    $results .= "</table>";

    // Fechar a conexão com o banco de dados
    $conn->close();

    // Enviar os resultados de volta para o JavaScript
    echo $results;
?>
