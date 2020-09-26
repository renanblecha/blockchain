<?php

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels great to relax.
|
*/

require __DIR__.'/vendor/autoload.php';


$privateKey =  "6ea53964897d997a70d28176383de3f3be4008084f9eeed75c8d3b272ef63f1f";
$publicKey = "03a9582ef6cb46fbf076c49cb7aa2a27e87099fed508a8e9ee6c07a17236736d9a";
$myAddress = "mmC6yEPDHQ2qYR1jtMQ3ipVAhvf1wataiQ";

$address25Percent = "mr9xV9aNnfv6a1LhGsvVmFGW63Mn3ZwtJz";
$addressBackFaucet = "mkHS9ne12qx9pS9VojpwU5xtRd4T7X7ZUt";

//https://bitcoinfees.earn.com/api/v1/fees/recommended
$feePerByte = 44;

//https://sochain.com/api/v2/get_tx_unspent/BTCTEST/mmC6yEPDHQ2qYR1jtMQ3ipVAhvf1wataiQ
$utxo = "656c1c3e3474af49957238a5911a0c2a138d73c11e2f95babe4a310f9e81d71e";
$utxoValue = 100000; //0.001 BTC;

$network = \BitWasp\Bitcoin\Bitcoin::getNetwork();
$ecAdapter = \BitWasp\Bitcoin\Bitcoin::getEcAdapter();
\BitWasp\Bitcoin\Bitcoin::setNetwork(\BitWasp\Bitcoin\Network\NetworkFactory::bitcoinTestnet());

$myTxo = \BitWasp\Bitcoin\Bitcoin::getTransaction($utxo, true);

$fee = $feePerByte * 526;
$input = new \BitWasp\Bitcoin\Transaction\TransactionInput($utxo, $utxoValue);
$output1 = new \BitWasp\Bitcoin\Transaction\TransactionOutput($utxoValue*0.25, $address25Percent);
$output2 = new \BitWasp\Bitcoin\Transaction\TransactionOutput($utxoValue*0.05, $myAddress);
$output3 = new \BitWasp\Bitcoin\Transaction\TransactionOutput((($utxoValue*0.70)-$fee), $addressBackFaucet);

$builderTransaction = new \BitWasp\Bitcoin\Transaction\TransactionBuilder($ecAdapter);
$builderTransaction->spendOutput($utxo, $fee)
    ->addInput($input)
    ->addOutput($output1)
    ->addOutput($output2)
    ->addOutput($output3)
;

$builderTransaction->signInputWithKey($privateKey, $myTxo->getOutputs()->getOutput($fee)->getScript(), 0);

\BitWasp\Bitcoin\Bitcoin::sendrawtransaction($builderTransaction, true);