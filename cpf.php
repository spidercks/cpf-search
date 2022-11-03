<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
$cpf = $_GET['cpf'];
$cpf = trim($cpf);
$cpf = str_replace(".", "", $cpf);
$cpf = str_replace("-", "", $cpf);
$cpf = str_replace(" ", "", $cpf);
$cpf = str_replace("-", "", $cpf);

$fusohorario = json_decode(file_get_contents("http://worldtimeapi.org/api/timezone/America/Sao_Paulo"));
$unixtime = $fusohorario->unixtime;
$datetime = $fusohorario->datetime;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://seuplanoonline.fisistemas.com.br/ajax/cpf.php');
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_ENCODING, "gzip");
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Host: seuplanoonline.fisistemas.com.br',
  'Accept: */*',
  'X-Requested-With: XMLHttpRequest',
  'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36',
  'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
  'Origin: https://seuplanoonline.fisistemas.com.br',
  'Accept-Language: pt-BR,pt;q=0.9,en-US;q=0.8,en;q=0.7'
));
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'cpfTitular='.$cpf.'&singular=26&tipoTitular=TI&ans=36156-');
$resposta = curl_exec($ch);

$strings = json_decode($resposta);
$cns = $strings->cns;
$nome = $strings->nomeCompleto;
$dataNascimento = $strings->dtNascimento;
$nomeMae = $strings->nomeMae;

if (strpos($resposta, '0,')) {
  header("HTTP/1.1 200 OK");
  $json = [
    "statuscode" => 200,
    "resposta" => "CONSULTA REALIZADA",
    "cpf" => "$cpf",
    "cns" => "$cns",
    "nomeCompleto" => "$nome",
    "nomeMae" => "$nomeMae",
    "nascimento" => "$dataNascimento"
  ];
  
} else {
  header("Status: 404 Not Found");
  $json = [
    "statuscode" => 404,
    "resposta" => "CPF NÃO ENCONTRADO",
     "cpf" => "$cpf",
    "cns" => "Não encontrado",
    "nomeCompleto" => "Não encontrado",
    "nomeMae" => "Não encontrado",
    "nascimento" => "Não encontrado"
  ];
}
print_r(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));