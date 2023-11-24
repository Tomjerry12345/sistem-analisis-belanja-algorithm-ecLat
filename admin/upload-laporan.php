<!DOCTYPE html>
<html>
<head>
    <title>Upload File CSV</title>
    <!-- Tambahkan link ke Bootstrap CSS di sini -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <!-- <div class="container mt-5">
        <h2>Upload File CSV Untuk Proses Analisis Belanja</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csvFile">Pilih file CSV:</label>
                <input type="file" class="form-control-file" name="csvFile" id="csvFile" required>
            </div>
            <button type="submit" name="uploadBtn" class="btn btn-primary">Mulai Upload</button>
        </form> -->
    
        <?php
        if (isset($_POST["uploadBtn"])) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . "data.csv";
            $fileType = strtolower(pathinfo($_FILES["csvFile"]["name"], PATHINFO_EXTENSION));

            // Hapus file data.csv jika sudah ada
            if (file_exists($targetFile)) {
                unlink($targetFile);
            }

            // Cek apakah file adalah CSV
            if ($fileType != "csv") {
                echo '<div class="alert alert-danger mt-3">Hanya file CSV yang diizinkan.</div>';
            } else {
                // Pindahkan file ke direktori tujuan dan ubah namanya menjadi data.csv
                if (move_uploaded_file($_FILES["csvFile"]["tmp_name"], $targetFile)) {
                    //
                    include "koneksi.php";
                    $sql = "TRUNCATE TABLE tbl_transaksi";
                    $result = $conn->query($sql);

                    // Lokasi dan nama file CSV
                    $csvFile = 'uploads/data.csv';

                    // Baca file CSV
                    $file = fopen($csvFile, 'r');
                    if ($file) {
                        // Lewati baris header
                        fgetcsv($file);

                        while (($line = fgetcsv($file)) !== false) {
                            $escapedValues = array_map(function ($value) use ($conn) {
                                return "'" . mysqli_real_escape_string($conn, $value) . "'";
                            }, $line);

                            $sql = "INSERT INTO tbl_transaksi (No, tanggal, Transaksi_Id, Item_1, Item_2, Item_3, Item_4, 
                                    Item_5, Item_6, Item_7, Item_8, Item_9, Item_10, Item_11, Item_12, Item_13, Item_14, Item_15, Item_16) VALUES (" . implode(",", $escapedValues) . ")";
                            
                            if ($conn->query($sql) !== true) {
                                //echo "Error: " . $sql . "<br>" . $conn->error;
                            }
                        }

                        fclose($file);
                        header("Location: hasil.php");
                        exit;
                    } else {
                        //echo "Gagal membuka file CSV";
                    }
                    //
                } else {
                    echo '<div class="alert alert-danger mt-3">Terjadi kesalahan saat mengunggah file.</div>';
                }
            }
        }
        ?>
    </div>

    <!-- Tambahkan link ke Bootstrap JS di sini jika diperlukan -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
