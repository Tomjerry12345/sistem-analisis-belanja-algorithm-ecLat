<?php
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status == "gagal1") {
        $pesan = "Login gagal, periksa password Anda.";
        $alert_type = "danger"; // Ini akan membuat alert merah
    } elseif ($status == "gagal2") {
        $pesan = "Login gagal, username tidak terdaftar.";
        $alert_type = "danger"; // Ini akan membuat alert merah
    } else {
        $pesan = ""; // Jika status selain "gagal1" atau "gagal2", pesan kosong
        $alert_type = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login Administrator</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body style=" background: #eaeaea; ">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <!-- Tampilkan alert jika pesan tidak kosong -->
                    <?php if (!empty($pesan)) { ?>
                    <div class="container mt-4">
                        <div class="alert alert-<?php echo $alert_type; ?>">
                            <?php echo $pesan; ?>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="card-header text-center" style="background:transparent">Halaman Login Administrator
                        <img src="assets/img/login.svg" style="max-width:300px;">
                    </div>
                    <div class="card-body">
                        <form action="config/cek.php" method="POST">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" name="username" id="username" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i id="password-toggle" class="fa fa-eye-slash"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            // Toggle password visibility
            $('#password-toggle').click(function () {
                var passwordField = $('#password');
                var passwordToggle = $('#password-toggle');

                if (passwordField.attr('type') === 'password') {
                    passwordField.attr('type', 'text');
                    passwordToggle.removeClass('fa-eye-slash');
                    passwordToggle.addClass('fa-eye');
                } else {
                    passwordField.attr('type', 'password');
                    passwordToggle.removeClass('fa-eye');
                    passwordToggle.addClass('fa-eye-slash');
                }
            });
        });
    </script>
</body>

</html>