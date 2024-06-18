<table class="table my-0" id="dataTable">
        <thead>
            <tr>                               
                <th style="font-size: 12px;">CODIGO</th>
                <th class="text-center" style="font-size: 12px;">QTD</th>
                <th class="text-center" style="font-size: 12px;">AÇÃO</th>
            </tr>
        </thead>
        <tbody>

<?php
// Conexão com o banco de dados - substitua com suas próprias credenciais
include 'conect_new.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

if(isset($_POST['search'])){
    $search = $_POST['search'];

    // Consulta ao banco de dados
    $sql = "SELECT * FROM bd_estoque WHERE CODIGO LIKE '%$search%'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            //echo "<p>" . $row["CODIGO"] . " QTD: " . $row["QTD_DISPONIVEL"] . "</p>"; // Aqui você pode personalizar o formato de exibição conforme necessário
            
    
?>

    

            <tr>
                <td style="font-size: 11px; width: 5%"><?php echo $row["CODIGO"]; ?></td>
                <td class="text-center" style="font-size: 11px; width: 5%"><?php echo $row["QTD_DISPONIVEL"]; ?></td>
                <td class="text-center" style="font-size: 11px; width: 5%">
                    <a href="#"><div style="display:none;"><?php echo $row["CODIGO"]; ?></div>
                        <i class="fas fa-plus-square" style="color: #439943; font-size: 15px; "></i>
                    </a>

                    
                </td>
            </tr>

        

<?php 

    }
        } else {
            echo "Nenhum resultado encontrado";
        }
    }

    $conn->close();



?>

</tbody>
    </table>