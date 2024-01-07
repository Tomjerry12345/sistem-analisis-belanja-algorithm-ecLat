<!DOCTYPE html>
<?php
$menu5 = "active";
error_reporting(0);
include "../config/koneksi.php";
$bulan = $_POST['bulan'];
$minggu = $_POST['minggu'];
$tahun = $_POST['tahun'];

$r_minsup = 0;

if (isset($_POST['kirim_minsup'])) {
    $minsup = $_POST['minsup'];
    $minconf = $_POST['minconf'];

    $q_minsup = "UPDATE tbl_minsup SET minsup = $minsup, minconf = $minconf";

    $r_minsup = $conn->query($q_minsup);

    // die(print_r($r_minsup));
}

$s_minsup = "SELECT * FROM tbl_minsup";
$ss_minsup = $conn->query($s_minsup);
// die(print_r($ss_minsup));
foreach ($ss_minsup as $sss) {
    $minimsl_sup = $sss['minsup'];
    $minimsl_conf = $sss['minconf'];
}

// if ($bulan != null ) include "eclat-table.php";

function calculateTIDsets($transactions)
{
    $tidsets = [];

    foreach ($transactions as $transaction) {
        $tid = $transaction['Transaksi_Id'];
        foreach ($transaction as $key => $value) {
            if ($key !== 'Transaksi_Id' && $key !== 'No' && $key !== 'tanggal' && $value !== null) {
                if (!isset($tidsets[$value])) {
                    $tidsets[$value] = [];
                }
                $tidsets[$value][] = $tid;
            }
        }
    }

    return $tidsets;
}

?>


<html>

<head>
    <title>Kombinasi Asosiasi</title>
    <?php include "head.php"; ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        select {
            padding: 7px;
        }

        @media (max-width: 768px) {
            select {
                width: 100%;
                box-sizing: border-box;
                /* Ini akan membuat padding dan border menjadi bagian dari lebar elemen, sehingga mencegah elemen melebihi 100% lebar */
            }

            button {
                margin-top: 12px;
            }
        }
    </style>
</head>

