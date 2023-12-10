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

            <font style="font-family: 'Lucida Sans';" color=red>Jumlah Transaksi Sebanyak 1356 dengan Jumlah Item Sebanyak 282</font>
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

                        // Fungsi untuk menggabungkan itemset dengan ukuran yang dinamis
                        function combineItems($itemset, $tidsets, $support)
                        {
                            $combinedSet = call_user_func_array('array_intersect', array_map(function ($item) use ($tidsets) {
                                return $tidsets[$item];
                            }, $itemset));

                            if (count($combinedSet) >= $support) {
                                return $combinedSet; // Return the combined set
                            }

                            return [];
                        }

                        function findAssociation($tidsets, $purchases, $support)
                        {
                            $associations = [];

                            $itemsCount = count($purchases);
                            $maxSize = count($purchases); // Atur ukuran maksimum untuk itemset yang akan diuji

                            for ($size = 1; $size <= $maxSize; $size++) {
                                for ($i = 0; $i < $itemsCount - $size + 1; $i++) {
                                    $itemset = array_slice($purchases, $i, $size);
                                    $combinedSet = combineItems($itemset, $tidsets, $support);

                                    if (!empty($combinedSet)) {
                                        $associations[implode(',', $itemset)] = [
                                            "transaksi_id" => $combinedSet,
                                            "items" => $itemset,
                                            "support" => count($combinedSet),
                                        ];
                                    }
                                }
                            }


                            return $associations;
                        }


                        function calculateSupport($tidsets, $items, $sizeData)
                        {
                            $sumSupport = 0;

                            foreach ($items as $i) {
                                $getCount = count($tidsets[$i]);
                                $sumSupport += $getCount;
                            }

                            // logO("sumSupport", $sumSupport);

                            return ($sumSupport / $sizeData) * 100;
                        }

                        if ($tahun != null) {
                            $data = $conn->query("SELECT * FROM tbl_transaksi WHERE YEAR(tanggal) = $tahun AND MONTH(tanggal) = $bulan");
                            // $data = $conn->query("SELECT * FROM tbl_transaksi");

                            $lengthData = $data->num_rows;

                            $tidsets = calculateTIDsets($data);

                            $output = [];

                            foreach ($data as $d) {
                                $itemA = [
                                    $d["Item_1"], $d["Item_2"], $d["Item_3"], $d["Item_4"], $d["Item_5"], $d["Item_6"], $d["Item_7"],
                                    $d["Item_8"], $d["Item_9"], $d["Item_10"], $d["Item_11"], $d["Item_12"], $d["Item_13"], $d["Item_14"], $d["Item_15"], $d["Item_16"]
                                ];

                                $itemA = array_filter($itemA);

                                $associatedItems = findAssociation($tidsets, $itemA, $minimsl_sup);

                                // logO("associatedItems", $associatedItems);

                                if (!empty($associatedItems)) {

                                    // return;
                                    $last = end($associatedItems);

                                    // logO("last", $last);

                                    // arsort($associatedItems);

                                    // $last = reset($associatedItems);


                                    $resultTranscation = $last["transaksi_id"];
                                    $resultItems = $last["items"];
                                    $supportA = $last["support"];

                                    $resultB = [];





                                    foreach ($resultTranscation as $result) {
                                        foreach ($tidsets as $key => $value) {
                                            // Jika transaksi tersebut ada di dalam nilai array tidsets, ambil kuncinya
                                            if (in_array($result, $value)) {
                                                $resultB[] = $key;
                                            }
                                        }
                                    }


                                    $resultB = array_diff($resultB, $resultItems);



                                    $resultB = array_filter($resultB);


                                    $resultB = array_count_values($resultB);
                                    arsort($resultB);

                                    // logO("resultB", $resultB);

                                    $firstItem = reset($resultB);

                                    // logO("firstItem", $firstItem);

                                    if ($firstItem !== false) {
                                        $itemB = key($resultB);
                                        $support = $firstItem;

                                        $resultB = [
                                            "item" => $itemB,
                                            "support" => $support
                                        ]; // Menggunakan indeks 0 untuk mengambil kunci pertama

                                        // logO("resultB", $resultB);

                                        $output[] = [
                                            "desc" => "Jika membeli " . implode(' dan ', $itemA) . " maka akan membeli " . $resultB["item"],
                                            "supportA" => $supportA,
                                            "supportB" => $resultB["support"],
                                        ];
                                    }
                                }
                            }

                            $output = array_map("unserialize", array_unique(array_map("serialize", $output)));



                            // logO("output", $output);

                            foreach ($output as $o) {
                                echo "<tr>";
                                echo "<td>" . $o["desc"];
                                echo "<td>" . $o["supportA"] . "</td>";
                                echo "<td> " . $o["supportB"] . "</td>";
                                echo "<td>" . $o["supportA"] / $o["supportB"] . "</td>";
                                echo "</tr>";
                                // return;
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