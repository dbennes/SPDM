<?php

include 'conect_new.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Query para limpar a tabela bd_pendencia
$query = "TRUNCATE TABLE bd_pendencia";


echo "foi";

if ($conn->query($query) === TRUE) {
    echo "Tabela bd_pendencia limpa com sucesso!";
} else {
    echo "Erro ao limpar a tabela bd_pendencia: " . $conn->error;
}

// Fechar a conexão
$conn->close();

?>
