<h1>Proses Upload Data</h1>
<?php
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
