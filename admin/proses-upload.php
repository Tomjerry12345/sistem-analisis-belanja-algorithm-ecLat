<?php
error_reporting(0);
include "../config/koneksi.php";

function uploadInfaq($conn, $data)
{

    $date = mysqli_real_escape_string($conn, $data[0]);
    $barcode = mysqli_real_escape_string($conn, $data[1]);
    $name = mysqli_real_escape_string($conn, $data[2]);
    $count = intval($data[3]);
    $price = floatval($data[4]);
    $discount = floatval($data[5]);
    $total = floatval($data[6]);
    $buy_price = floatval($data[7]);
    $margin = floatval($data[8]);

    $keuntungan = 0;

    if (strcasecmp($name, "GULA 1 LITER") === 0) {
        $margin -= 100 * $count;
        $keuntungan =  100;
    }

    // Query SQL untuk memasukkan data ke dalam tabel Anda
    $query = "INSERT INTO tbl_infaq (date, barcode, name, count, price, discount, total, buy_price, margin, keuntungan) 
            VALUES ('$date', '$barcode', '$name', $count, $price, $discount, $total, $buy_price, $margin, $keuntungan)";

    if ($conn->query($query) === TRUE) {
        // echo "Data berhasil disimpan.<br>";
    } else {
        echo "Error: " . $query . "<br>" . $conn->error;
    }
}

