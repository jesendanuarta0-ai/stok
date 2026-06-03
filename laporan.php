<?php
require 'database.php';

$mulai = $_GET['mulai'] ?? '';
$akhir = $_GET['akhir'] ?? '';

// =============================
// LAPORAN BARANG MASUK
// =============================
$query_masuk = "
    SELECT bm.*, b.nama_barang 
    FROM barang_masuk bm 
    JOIN barang b ON bm.id_barang = b.id_barang
";

if (!empty($mulai) && !empty($akhir)) {
    $query_masuk .= " WHERE bm.tanggal BETWEEN '$mulai' AND '$akhir'";
}

$data_masuk = mysqli_query($conn, $query_masuk);


// =============================
// LAPORAN BARANG KELUAR
// DIGABUNG DARI barang_keluar + kasir
// =============================
$query_keluar = "
    SELECT 
        bk.tanggal,
        b.nama_barang,
        bk.jumlah,
        bk.keterangan,
        'Barang Keluar Manual' AS sumber
    FROM barang_keluar bk
    JOIN barang b ON bk.id_barang = b.id_barang
";

if (!empty($mulai) && !empty($akhir)) {
    $query_keluar .= " WHERE bk.tanggal BETWEEN '$mulai' AND '$akhir'";
}

$query_keluar .= "

    UNION ALL

    SELECT 
        k.tanggal,
        b.nama_barang,
        k.quantity AS jumlah,
        CONCAT('Penjualan Kasir - ', k.nama_toko, ' - ', k.metode_pembayaran) AS keterangan,
        'Kasir' AS sumber
    FROM kasir k
    JOIN barang b ON k.id_barang = b.id_barang
";

if (!empty($mulai) && !empty($akhir)) {
    $query_keluar .= " WHERE k.tanggal BETWEEN '$mulai' AND '$akhir'";
}

$query_keluar .= " ORDER BY tanggal DESC";

$data_keluar = mysqli_query($conn, $query_keluar);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="mb-3">
    <a href="dashboard.php" class="btn btn-secondary">
        ← Kembali ke Dashboard
    </a>
</div>

    <h1 class="text-center mb-4">Laporan Stok Barang</h1>

    <div class="card shadow p-4 mb-4">
        <h3>Filter Laporan</h3>

        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label>Dari</label>
                <input type="date" name="mulai" value="<?= $mulai; ?>" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Sampai</label>
                <input type="date" name="akhir" value="<?= $akhir; ?>" class="form-control">
            </div>

            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    Tampilkan
                </button>
            </div>
        </form>
    </div>

    <div class="card shadow p-4 mb-4">
        <h3>Laporan Barang Masuk</h3>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Supplier</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($data_masuk)) { ?>
            <tr>
                <td><?= $row['tanggal']; ?></td>
                <td><?= $row['nama_barang']; ?></td>
                <td><?= $row['jumlah']; ?></td>
                <td><?= $row['supplier']; ?></td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <div class="card shadow p-4">
        <h3>Laporan Barang Keluar</h3>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Keterangan</th>
                <th>Sumber</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($data_keluar)) { ?>
            <tr>
                <td><?= $row['tanggal']; ?></td>
                <td><?= $row['nama_barang']; ?></td>
                <td><?= $row['jumlah']; ?></td>
                <td><?= $row['keterangan']; ?></td>
                <td>
                    <?php if ($row['sumber'] == 'Kasir') { ?>
                        <span class="badge bg-success">Kasir</span>
                    <?php } else { ?>
                        <span class="badge bg-warning text-dark">Manual</span>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>