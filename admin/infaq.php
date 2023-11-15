<!DOCTYPE html>
<?php $menu6="active"; ?>
<?php
    include "../config/koneksi.php";

    $bulann = 0;
    $tahunn = 0;
    if (isset($_POST['lihat'])) {
        $bulann = $_POST['bulan'];
        $tahunn = $_POST['tahun'];

        // die(print_r($tahunn));
    }

    $query  = "SELECT SUM(margin) AS keuntungan_rp FROM tbl_infaq WHERE date LIKE '%%/$bulann/$tahunn%%' ";
    $result = $conn->query($query);

    if ($result) {
        $row = $result->fetch_assoc();
        $keuntungan_rp = number_format($row["keuntungan_rp"]);
    } else {
        $keuntungan_rp = "Tidak ada data";
    }
    $persentase = 2.5;
    $hasil = ($persentase / 100) * $row["keuntungan_rp"];
    $conn->close();
?>

<html>

<head>
    <title>Infaq</title>
    <?php include "head.php"; ?>
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="content">
        <div class="container pt-3">
            <h3>Informasi Infaq</h3>
            <hr>

        <form action="" method="POST">
        <label for="month">Pilih Bulan:</label>
        <select name="bulan" id="bulan" style="padding: 7px">
            <?php 
            $months = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
            foreach ($months as $key => $value) {
                echo "<option value=\"$key\"";
                if (isset($_POST['bulan']) && $_POST['bulan'] == $key) {
                    echo ' selected="selected"';
                }
                echo ">$value</option>";
            }
            ?>
        </select>

        <label for="tahun">Pilih Tahun:</label>
        <select name="tahun" id="tahun" style="padding: 7px">
            <?php 
            $years = array(1 => '2021');
            foreach ($years as $key => $value) {
                echo "<option value=\"$value\"";
                if (isset($_POST['tahun']) && $_POST['tahun'] == $key) {
                    echo ' selected="selected"';
                }
                echo ">$value</option>";
            }
            ?>
        </select>
        <button type="submit" name="lihat"
                    style="color: #fff; background-color: #007bff; border-color: #007bff; padding: 7px; border: none;">Lihat
                    Hasil</button>
            </form>
            <br>

        
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Laba Bersih</h5>
                                <p class="card-text">Rp. <?php echo $keuntungan_rp; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title">Biaya Infaq (2.5% dari Laba Bersih)</h5>
                                <p class="card-text">Rp. <?php echo number_format($hasil); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <?php include "scripts.php"; ?>
</body>

</html>