if (isset($_POST["upload-analisa"])) {
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

            $sql = "TRUNCATE TABLE tbl_infaq";
            $result = $conn->query($sql);

            // Lokasi dan nama file CSV
            $csvFile = 'uploads/data.csv';

            $arr = array();

            // Baca file CSV
            $file = fopen($csvFile, 'r');
            if ($file) {
                // Lewati baris header
                fgetcsv($file);

                while (($dataSplit = fgetcsv($file, 0, ";")) !== false) {
                    $date = $dataSplit[0];
                    $barcode = $dataSplit[1];
                    $itemName = $dataSplit[2];
                    $count = $dataSplit[3];

                    $data_transaksi[$date][] = $itemName;

                    uploadInfaq($conn, $dataSplit);
                }

                $tidCounter = 1;

                $result = array_map(function ($date, $items) use (&$tidCounter) {
                    $no =  $tidCounter;
                    $tid = 'TID_' . $tidCounter;
                    $tidCounter++;

                    return [
                        'No' => $no,
                        'tanggal' => $date,
                        'Transaksi_Id' => $tid,
                        'name' => $items
                    ];
                }, array_keys($data_transaksi), $data_transaksi);

                $barang_df = array_map(function ($item) {
                    return array_slice(array_pad($item['name'], 53, ''), 0, 53);
                }, $result);


                foreach ($result as $key => &$item) {
                    $item = array_merge($item, array_combine(
                        array_map(function ($i) {
                            return 'Item_' . $i;
                        }, range(1, 53)),
                        $barang_df[$key]
                    ));
                    unset($item['name']);
                }

                $csvOutputFile = fopen($csvFile, 'w');
                $csvHeader = [
                    'No.',
                    'Date',
                    'Transaksi_Id',
                    'Item_1',
                    'Item_2',
                    'Item_3',
                    'Item_4',
                    'Item_5',
                    'Item_6',
                    'Item_7',
                    'Item_8',
                    'Item_9',
                    'Item_10',
                    'Item_11',
                    'Item_12',
                    'Item_13',
                    'Item_14',
                    'Item_15',
                    'Item_16',
                    'Item_17',
                    'Item_18',
                    'Item_19',
                    'Item_20',
                    'Item_21',
                    'Item_22',
                    'Item_23',
                    'Item_24',
                    'Item_25',
                    'Item_26',
                    'Item_27',
                    'Item_28',
                    'Item_29',
                    'Item_30',
                    'Item_31',
                    'Item_32',
                    'Item_33',
                    'Item_34',
                    'Item_35',
                    'Item_36',
                    'Item_37',
                    'Item_38',
                    'Item_39',
                    'Item_40',
                    'Item_41',
                    'Item_42',
                    'Item_43',
                    'Item_44',
                    'Item_45',
                    'Item_46',
                    'Item_47',
                    'Item_48',
                    'Item_49',
                    'Item_50',
                    'Item_51',
                    'Item_52',
                    'Item_53'
                ];
                fputcsv($csvOutputFile, $csvHeader);

                foreach ($result as $row) {
                    $csvRow = [
                        $row['No'],
                        date('Y-m-d H:i:s', strtotime($row['tanggal'])),
                        $row['Transaksi_Id'],
                        $row['Item_1'],
                        $row['Item_2'],
                        $row['Item_3'],
                        $row['Item_4'],
                        $row['Item_5'],
                        $row['Item_6'],
                        $row['Item_7'],
                        $row['Item_8'],
                        $row['Item_9'],
                        $row['Item_10'],
                        $row['Item_11'],
                        $row['Item_12'],
                        $row['Item_13'],
                        $row['Item_14'],
                        $row['Item_15'],
                        $row['Item_16'],
                        $row['Item_17'],
                        $row['Item_18'],
                        $row['Item_19'],
                        $row['Item_20'],
                        $row['Item_21'],
                        $row['Item_22'],
                        $row['Item_23'],
                        $row['Item_24'],
                        $row['Item_25'],
                        $row['Item_26'],
                        $row['Item_27'],
                        $row['Item_28'],
                        $row['Item_29'],
                        $row['Item_30'],
                        $row['Item_31'],
                        $row['Item_32'],
                        $row['Item_33'],
                        $row['Item_34'],
                        $row['Item_35'],
                        $row['Item_36'],
                        $row['Item_37'],
                        $row['Item_38'],
                        $row['Item_39'],
                        $row['Item_40'],
                        $row['Item_41'],
                        $row['Item_42'],
                        $row['Item_43'],
                        $row['Item_44'],
                        $row['Item_45'],
                        $row['Item_46'],
                        $row['Item_47'],
                        $row['Item_48'],
                        $row['Item_49'],
                        $row['Item_50'],
                        $row['Item_51'],
                        $row['Item_52'],
                        $row['Item_53'],
                    ];

                    fputcsv($csvOutputFile, $csvRow);
                }

                // Menutup file CSV
                fclose($csvOutputFile);

                foreach ($result as &$entry) {
                    $entry = array_map(function ($value) use ($conn) {
                        return "'" . mysqli_real_escape_string($conn, $value) . "'";
                    }, $entry);


                    $entry["tanggal"] = "STR_TO_DATE({$entry["tanggal"]}, '%Y-%m-%d %H:%i:%s')";

                    // echo json_encode($entry, JSON_PRETTY_PRINT);

                    $sql = "INSERT INTO tbl_transaksi (No, tanggal, Transaksi_Id, Item_1, Item_2, Item_3, Item_4, Item_5, Item_6, Item_7, Item_8, Item_9, Item_10, 
                            Item_11, Item_12, Item_13, Item_14, Item_15, Item_16, Item_17, Item_18, Item_19, Item_20,
                            Item_21, Item_22, Item_23, Item_24, Item_25, Item_26, Item_27, Item_28, Item_29, Item_30,
                            Item_31, Item_32, Item_33, Item_34, Item_35, Item_36, Item_37, Item_38, Item_39, Item_40,
                            Item_41, Item_42, Item_43, Item_44, Item_45, Item_46, Item_47, Item_48, Item_49, Item_50,
                            Item_51, Item_52, Item_53) 
                            VALUES (" . implode(",", $entry) . ")";

                    if ($conn->query($sql) !== true) {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }

                fclose($file);

                header("Location: upload.php?status=1");
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

// if (isset($_POST["upload-infaq"])) {
//     $sql = "TRUNCATE TABLE tbl_infaq";
//     $result = $conn->query($sql);

//     $target_dir = "uploads/";
//     $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
//     $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

//     if ($fileType != "csv") {
//         echo "Hanya file CSV yang diizinkan.";
//     } else {
//         // Pindahkan file CSV ke direktori yang ditentukan
//         if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//             // Nama file CSV yang telah diunggah
//             $csvFile = $target_file;

//             // Buka file CSV
//             if (($handle = fopen($csvFile, "r")) !== FALSE) {
//                 // Variabel flag untuk memeriksa header
//                 $isHeader = true;

//                 // Baca baris demi baris dari file CSV
//                 while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//                     // Skip baris pertama (header)
//                     if ($isHeader) {
//                         $isHeader = false;
//                         continue;
//                     }

//                     // Ambil data dari setiap kolom
//                     $date = mysqli_real_escape_string($conn, $data[0]);
//                     $barcode = mysqli_real_escape_string($conn, $data[1]);
//                     $name = mysqli_real_escape_string($conn, $data[2]);
//                     $count = intval($data[3]);
//                     $price = floatval($data[4]);
//                     $discount = floatval($data[5]);
//                     $total = floatval($data[6]);
//                     $buy_price = floatval($data[7]);
//                     $margin = floatval($data[8]);

//                     // Query SQL untuk memasukkan data ke dalam tabel Anda
//                     $query = "INSERT INTO tbl_infaq (date, barcode, name, count, price, discount, total, buy_price, margin) 
//                             VALUES ('$date', '$barcode', '$name', $count, $price, $discount, $total, $buy_price, $margin)";

//                     // Jalankan query
//                     if ($conn->query($query) === TRUE) {
//                         //echo "Data berhasil disimpan.<br>";
//                     } else {
//                         echo "Error: " . $query . "<br>" . $conn->error;
//                     }
//                 }
//                 header("Location: upload.php?status=berhasil2");
//                 exit;
//             } else {
//                 echo "Gagal membuka file CSV.";
//             }

//             // Hapus file CSV yang telah diunggah
//             unlink($csvFile);
//         } else {
//             echo "Terjadi kesalahan saat mengunggah file.";
//         }
//     }
// }
