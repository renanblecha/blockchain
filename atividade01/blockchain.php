<?php
class BlockChain
{
    private $nomes = ['Chase','Rennie','Franklin', 'Huynh', 'England', 'Lugo', 'Rodrigues', 'Betts', 'Cummings', 'Irwin', 'Nixon', 'Higgins', 'Cook', 'Ross', 'Eaton', 'Fountain'];
    private $template = "Ola <Destino>. Meu nome é <Origem>.";
    private $size = 0;
    private $chain = [];

    public function __construct()
    {
        $this->size = count($this->nomes);
    }

    private function writeBlock($numeroBloco, $origem, $destino)
    {
        $mensagem = "Origem: ".$origem.PHP_EOL."Destino: ".$destino.PHP_EOL."Mensagem: ".str_replace(['<Destino>','<Origem>'], [$destino, $origem], $this->template).PHP_EOL;
        file_put_contents("blocos/bloco_".$numeroBloco.".txt", $mensagem);
    }

    private function createChain(){
        for ($i = 0; $i < $this->size; $i++){
            $bloco = file_get_contents("blocos/bloco_".($i+1).".txt");
            if ($i > 0) {
                $blocoAnterior = file_get_contents("blocos/bloco_" . (($i+1) - 1) . ".txt");
            }else{
                $blocoAnterior = null;
            }
            $this->chain[] = [
                "Bloco" => $bloco,
                "Hash" => hash("sha256", $bloco),
                "Hash Anterior" => ($i > 0 ? hash("sha256", $blocoAnterior) : null),
            ];
        }
    }

    public function criaBlocos(){
        for ($i = 0; $i < $this->size; $i++){
            $this->writeBlock(($i+1), $this->nomes[$i], ($i == ($this->size-1) ? $this->nomes[0] : $this->nomes[($i+1)]));
        }
        $this->createChain();
    }

    public function getChain()
    {
        return $this->chain;
    }

    public function validaChain(array $chain){
        $size = count($chain);
        for ($i = 0; $i < $size; $i++){
            $hash = hash("sha256", $chain[$i]["Bloco"]);
            $hashAnterior = ($i > 0 ? hash("sha256", $chain[$i-1]["Bloco"]) : null);
            if ($hash != $chain[$i]["Hash"]){
                return [false, "Hash inválida no bloco: ".($i+1)];
            }

            if ($hashAnterior != $chain[$i]["Hash Anterior"]){
                return [false, "Hash Anterior inválida no bloco: ".($i+1)];
            }
        }

        return [true, "Chain validada com sucesso"];
    }

}

$blockChain = new BlockChain();
$blockChain->criaBlocos();
$chain = $blockChain->getChain();
echo "CHAIN: ".json_encode($chain)."<br><br>";
echo "1º Teste sem alteração ==> ";
list($isValid, $message) = $blockChain->validaChain($chain);
echo $message;
echo "<br>________________________________________________<br><br>";
echo "CHAIN: ".json_encode($chain)."<br><br>";
echo "2º Teste com alteração ==> ";
$chain[5]["Bloco"] .= "add mais conteudo no bloco no bloco 6";

list($isValid, $message) = $blockChain->validaChain($chain);
echo $message;

//echo '<pre>';print_r($chain);

