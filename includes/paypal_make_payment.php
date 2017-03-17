<?php
require_once CRAFTYKITS_PAYPAL_LOADER_PATH.'bootstrap.php';
use PayPal\Api\Amount; 
use PayPal\Api\Details; 
use PayPal\Api\Item; 
use PayPal\Api\ItemList; 
use PayPal\Api\CreditCard; 
use PayPal\Api\Payer; 
use PayPal\Api\Payment; 
use PayPal\Api\FundingInstrument; 
use PayPal\Api\Transaction;

$error = "";
$data = "";

$card = new CreditCard(); 
$card->setType($cc_info['type']) ->setNumber($cc_info['number']) ->setExpireMonth($cc_info['exp_month']) ->setExpireYear($cc_info['exp_year']) ->setCvv2($cc_info['cvv']);

$fi = new FundingInstrument(); 
$fi->setCreditCard($card);

$payer = new Payer(); 
$payer->setPaymentMethod("credit_card") ->setFundingInstruments(array($fi));


$itemList = new ItemList(); 
//$itemList->setItems(array($item1));

$details = new Details(); 
//$details->setShipping(0) ->setTax(0) ->setSubtotal(17.5);

$amount = new Amount(); $amount->setCurrency("USD") ->setTotal($payment_info['amount']);

$transaction = new Transaction(); 
$transaction->setAmount($amount) ->setDescription($payment_info['detail']) ->setInvoiceNumber(uniqid());

$payment = new Payment(); 
$payment->setIntent("sale") ->setPayer($payer) ->setTransactions(array($transaction));

$request = clone $payment;

try { 
    $payment->create($apiContext);
    $json_data = $payment->toJSON(128);
    $data = json_decode( $json_data );
}catch (Exception $ex) { 
   $error = json_decode( $ex->getData() );
}