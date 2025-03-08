<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sinif = $_POST['sinif'];
    $sonuclar = file("sonuclar/$sinif\_cevap.txt");

    echo "<h1>$sinif Sınıfı Sonuçları</h1>";
    foreach ($sonuclar as $sonuc) {
        echo nl2br($sonuc);
    }
}
?>

<form method="POST">
    <label for="sinif">Sınıf Seç:</label>
    <select name="sinif" required>
        <option value="10-A">10-A</option>
        <option value="10-B">10-B</option>
        <option value="10-C">10-C</option>
        <option value="10-D">10-D</option>
    </select>
    <button type="submit">Göster</button>
</form>