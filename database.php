<?php

$host = "sql311.infinityfree.com";
$user = "if0_41943124";
$pass = "Jesen060507";
$db   = "if0_41943124_stok_toko";

$conn = mysqli_connect($host, $user, $pass, $db);

if (isset($_GET['lunas'])) {
    $id = $_GET['lunas'];

    mysqli_query($conn,"
        UPDATE kasir
        SET status_pembayaran='Lunas'
        WHERE id_kasir='$id'
    ");

    echo "<script>
            alert('Pembayaran berhasil dilunasi!');
            window.location='kasir.php';
          </script>";
    exit();
}
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>