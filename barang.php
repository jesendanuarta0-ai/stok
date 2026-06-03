<?php
require 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// =============================
// SIMPAN DATA BARANG
// =============================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $kode_barang = $_POST['kode_barang'];
    $nama_barang = $_POST['nama_barang'];
    $harga_beli = $_POST['harga_beli'];
    $diskon_beli = $_POST['diskon_beli'];
    $diskon_tambahan = $_POST['diskon_tambahan'];
    $harga_jual = $_POST['harga_jual'];
    $stok = $_POST['stok'];
    $kategori = $_POST['kategori'];

    // =============================
    // HITUNG DISKON BERTINGKAT
    // =============================

    // Diskon pertama
    $setelah_diskon_1 = $harga_beli - ($harga_beli * $diskon_beli / 100);

    // Diskon tambahan
    $harga_beli_final = $setelah_diskon_1 - ($setelah_diskon_1 * $diskon_tambahan / 100);

    // =============================
    // SIMPAN KE DATABASE
    // =============================
    mysqli_query($conn, "
        INSERT INTO barang
        (kode_barang, nama_barang, harga_beli, diskon_beli, diskon_tambahan, harga_beli_final, harga_jual, stok, kategori)
        VALUES
        (
            '$kode_barang',
            '$nama_barang',
            '$harga_beli',
            '$diskon_beli',
            '$diskon_tambahan',
            '$harga_beli_final',
            '$harga_jual',
            '$stok',
            '$kategori'
        )
    ");

    echo "<script>alert('Barang berhasil ditambahkan!'); window.location='barang.php';</script>";
}

// =============================
// FILTER KATEGORI
// =============================
$filter = "";

if (!empty($_GET['kategori'])) {
    $kategori = $_GET['kategori'];
    $filter = "WHERE kategori='$kategori'";
}

// =============================
// AMBIL DATA BARANG
// =============================
$data = mysqli_query($conn, "
    SELECT * FROM barang
    $filter
    ORDER BY nama_barang ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <h1 class="text-center mb-4">Data Barang</h1>

   
    <div class="card shadow p-4 mb-4">

        <form method="GET">

            <label>Filter Kategori:</label>

            <input type="text" name="kategori" class="form-control mb-2">

            <button type="submit" class="btn btn-success">
                Cari
            </button>

        </form>

    </div>

    <!-- =============================
         DAFTAR BARANG
    ============================== -->
    <div class="card shadow p-4">

        <h3 class="mb-3">Daftar Barang</h3>

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <tr>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Diskon 1</th>
                    <th>Diskon 2</th>
                    <th>Harga Modal Final</th>
                    <th>Harga Jual</th>
                    <th>Stok</th>
                    <th>Kategori</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td><?= $row['kode_barang']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>
                    <td>Rp<?= number_format($row['harga_beli']); ?></td>
                    <td><?= $row['diskon_beli']; ?>%</td>
                    <td><?= $row['diskon_tambahan']; ?>%</td>
                    <td>Rp<?= number_format($row['harga_beli_final']); ?></td>
                    <td>Rp<?= number_format($row['harga_jual']); ?></td>
                    <td><?= $row['stok']; ?></td>
                    <td><?= $row['kategori']; ?></td>
                </tr>
                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>