<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sinif = $_GET['sinif'] ?? '';
$dosya = "ogrenciler/$sinif.txt";

if (file_exists($dosya)) {
    $ogrenciler = [];
    $satirlar = file($dosya);

    foreach ($satirlar as $satir) {
        list($ad, $soyad, $numara) = explode(",", trim($satir));
        $ogrenciler[] = [
            'ad' => $ad,
            'soyad' => $soyad,
            'numara' => $numara
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($ogrenciler);
} else {
    header('Content-Type: application/json');
    echo json_encode([]);
}
?>