<?php
require 'database.php';

if (isset($_POST['keluar'])) {
    $id_barang = $_POST['id_barang'];
    $tanggal = $_POST['tanggal'];
    $jumlah = $_POST['jumlah'];
    

    mysqli_query($conn, "INSERT INTO barang_keluar (id_barang,tanggal,jumlah)
    VALUES ('$id_barang','$tanggal','$jumlah')");

    // Kurangi stok otomatis
    mysqli_query($conn, "UPDATE barang SET stok = stok - $jumlah WHERE id_barang='$id_barang'");
}

$barang = mysqli_query($conn, "SELECT * FROM barang");
?>
<a href="dashboard.php"><button>Kembali ke Dashboard</button></a><br><br>
<form method="POST">
<form method="POST">
    Barang:
    <select name="id_barang">
        <?php while($b = mysqli_fetch_assoc($barang)) { ?>
            <option value="<?= $b['id_barang']; ?>"><?= $b['nama_barang']; ?></option>
        <?php } ?>
    </select><br>
    Tanggal: <input type="date" name="tanggal"><br>
    Jumlah: <input type="number" name="jumlah"><br>

    <button type="submit" name="keluar">Tambah Barang Keluar</button>
</form>