<body>

    <?php include "sidebar.php"; ?>

    <div class="content pb-3">
        <div class="container pt-3">
            <h3>Kombinasi Aturan Asosiasi
                <br>

            </h3>
            Kombinasi, Support A, Support A,B, Confidence (A,B)
            <hr>

            <?php

            if ($tahun != null) {
                $data = $conn->query("SELECT * FROM tbl_transaksi WHERE YEAR(tanggal) = $tahun AND MONTH(tanggal) = $bulan ORDER BY tanggal");

                // Hitung kembali jumlah baris dari hasil query
                $lengthData = $data->num_rows;

                $tidsets = calculateTIDsets($data);

                // Kemudian, Anda bisa menampilkan jumlah transaksi dengan nilai yang baru di bagian HTML yang sesuai
                echo '<font>Jumlah Transaksi Sebanyak ' . $lengthData . ' dengan Jumlah Item Sebanyak ' . count($tidsets) . `</font>`;
            }
            ?>
            <br>
            <br>
            <form action="" method="POST">
                <label for="minsup">Masukkan Minimal Support (%):</label>
                <input type="text" name="minsup" id="minsup" value="<?= $minimsl_sup ?>" style="padding: 5px; width: 50px;">

                <label for="minconf">Masukkan Minimal Confidance (%):</label>
                <input type="text" name="minconf" id="minconf" value="<?= $minimsl_conf ?>" style="padding: 5px; width: 50px;">
                <button type="submit" name="kirim_minsup" style="color: #fff; background-color: #007bff; border-color: #007bff; padding: 7px; border: none;">Lihat
                    Kirim</button>
            </form>
            <br>

            <form action="" method="POST">
                <label for="month">Pilih Bulan:</label>
                <select name="bulan" id="bulan" style="width: 120px;">
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
                <select name="tahun" id="tahun" style="width: 120px;">
                    <?php
                    $years = array('2021');
                    foreach ($years as $year) {
                        echo "<option";
                        if (isset($_POST['tahun']) && $_POST['tahun'] == $year) {
                            echo ' selected="selected"';
                        }
                        echo ">$year</option>";
                    }
                    ?>
                </select>
                <button type="submit" style="color: #fff; background-color: #007bff; border-color: #007bff; padding: 7px; border: none;">Lihat
                    Hasil</button>
            </form>

            <div class="mt-5">
                <div class="table-responsive">
                    <table id="example" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Kombinasi Item</th>
                                <th>Dukungan Support (A)</th>
                                <th>Dukungan Support (A,B)</th>
                                <th>Confidence (A,B)</th>
                            </tr>
                        </thead>

                        <?php

                        function logO($t, $m)
                        {
                            echo '<pre>';
                            print_r($t . ": " . json_encode($m, JSON_PRETTY_PRINT));
                            echo '</pre>';
                        }

                        function test()
                        {
                            logO("test", "");
                        }

                        // Fungsi untuk membaca data transaksi dari database
                        function bacaDataTransaksiDariDatabase($tahun, $bulan, $conn)
                        {
                            $data = [];
                            if ($tahun != null) {
                                $result = $conn->query("SELECT * FROM tbl_transaksi WHERE YEAR(tanggal) = $tahun AND MONTH(tanggal) = $bulan");

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $data[] = $row;
                                    }
                                }
                            }
                            return $data;
                        }

                        // Fungsi untuk menghitung dukungan dari itemset di seluruh transaksi
                        function hitungDukungan($dataTransaksi, $itemset)
                        {
                            $count = 0;
                            foreach ($dataTransaksi as $transaksi) {
                                $found = true;
                                foreach ($itemset as $item) {
                                    if (!in_array($item, $transaksi)) {
                                        $found = false;
                                        break;
                                    }
                                }
                                if ($found) {
                                    $count++;
                                }
                            }
                            return $count;
                        }

                        function ekstrakItemset($dataTransaksi, $minSupport)
                        {
                            $itemsCount = [];
                            $itemsets = []; // Penyimpanan semua itemset yang ditemukan

                            // Menghitung item yang muncul pada transaksi
                            foreach ($dataTransaksi as $transaksi) {
                                foreach ($transaksi as $key => $item) {
                                    if ($key !== 'Transaksi_Id' && $key !== 'No' && $key !== 'tanggal' && $item !== null && $item !== "") {
                                        if (!isset($itemsCount[$item])) {
                                            $itemsCount[$item] = 0;
                                        }
                                        $itemsCount[$item]++;
                                    }
                                }
                            }

                            // Mencari itemset dengan dukungan lebih dari minimum support
                            foreach ($itemsCount as $item => $support) {
                                if ($support >= $minSupport) {
                                    $itemsets[] = [[$item], $support]; // Itemset dengan satu item dan dukungan
                                }
                            }

                            // Implementasi ECLAT untuk mengekstrak itemset yang lebih besar
                            $k = 2;
                            while (true) {
                                $candidateItemsets = [];

                                $numItemsets = count($itemsets);
                                for ($i = 0; $i < $numItemsets - 1; $i++) {
                                    for ($j = $i + 1; $j < $numItemsets; $j++) {
                                        $itemset1 = $itemsets[$i][0]; // Ambil itemset pertama
                                        $itemset2 = $itemsets[$j][0]; // Ambil itemset kedua

                                        $newItemset = array_unique(array_merge($itemset1, $itemset2));
                                        sort($newItemset);

                                        if (count($newItemset) === $k) {
                                            $support = hitungDukungan($dataTransaksi, $newItemset);

                                            if ($support >= $minSupport) {

                                                $candidateItemsets[] = [$newItemset, $support]; // Tambahkan itemset yang ditemukan
                                            }
                                        }
                                    }
                                }

                                if (empty($candidateItemsets)) {
                                    break;
                                }

                                $itemsets = array_merge($itemsets, $candidateItemsets); // Gabungkan dengan itemset yang ditemukan

                                $serializedData = array_map('serialize', $itemsets);
                                $uniqueSerializedData = array_unique($serializedData);

                                $itemsets = [];
                                foreach ($uniqueSerializedData as $serializedItem) {
                                    $itemsets[] = unserialize($serializedItem);
                                }

                                // logO("itemsets", $itemsets);

                                $k++;
                            }

                            return $itemsets;
                        }

                        // Fungsi untuk menghasilkan aturan asosiasi dari itemset yang telah diekstrak
                        function generateAssociationRules($itemsets, $dataTransaksi)
                        {
                            $rules = [];

                            foreach ($itemsets as $itemset) {
                                $set = $itemset[0]; // Ambil itemset

                                // Jika ukuran itemset lebih dari 1, kita bisa membuat aturan asosiasi
                                if (count($set) > 1) {
                                    $allSubsets = generateSubsets($set);

                                    foreach ($allSubsets as $subset) {
                                        // Ambil subset dan buat aturan asosiasi
                                        $left = $subset;
                                        $right = array_diff($set, $subset);
                                        $supportA = hitungDukungan($dataTransaksi, $left);
                                        $supportAb = hitungDukungan($dataTransaksi, array_merge($left, $right));
                                        $confidence = $supportAb / $supportA;
                                        $rules[] = [
                                            'left' => $left,
                                            'right' => $right,
                                            'supportA' => $supportA,
                                            "supportAb" => $supportAb,
                                            "confidence" => $confidence
                                        ];
                                    }
                                }
                            }

                            return $rules;
                        }

                        // Fungsi untuk menghasilkan semua subset dari sebuah set item
                        function generateSubsets($set)
                        {
                            $subsets = [[]];
                            $n = count($set);

                            for ($i = 0; $i < $n; $i++) {
                                $temp = $subsets;
                                foreach ($temp as $tempVal) {
                                    $tempVal[] = $set[$i];
                                    sort($tempVal);
                                    $subsets[] = $tempVal;
                                }
                            }

                            return array_slice($subsets, 1); // Menghilangkan subset kosong
                        }



                        if ($tahun != null) {
                            $dataTransaksi = bacaDataTransaksiDariDatabase($tahun, $bulan, $conn);
                            $minSupport = $minimsl_sup; // Misalnya, atur minimum support

                            // Proses ECLAT
                            $itemsets = ekstrakItemset($dataTransaksi, $minSupport);

                            // logO("itemsets", $itemsets);

                            // Menjalankan fungsi untuk menghasilkan aturan asosiasi dari itemset yang ditemukan
                            $associationRules = generateAssociationRules($itemsets, $dataTransaksi);

                            // Menampilkan hasil aturan asosiasi
                            foreach ($associationRules as $rule) {
                                // echo implode(', ', $rule['left']) . " => " . implode(', ', $rule['right']) . " [SupportA: " . $rule['supportA'] . "]" . " [SupportAb: " . $rule['supportAB'] . "]<br>";
                                if (implode(', ', $rule['right']) != "") {
                                    echo "<tr>";
                                    echo "<td>" . implode(', ', $rule['left']) . " maka akan membeli " .  implode(', ', $rule['right']) . "\n";
                                    echo "<td>" . $rule['supportA'] . "</td>";
                                    echo "<td>" . $rule['supportAb'] . "</td>";
                                    echo "<td>" . $rule['confidence'] . "</td>";
                                    echo "</tr>";
                                }
                            }
                        }
                        ?>
                    </table>
                </div>
            </div>
        </div>

        <?php include "scripts.php"; ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#example').DataTable();
            });
        </script>
</body>

</html>