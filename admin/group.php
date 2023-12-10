<!DOCTYPE html>
<?php
$menu3 = "active";
include "../config/koneksi.php";
?>

<html>

<head>
    <title>Frequent Item Sets Group</title>
    <?php include "head.php"; ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content pb-3">
        <div class="container pt-3">
            <h3>Frequent Item Sets Group</h3>
            Item & TID List
            <hr>

            <div class="mt-5">
                <div class="table-responsive">
                    <table id="frequentSetsTable" class="table table-bordered">
                        <thead class="thead-dark">
                            <tr>
                                <th>Item</th>
                                <th>TID List</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
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
                                        if ($key !== 'Transaksi_Id' && $key !== 'No' && $key !== 'tanggal' && $value !== null && $value != "") {
                                            if (!isset($tidsets[$value])) {
                                                $tidsets[$value] = [];
                                            }
                                            $tidsets[$value][] = $tid;
                                        }

                                        // logO("value", $value);
                                    }
                                }

                                return $tidsets;
                            }

                            $data = $conn->query("SELECT * FROM tbl_transaksi");

                            $dataCalculate = calculateTIDsets($data);

                            // return;

                            // logO("sum", count($dataCalculate));
                            // logO("dataCalculate", $dataCalculate);


                            foreach ($dataCalculate as $key => $value) {
                                echo "<tr>";
                                echo "<td>" . $key . "</td>";
                                echo "<td>" . implode(', ', $value) . "</td>";
                                echo "</tr>";
                            }



                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php include "scripts.php"; ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#frequentSetsTable, #frequentSetsDataTable').DataTable();
            });
            $(document).ready(function() {
                $('#example').DataTable();
            });
        </script>
</body>

</html>