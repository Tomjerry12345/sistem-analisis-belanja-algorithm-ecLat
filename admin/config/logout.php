<?php
    session_start(); // Memulai sesi

    // Menghapus semua data sesi
    session_unset();

    // Menghancurkan sesi
    session_destroy();

    // Mengarahkan pengguna ke halaman login
    header("Location: ../../login");
    exit;
?>
