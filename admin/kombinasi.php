<!DOCTYPE html>
<?php
$menu5 = "active";
error_reporting(0);
include "../config/koneksi.php";
$bulan = $_POST['bulan'];
$minggu = $_POST['minggu'];
$tahun = $_POST['tahun'];

$r_minsup = 0;

$lengthData = 0;

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

$jumlahTransaksi = 0;

// if ($bulan != null ) include "eclat-table.php";

function logO($t, $m)
{
    echo '<pre>';
    print_r($t . ": " . json_encode($m, JSON_PRETTY_PRINT));
    echo '</pre>';
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


function findAssociation($tidsets, $purchases)
{
    $associatedItems = [];
    $transactionList = [];

    foreach ($purchases as $purchase) {
        $transactionList[] = $tidsets[$purchase];
    }

    // Menemukan item yang ada di semua transaksi
    $commonTransactions = call_user_func_array('array_intersect', $transactionList);

    // Menghitung jumlah kemunculan item terkait
    $itemCount = [];

    foreach ($commonTransactions as $commonTransaction) {
        foreach ($tidsets as $item => $tidList) {
            if (in_array($commonTransaction, $tidList) && !in_array($item, $purchases)) {
                if (!isset($itemCount[$item])) {
                    $itemCount[$item] = 1;
                } else {
                    $itemCount[$item]++;
                }
            }
        }
    }

    $associatedItems = array_filter($itemCount, function ($key) {
        return $key !== "";
    }, ARRAY_FILTER_USE_KEY);

    // Mengurutkan berdasarkan jumlah kemunculan
    arsort($associatedItems);

    return array_keys($associatedItems);
}

function calculateSupport($tidsets, $items, $sizeData, $i)
{
    $transactionList = [];

    foreach ($items as $associatedItem) {
        $transactionList[] = $tidsets[$associatedItem];
    }

    $commonTransactions = call_user_func_array('array_intersect', $transactionList);

    $support = ((count($commonTransactions)) / $sizeData) * 100;

    logO("items", $items);
    logO("transactionList", $transactionList);
    logO("commonTransactions", $commonTransactions);
    logO("sizeData", $sizeData);
    logO("support", $support);
    logO("=", "===========");

    return $support;
}

function calculateSupportAB($tidsets, $items, $associatedItems, $sizeData)
{

    $transactionList = [];

    foreach (array_merge($items, $associatedItems) as $associatedItem) {
        $transactionList[] = $tidsets[$associatedItem];
    }

    $commonTransactions = call_user_func_array('array_intersect', $transactionList);


    return ((count($commonTransactions)) / $sizeData) * 100;
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
                <label for="minsup" style="color:black">Masukkan Minimal Support (%):</label>
                <input type="text" name="minsup" id="minsup" value="<?= $minimsl_sup ?>" style="padding: 5px; width: 50px;">

                <label style="color:black" for="minconf">Masukkan Minimal Confidance (%):</label>
                <input type="text" name="minconf" id="minconf" value="<?= $minimsl_conf ?>" style="padding: 5px; width: 50px;">
                <button type="submit" name="kirim_minsup" style="color: #fff; background-color: #007bff; border-color: #007bff; padding: 7px; border: none;">Lihat
                    Kirim</button>
            </form>
            <br>

            <form action="" method="POST">
                <label style="color:black" for="month">Pilih Bulan:</label>
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

                <label style="color:black" for="tahun">Pilih Tahun:</label>
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

                        if ($tahun != null) {
                            $data = $conn->query("SELECT * FROM tbl_transaksi WHERE YEAR(tanggal) = $tahun AND MONTH(tanggal) = $bulan ORDER BY tanggal");
                            // $data = $conn->query("SELECT * FROM tbl_transaksi");

                            $lengthData = $data->num_rows;

                            $tidsets = calculateTIDsets($data);

                            $output = [];

                            $i = 0;


                            foreach ($data as $d) {
                                $itemsToAnalyze = [
                                    $d["Item_1"], $d["Item_2"], $d["Item_3"],
                                    $d["Item_4"], $d["Item_5"],
                                ];

                                $itemsToAnalyze = array_filter($itemsToAnalyze);


                                $associatedItems = findAssociation($tidsets, $itemsToAnalyze);

                                $supportA = calculateSupport($tidsets, $itemsToAnalyze, $lengthData, $i);

                                $i++;



                                if ($associatedItems[0] != null) {

                                    $supportAB = calculateSupportAB($tidsets, $itemsToAnalyze, array_slice($associatedItems, 0, 1,), $lengthData);
                                    $confidence = $supportAB / $supportA * 100;

                                    if ($supportA >= $minimsl_sup) {
                                        array_push($output, [
                                            // "output" => "Jika membeli " . implode(' dan ', $itemsToAnalyze) . " maka akan membeli " . implode(', ', $associatedItems),
                                            "output" => "Jika membeli " . implode(' dan ', $itemsToAnalyze) . " maka akan membeli " . $associatedItems[0],
                                            "supportA" => round($supportA, 2),
                                            "supportAB" => round($supportAB, 2),
                                            "confidence" => round($confidence, 2)
                                        ]);
                                    }
                                }
                            }


                            $uniqueData = array_reduce($output, function ($carry, $item) {
                                $output = $item['output'];

                                if (!isset($carry[$output])) {
                                    $carry[$output] = $item;
                                }

                                return $carry;
                            }, []);

                            $uniqueData = array_values($uniqueData);

                            foreach ($uniqueData as $o) {
                                echo "<tr>";
                                // echo "<td>Jika membeli " . implode(' dan ', $itemsToAnalyze) . " maka akan membeli " . implode(', ', $associatedItems) . "\n";
                                echo "<td>" . $o["output"] . "\n";
                                echo "<td>" . $o["supportA"] . "%</td>";
                                echo "<td>" . $o["supportAB"] . "%</td>";
                                echo "<td>" . $o["confidence"] . "%</td>";
                                echo "</tr>";
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