<?php
/**
 * Proses penyimpanan data mahasiswa dari form_input.php ke database
 */

include 'database.php';

// pastikan request berasal dari form (method POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim    = isset($_POST['txtnim']) ? $_POST['txtnim'] : '';
    $nama   = isset($_POST['txtnama']) ? $_POST['txtnama'] : '';
    $alamat = isset($_POST['txtalamat']) ? $_POST['txtalamat'] : '';

    // validasi sederhana
    if ($nim === '' || $nama === '' || $alamat === '') {
        echo 'Data belum lengkap!';
        exit;
    }

    // simpan ke database menggunakan class Database
    $db = new Database();

    $data = [
        'nim'    => $nim,
        'nama'   => $nama,
        'alamat' => $alamat
    ];

    $simpan = $db->insert('mahasiswa', $data);

    if ($simpan) {
        echo 'Data berhasil disimpan.';
    } else {
        echo 'Gagal menyimpan data.';
    }
} else {
    echo 'Akses tidak valid.';
}
?>
