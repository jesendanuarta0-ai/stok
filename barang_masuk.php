<?php
require 'database.php';

if (isset($_POST['masuk'])) {
    $id_barang = $_POST['id_barang'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    

    // Simpan transaksi barang masuk
    mysqli_query($conn, "INSERT INTO barang_masuk (id_barang,tanggal,jumlah)
    VALUES ('$id_barang','$tanggal','$jumlah')");

    // Update stok barang
    mysqli_query($conn, "UPDATE barang SET stok = stok + $jumlah WHERE id_barang='$id_barang'");
}

$barang = mysqli_query($conn, "SELECT * FROM barang");
?>

<a href="dashboard.php"><button>Kembali ke Dashboard</button></a><br><br>
<form method="POST">
    Barang:
    <select name="id_barang">
        <?php while($b = mysqli_fetch_assoc($barang)) { ?>
            <option value="<?= $b['id_barang']; ?>"><?= $b['nama_barang']; ?></option>
        <?php } ?>
    </select><br>

    Tanggal: <input type="date" name="tanggal"><br>
    Jumlah: <input type="number" name="jumlah"><br>

    <button type="submit" name="masuk">Tambah Barang Masuk</button>
</form>