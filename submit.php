<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$sinif = $_POST['sinif'];
$numara = $_POST['numara'];

// Öğrenci bilgilerini kontrol et
$ogrenciListesi = file("ogrenciler/$sinif.txt");
$ogrenciBulundu = false;
$ad = '';
$soyad = '';

foreach ($ogrenciListesi as $satir) {
    list($ogrenciAd, $ogrenciSoyad, $ogrenciNumara) = explode(",", trim($satir));
    if ($ogrenciNumara == $numara) {
        $ogrenciBulundu = true;
        $ad = $ogrenciAd;
        $soyad = $ogrenciSoyad;
        break;
    }
}

if (!$ogrenciBulundu) {
    die("Öğrenci bulunamadı veya bilgiler yanlış!");
}

// Öğrencinin daha önce sınava girip girmediğini kontrol et
$dosyaYolu = "sonuclar/$sinif\_cevap.txt";
if (file_exists($dosyaYolu)) {
    $sonuclar = file($dosyaYolu);
    foreach ($sonuclar as $satir) {
        if (strpos($satir, "numara=$numara") !== false) {
            die("Bu öğrenci zaten sınava girdi!");
        }
    }
}

// Sınav sonuçlarını hesapla
$dogru = 0;
$yanlis = 0;
$bos = 0;

$kelimeler = file("kelimeler.txt");
foreach ($kelimeler as $index => $kelime) {
    list($ingilizce, $turkce) = explode("=", trim($kelime));
    $cevap = $_POST["kelime$index"] ?? '';
    if (empty($cevap)) {
        $bos++;
    } elseif ($cevap == $turkce) {
        $dogru++;
    } else {
        $yanlis++;
    }
}

// Sonuçları kaydet
$sonuc = "ad=$ad\nsoyad=$soyad\nnumara=$numara\ndogru=$dogru\nyanlis=$yanlis\nbos=$bos\n---------------------------------\n";

// Klasör yoksa oluştur
if (!file_exists("sonuclar")) {
    mkdir("sonuclar", 0777, true);
}

// Dosyaya yaz
if (file_put_contents($dosyaYolu, $sonuc, FILE_APPEND) === false) {
    die("Dosya yazma hatası: Lütfen klasör izinlerini kontrol edin.");
}

// Güzel bir arayüzle sonuç mesajı göster
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sonucu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card text-center">
            <div class="card-header bg-success text-white">
                Sınav Tamamlandı!
            </div>
            <div class="card-body">
                <h5 class="card-title">Sonuçlar Kaydedildi</h5>
                <p class="card-text">
                    Ad: <?php echo $ad; ?><br>
                    Soyad: <?php echo $soyad; ?><br>
                    Numara: <?php echo $numara; ?><br>
                    Doğru: <?php echo $dogru; ?><br>
                    Yanlış: <?php echo $yanlis; ?><br>
                    Boş: <?php echo $bos; ?>
                </p>
                <a href="index.php" class="btn btn-primary">Ana Sayfaya Dön</a>
            </div>
            <div class="card-footer text-muted">
                Teşekkürler!
            </div>
        </div>
    </div>
</body>
</html>