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
// DATA LABA PER TRANSAKSI
// =============================
$data_laba = mysqli_query($conn, "
    SELECT 
        k.tanggal,
        b.nama_barang,
        b.harga_beli,
        b.diskon_beli,
        b.diskon_tambahan,
        b.harga_beli_final,
        k.harga_jual,
        k.quantity,
        ((k.harga_jual - b.harga_beli_final) * k.quantity) AS laba
    FROM kasir k
    JOIN barang b ON k.id_barang = b.id_barang
    ORDER BY k.tanggal DESC
");

// =============================
// TOTAL LABA KESELURUHAN
// =============================
$total_laba = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT 
            SUM((k.harga_jual - b.harga_beli_final) * k.quantity) AS grand_laba
        FROM kasir k
        JOIN barang b ON k.id_barang = b.id_barang
    ")
);

// =============================
// LABA PER BULAN
// =============================
$laba_bulanan = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(k.tanggal, '%Y-%m') AS bulan,
        SUM((k.harga_jual - b.harga_beli_final) * k.quantity) AS laba_bulan
    FROM kasir k
    JOIN barang b ON k.id_barang = b.id_barang
    GROUP BY DATE_FORMAT(k.tanggal, '%Y-%m')
    ORDER BY bulan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Laba</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">Kembali ke Dashboard</a>

    <h1 class="text-center mb-4">Laporan Laba</h1>

    <!-- DETAIL LABA -->
    <div class="card shadow p-4 mb-5">

        <h3 class="mb-3">Detail Laba Penjualan</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <tr>
                    <th>Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Diskon 1</th>
                    <th>Diskon 2</th>
                    <th>Harga Modal Final</th>
                    <th>Harga Jual</th>
                    <th>Quantity</th>
                    <th>Laba</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($data_laba)) { ?>
                <tr>
                    <td><?= $row['tanggal']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>
                    <td>Rp<?= number_format($row['harga_beli']); ?></td>
                    <td><?= $row['diskon_beli']; ?>%</td>
                    <td><?= $row['diskon_tambahan']; ?>%</td>
                    <td>Rp<?= number_format($row['harga_beli_final']); ?></td>
                    <td>Rp<?= number_format($row['harga_jual']); ?></td>
                    <td><?= $row['quantity']; ?></td>
                    <td>Rp<?= number_format($row['laba']); ?></td>
                </tr>
                <?php } ?>

                <tr>
                    <td colspan="8"><strong>Total Laba Keseluruhan</strong></td>
                    <td>
                        <strong>
                            Rp<?= number_format($total_laba['grand_laba'] ?? 0); ?>
                        </strong>
                    </td>
                </tr>

            </table>
        </div>

    </div>

    <!-- LABA BULANAN -->
    <div class="card shadow p-4">

        <h3 class="mb-3">Laba Per Bulan</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">

                <tr>
                    <th>Bulan</th>
                    <th>Total Laba</th>
                </tr>

                <?php while($bulan = mysqli_fetch_assoc($laba_bulanan)) { ?>
                <tr>
                    <td><?= $bulan['bulan']; ?></td>
                    <td>Rp<?= number_format($bulan['laba_bulan']); ?></td>
                </tr>
                <?php } ?>

            </table>
        </div>

    </div>

</div>

</body>
</html>