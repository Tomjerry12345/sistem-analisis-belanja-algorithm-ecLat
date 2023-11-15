<?php
// Koneksi ke database MySQL
include "koneksi.php";

// Query SQL untuk menghitung keuntungan dengan mengalikan kolom 'margin' dengan jumlah baris
$query = "SELECT SUM(margin * count) AS keuntungan_rp FROM tbl_infaq";

$result = $conn->query($query);

if ($result) {
    // Ambil hasil query
    $row = $result->fetch_assoc();
    
    // Keuntungan dalam format Rupiah (dengan pemisah ribuan)
    $keuntungan_rp = number_format($row["keuntungan_rp"]);

    echo "Keuntungan (Rp): " . $keuntungan_rp;
} else {
    echo "Tidak ada data.";
}

// Tutup koneksi database
$conn->close();
?>
