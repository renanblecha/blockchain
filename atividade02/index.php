<?php

function dd()
{
    $args = func_get_args();

    foreach ($args as $arg) {
        echo "<pre>";
        print_r($arg);
        echo "</pre>";
    }

    die;
}

class BlockChain
{
    private $names = ['Betts','Chase','Cook','Cummings','Eaton','England','Fountain','Franklin','Higgins','Huynh','Irwin','Lugo','Nixon','Rennie','Rodrigues','Ross'];
    private $size = 0;
    private $messages = [];

    public function __construct()
    {
        $this->size = count($this->names);
        $this->readMessages();
    }

    private function readMessages()
    {
        $this->messages = glob('Mensagens/*.txt');
    }

    public function getMessages()
    {
        return $this->messages;
    }

    public function getNames()
    {
        return $this->names;
    }

    public function getRecipient($message)
    {
        $startIndex = strpos($message, 'h ');
        $endIndex = strpos($message, '. ');
        $recipient = substr($message, ($startIndex+2), ($endIndex-($startIndex+2)));
        return $recipient;
    }

    public function getSender($message)
    {
        $startIndex = strpos($message, ': ');
        $sender = substr($message, ($startIndex+2));
        return $sender;
    }

    public function encrypt($data, $key,  $type = 'public')
    {
        if ($type == 'private')
            $result = openssl_private_encrypt($data, $encrypted, $key);
        else
            $result = openssl_public_encrypt($data, $encrypted, $key);

        if ($result)
            $data = $encrypted;
        else
            throw new Exception('Unable to encrypt data.');

        return $data;
    }

    public function decrypt($data, $key,  $type = 'public')
    {
        if ($type == 'private')
            $result = openssl_private_decrypt($data, $decrypted, $key);
        else
            $result = openssl_public_decrypt($data, $decrypted, $key);

        if ($result)
            $data = $decrypted;
        else
            $data = '';

        return $data;
    }

}

$blockChain = new BlockChain();
$messages = $blockChain->getMessages();
$names = $blockChain->getNames();
foreach ($messages as $message){
    $originalMessageBase64 = file_get_contents($message);
    $originalMessageRSA = base64_decode($originalMessageBase64);
    foreach ($names as $sender){
        $privateKey = file_get_contents("Chaves/{$sender}_private_key.pem");
        $publicKey = file_get_contents("Publicas/{$sender}_public_key.pem");
        if ($messageDecrypted = $blockChain->decrypt($originalMessageRSA, $privateKey, 'private')){
            echo "<strong>ARQUIVO:</strong> ".$message."<br>";
            echo "<strong>CONTEUDO:</strong> ".$messageDecrypted."<br>";
            echo "<strong>REMETENTE:</strong> ".$blockChain->getSender($messageDecrypted)."<br>";
            echo "<strong>DESTINATARIO:</strong> ".$blockChain->getRecipient($messageDecrypted)."<br>";
            echo "<strong>REMETENTE REAL:</strong> <span style='background-color: ".($sender == $blockChain->getSender($messageDecrypted) ? "green" : "red")."'>{$sender}</span><br>";
        }
    }
    echo "<br>________________________________________________<br><br>";
}