<?php $menu7="active"; ?>
<?php include "proses-upload.php"; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Frequent Item Sets Total</title>
    <?php include "head.php"; ?>
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
</head>

<body>
    <?php include "sidebar.php"; ?>

    <div class="content pb-3">
    <div class="container mt-5">
        <h2 class="mb-4">Upload dan Simpan CSV</h2>

        <div class="row">
            <!-- Kartu pertama -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Form Upload Analisa EClaT</h3>
                        <form action="proses-upload.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="fileToUploadAnalisa">Pilih file CSV:</label>
                                <input type="file" class="form-control-file" name="csvFile" id="csvFile" required>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Upload" name="upload-analisa">
                        </form>
                    </div>
                </div>
            </div>

            <!-- Kartu kedua -->
            <div class="col-md-12 pt-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Form Upload Analisa Keuntungan & Infaq</h3>
                        <form action="proses-upload.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="fileToUpload">Pilih file CSV:</label>
                                <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload" required>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Upload" name="upload-infaq">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


        <?php include "scripts.php"; ?>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

</body>

</html>