<?php

session_start();

//Conecta ao Banco
include ('../../../../php/conect/conect.php');

//Verifica o Login
include('../../../../php/funcoes/login/verificalogin.php');


$email = $_POST['email'];
$novasenha = MD5($_POST['novasenha']);

echo $email;
echo $novasenha;

$sql = "UPDATE user SET senha='$novasenha' WHERE email= '$email' ";

if (mysqli_query($conecta, $sql)) {
  echo "Salva com sucesso";
} else {
  echo "Erro para gravar: " . mysqli_error($conecta);
}

header('Location: ../../../perfil.php');

mysqli_close($conecta);


?>

