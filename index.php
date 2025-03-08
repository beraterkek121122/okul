<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sınav Sayfası</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .kelime-cifti {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .kelime-cifti div {
            width: 48%;
        }
        .kelime-cifti label {
            font-weight: bold;
        }
        #timer {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-size: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Geri Sayım Sayacı -->
    <div id="timer">25:00</div>

    <div class="container mt-5">
        <h1 class="text-center">Sınav Sayfası</h1>
        <form id="sinavForm" action="submit.php" method="POST">
            <!-- Sınıf Seç -->
            <div class="mb-3">
                <label for="sinif" class="form-label">Sınıf Seç</label>
                <select class="form-select" id="sinif" name="sinif" required onchange="ogrencileriYukle()">
                    <option value="">Sınıf Seçin</option>
                    <option value="10-A">10-A</option>
                    <option value="10-B">10-B</option>
                    <option value="10-C">10-C</option>
                    <option value="10-D">10-D</option>
                </select>
            </div>

            <!-- Öğrenci Seç -->
            <div class="mb-3">
                <label for="ogrenci" class="form-label">Öğrenci Seç</label>
                <select class="form-select" id="ogrenci" name="ogrenci" required>
                    <option value="">Önce sınıf seçin</option>
                </select>
            </div>

            <!-- Okul Numarası -->
            <div class="mb-3">
                <label for="numara" class="form-label">Okul Numarası</label>
                <input type="text" class="form-control" id="numara" name="numara" required>
                <div id="numaraUyari" class="text-danger" style="display: none;">Bu numara sınıf listesinde bulunamadı!</div>
            </div>

            <!-- Kelime Soruları -->
            <?php
            $kelimeler = file("kelimeler.txt"); // kelimeler.txt dosyasını oku
            $kelimeCiftleri = array_chunk($kelimeler, 2); // Kelimeleri 2'li gruplara ayır

            foreach ($kelimeCiftleri as $cift) {
                echo '<div class="kelime-cifti">';
                foreach ($cift as $index => $kelime) {
                    list($ingilizce, $turkce) = explode("=", trim($kelime));
                    echo '<div>
                            <label for="kelime'.$index.'">'.$ingilizce.'</label>
                            <input type="text" class="form-control" id="kelime'.$index.'" name="kelime'.$index.'" placeholder="Türkçe karşılığını yazın">
                          </div>';
                }
                echo '</div>';
            }
            ?>

            <!-- Gönder Butonu -->
            <button type="submit" class="btn btn-primary">Gönder</button>
        </form>
    </div>

    <!-- JavaScript ile Öğrencileri Yükleme -->
    <script>
        function ogrencileriYukle() {
            const sinif = document.getElementById('sinif').value;
            const ogrenciDropdown = document.getElementById('ogrenci');

            // Dropdown'u temizle
            ogrenciDropdown.innerHTML = '<option value="">Öğrenci Seçin</option>';

            if (sinif) {
                // AJAX ile öğrenci bilgilerini al
                fetch(`ogrencileriGetir.php?sinif=${sinif}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(ogrenci => {
                            const option = document.createElement('option');
                            option.value = ogrenci.numara; // Öğrenci numarasını value olarak kullan
                            option.textContent = `${ogrenci.ad} ${ogrenci.soyad}`;
                            ogrenciDropdown.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Hata:', error));
            }
        }

        // Okul numarası kontrolü
        document.getElementById('numara').addEventListener('input', function() {
            const numara = this.value;
            const sinif = document.getElementById('sinif').value;
            const numaraUyari = document.getElementById('numaraUyari');

            if (sinif && numara) {
                fetch(`ogrencileriGetir.php?sinif=${sinif}`)
                    .then(response => response.json())
                    .then(data => {
                        const ogrenciBulundu = data.some(ogrenci => ogrenci.numara === numara);
                        if (ogrenciBulundu) {
                            numaraUyari.style.display = 'none';
                        } else {
                            numaraUyari.style.display = 'block';
                        }
                    })
                    .catch(error => console.error('Hata:', error));
            } else {
                numaraUyari.style.display = 'none';
            }
        });

        // Geri Sayım Sayacı
        let time = 25 * 60; // 25 dakika (saniye cinsinden)
        const timerElement = document.getElementById('timer');

        const interval = setInterval(() => {
            const minutes = Math.floor(time / 60);
            const seconds = time % 60;
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            time--;

            if (time < 0) {
                clearInterval(interval);
                timerElement.textContent = "Süre Doldu!";
                document.getElementById('sinavForm').submit(); // Formu otomatik gönder
            }
        }, 1000);
    </script>
</body>
</html>