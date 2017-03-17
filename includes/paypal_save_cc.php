<?php
require_once CRAFTYKITS_PAYPAL_LOADER_PATH.'bootstrap.php';
use PayPal\Api\CreditCard;
$error = "";
$data = "";
$card = new CreditCard(); 
$card->setType( $cc_info['type'] ) 
        ->setNumber( $cc_info['number'] ) 
        ->setExpireMonth( $cc_info['exp_month'] ) 
        ->setExpireYear( $cc_info['exp_year'] ) 
        ->setCvv2( $cc_info['cvv'] );

$request = clone $card;

try { 
    $card->create($apiContext);
    $json_data = $card->toJSON(128);
    $data = json_decode( $json_data );
}catch (Exception $ex) { 
   $error = json_decode( $ex->getData() );
}