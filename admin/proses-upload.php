<?php
error_reporting(0);
include "../config/koneksi.php";

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

            // Lokasi dan nama file CSV
            $csvFile = 'uploads/data.csv';

            $arr = array();

            // Baca file CSV
            $file = fopen($csvFile, 'r');
            if ($file) {
                // Lewati baris header
                fgetcsv($file);

                $result = [];

                while (($line = fgetcsv($file)) !== false) {
                    $dataSplit = explode(";", $line[0]);

                    $date = $dataSplit[0];
                    $barcode = $dataSplit[1];
                    $itemName = $dataSplit[2];

                    $index = array_search($date, array_column($result, 'tanggal'));

                    if ($index === false) {
                        $result[] = [
                            "No" => count($result) + 1,
                            "tanggal" => $date,
                            "Transaksi_Id" => "TID_" . (count($result) + 1),
                            // "Transaksi_Id" => $barcode,
                            "Item_1" => $itemName,
                        ];
                    } else {
                        $itemIndex = 1;
                        while (isset($result[$index]["Item_$itemIndex"])) {
                            $itemIndex++;
                        }

                        if ($itemIndex <= 16) {
                            $result[$index]["Item_$itemIndex"] = $itemName;
                        } else {
                            // If Item-2 already exists, create a new entry
                            $result[] = [
                                "No" => count($result) + 1,
                                "tanggal" => $date,
                                // "Transaksi_Id" => $barcode,
                                "Transaksi_Id" => "TID_" . (count($result) + 1),
                                "Item_1" => $itemName,
                            ];
                        }
                    }
                }

                foreach ($result as &$entry) {
                    for ($i = 1; $i <= 16; $i++) {
                        if (!isset($entry["Item_$i"])) {
                            $entry["Item_$i"] = "";
                        }
                    }
                }

                // echo json_encode($result, JSON_PRETTY_PRINT);

                $csvOutputFile = fopen($csvFile, 'w');
                $csvHeader = ['No.', 'Date', 'Transaksi_Id', 'Item_1', 'Item_2', 'Item_3', 'Item_4', 'Item_5', 'Item_6', 'Item_7', 'Item_8', 'Item_9', 'Item_10', 'Item_11', 'Item_12', 'Item_13', 'Item_14', 'Item_15', 'Item_16'];
                fputcsv($csvOutputFile, $csvHeader);

                foreach ($result as $row) {
                    $csvRow = [
                        $row['No'],
                        date('n/j/Y H:i', strtotime($row['tanggal'])),
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
                        $row['Item_16']
                    ];

                    fputcsv($csvOutputFile, $csvRow);
                }

                // Menutup file CSV
                fclose($csvOutputFile);

                echo 'CSV file has been created successfully.';

                foreach ($result as &$entry) {
                    $entry = array_map(function ($value) use ($conn) {
                        return "'" . mysqli_real_escape_string($conn, $value) . "'";
                    }, $entry);


                    $entry["tanggal"] = "STR_TO_DATE({$entry["tanggal"]}, '%Y-%m-%d %H:%i:%s')";

                    // echo json_encode($entry, JSON_PRETTY_PRINT);

                    $sql = "INSERT INTO tbl_transaksi (No, tanggal, Transaksi_Id, Item_1, Item_2, Item_3, Item_4, 
                            Item_5, Item_6, Item_7, Item_8, Item_9, Item_10, Item_11, Item_12, Item_13, Item_14, Item_15, Item_16) 
                            VALUES (" . implode(",", $entry) . ")";

                    if ($conn->query($sql) !== true) {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                }

                echo "succes";


                // while (($line = fgetcsv($file)) !== false) {
                //     // Mengganti tanggal dengan nilai yang sesuai dari $line
                //     $escapedValues = array_map(function ($value) use ($conn) {
                //         return "'" . mysqli_real_escape_string($conn, $value) . "'";
                //     }, $line);

                //     // Ganti indeks [1] dengan tanggal yang sesuai dari $line
                //     $escapedValues[1] = "STR_TO_DATE({$escapedValues[1]}, '%m/%d/%Y %H:%i')";
                //     $sql = "INSERT INTO tbl_transaksi (No, tanggal, Transaksi_Id, Item_1, Item_2, Item_3, Item_4, 
                //             Item_5, Item_6, Item_7, Item_8, Item_9, Item_10, Item_11, Item_12, Item_13, Item_14, Item_15, Item_16) 
                //             VALUES (" . implode(",", $escapedValues) . ")";

                //     if ($conn->query($sql) !== true) {
                //         //echo "Error: " . $sql . "<br>" . $conn->error;
                //     }
                // }


                fclose($file);
                header("Location: upload.php?status=berhasil1");
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
if (isset($_POST["upload-infaq"])) {
    $sql = "TRUNCATE TABLE tbl_infaq";
    $result = $conn->query($sql);

    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if ($fileType != "csv") {
        echo "Hanya file CSV yang diizinkan.";
    } else {
        // Pindahkan file CSV ke direktori yang ditentukan
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            // Nama file CSV yang telah diunggah
            $csvFile = $target_file;

            // Buka file CSV
            if (($handle = fopen($csvFile, "r")) !== FALSE) {
                // Variabel flag untuk memeriksa header
                $isHeader = true;

                // Baca baris demi baris dari file CSV
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Skip baris pertama (header)
                    if ($isHeader) {
                        $isHeader = false;
                        continue;
                    }

                    // Ambil data dari setiap kolom
                    $date = mysqli_real_escape_string($conn, $data[0]);
                    $barcode = mysqli_real_escape_string($conn, $data[1]);
                    $name = mysqli_real_escape_string($conn, $data[2]);
                    $count = intval($data[3]);
                    $price = floatval($data[4]);
                    $discount = floatval($data[5]);
                    $total = floatval($data[6]);
                    $buy_price = floatval($data[7]);
                    $margin = floatval($data[8]);

                    // Query SQL untuk memasukkan data ke dalam tabel Anda
                    $query = "INSERT INTO tbl_infaq (date, barcode, name, count, price, discount, total, buy_price, margin) 
                            VALUES ('$date', '$barcode', '$name', $count, $price, $discount, $total, $buy_price, $margin)";

                    // Jalankan query
                    if ($conn->query($query) === TRUE) {
                        //echo "Data berhasil disimpan.<br>";
                    } else {
                        echo "Error: " . $query . "<br>" . $conn->error;
                    }
                }
                header("Location: upload.php?status=berhasil2");
                exit;
            } else {
                echo "Gagal membuka file CSV.";
            }

            // Hapus file CSV yang telah diunggah
            unlink($csvFile);
        } else {
            echo "Terjadi kesalahan saat mengunggah file.";
        }
    }
}
