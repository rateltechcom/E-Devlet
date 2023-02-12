<?php include $_SERVER["DOCUMENT_ROOT"] . "/inc/autoload.php";


if (isset($_POST["tcKimlikNo"]) && isset($_POST["ad"]) && isset($_POST["soyad"]) && isset($_POST["dogumYili"])) {
    $tcKimlikNo = $_POST["tcKimlikNo"];
    $ad = $_POST["ad"];
    $soyad = $_POST["soyad"];
    $dogumYili = $_POST["dogumYili"];

    echo tcNoSorgulama($tcKimlikNo,$ad, $soyad, $dogumYili);
}





function tcNoSorgulama(String $tcKimlikNo,String $ad, String $soyad,String $dogumYili){
		
    $ad = karakterDuzelt($ad);
    $soyad= karakterDuzelt($soyad);

    try {

    $veriler = array(
        'TCKimlikNo' => $tcKimlikNo,
        'Ad' => $ad,
        'Soyad' => $soyad,
        'DogumYili' => $dogumYili
    );

    $baglan = new SoapClient("https://tckimlik.nvi.gov.tr/Service/KPSPublic.asmx?WSDL");
    $sonuc = $baglan -> TCKimlikNoDogrula ($veriler);
    if ($sonuc->TCKimlikNoDogrulaResult) {
        return 1;
    }
    else {
        return 0;
    }
    } catch (\Exception $e) {
      return 0;
    }
}
?>