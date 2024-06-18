<?php
require("vendor/autoload.php");

$pagarme = new PagarMe\Client('pk_4V0j62EHbcNzjxB5');

$transaction = $pagarme->transactions()->create([
  'amount' => 1,
  'payment_method' => 'boleto',
  'customer' => [
    'document_number' => '11111111111',
    'name' => 'daniel',
    'email' => 'dbennes97@gmail.com'
  ]
]);