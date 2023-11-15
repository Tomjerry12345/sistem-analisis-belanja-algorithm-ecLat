<?php
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);

    try {
        // Ambil data admin berdasarkan username dari tabel
        $stmt = $conn->prepare("SELECT * FROM tbl_admin WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();
            
            // Bandingkan kata sandi yang dimasukkan dengan kata sandi yang disimpan dalam tabel
            if ($password === $data['password']) {
                // Kata sandi cocok, admin berhasil login
                // Lakukan tindakan yang sesuai, seperti mengarahkan ke halaman admin.
                header("Location: ../admin/dashboard.php");
                exit;
            } else {
                // Kata sandi tidak cocok
                // Tampilkan pesan kesalahan atau arahkan kembali ke halaman login
                header("Location: ../login.php?status=gagal1");
                exit;
            }
        } else {
            // Username tidak ditemukan dalam database
            // Tampilkan pesan kesalahan atau arahkan kembali ke halaman login
            header("Location: ../login.php?status=gagal2");
            exit;
        }
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
// Tutup koneksi database setelah selesai
$conn->close();
?>
