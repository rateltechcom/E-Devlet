<?php
require "vendor/autoload.php";
$endpoint = 'https://www.turkiye.gov.tr';
$client = new \GuzzleHttp\Client([
    'base_uri' => $endpoint,
			'timeout' => 5,
			'cookies' => true,
			'headers' => ['User-Agent' => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36']
]);
$file;

$response = $client->request('POST', '/belge-dogrulama')->getBody();

$token = trim(getPart('~data-token="(.+?)"~', $response));
$tc = $_POST["tcKimlikNo"];
$barkod = $_POST["barkod"];

//echo "Token:";
//print_r ($token);
//echo "</br>";
$response = $client->request('POST', '/belge-dogrulama?submit', [
    'form_params' => [
        'sorgulananBarkod' => $barkod,
        'token' => $token,
        'btn' => 'Devam Et'
    ]
])->getBody();

$token = getPart('~data-token="(.+?)"~', $response);
//echo "New Token 1: $token </br>";
if (strpos($response, 'Kimlik Numarası') === false) {
    print_r('Cannot find Kimlik Numarası </br>');
}

$response2 = $client->request('POST', 'belge-dogrulama?islem=dogrulama&submit', [
    'form_params' => [
        'ikinciAlan' => $tc,
        'token' => $token,
        'btn' => 'Devam Et'
    ]
])->getBody();


$token = getPart('~data-token="(.+?)"~', $response2);
//echo "New Token 2: $token </br>";

$response3 = $client->request('POST', 'belge-dogrulama?islem=onay&submit', [
    'form_params' => [
        'chkOnay' => 1,
        'token' => $token,
        'btn' => 'Devam Et'
    ]
])->getBody();



$pdfLink = "/belge-dogrulama?belge=goster&goster=1";


$remoteFile = $client->request('GET',$pdfLink);
		if ($remoteFile->getStatusCode() !== 200) {
			print_r('Uzak Pdf download edilemedi');
		}


$temp_file = tempnam(sys_get_temp_dir(), $tc.'_');
rename($temp_file, $temp_file .= '.pdf');
//echo $temp_file;

file_put_contents($temp_file, $remoteFile->getBody());

// Parse PDF file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile($temp_file);

$text = $pdf->getText();
//echo $text;


if (strpos($text, 'KAHRAMANMARAŞ') == true || strpos($text, 'GAZİANTEP') == true || strpos($text, 'ADIYAMAN') == true || strpos($text, 'HATAY') == true || strpos($text, 'KİLİS') == true || strpos($text, 'MALATYA') == true || strpos($text, 'OSMANİYE') == true) {
    print_r('YES');
}
else 
{
    print_r('NO');
}



function getPart($regex, $string) {
    $m = array();
    preg_match($regex, $string, $m);
    return $m && isset($m[1]) ? $m[1] : null;
}



?>
