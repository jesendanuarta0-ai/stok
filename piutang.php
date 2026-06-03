<?php
require 'database.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['lunas_group']) && isset($_GET['tanggal'])) {
    $nama_toko = mysqli_real_escape_string($conn, $_GET['lunas_group']);
    $tanggal = mysqli_real_escape_string($conn, $_GET['tanggal']);

    mysqli_query($conn, "
        UPDATE kasir
        SET status_pembayaran='Lunas'
        WHERE nama_toko='$nama_toko'
        AND tanggal='$tanggal'
        AND status_pembayaran='Belum Lunas'
    ");

    echo "<script>
        alert('Transaksi berhasil diubah jadi Lunas');
        window.location='piutang.php';
    </script>";
    exit;
}

$data = mysqli_query($conn, "
SELECT
    k.nama_toko,
    k.tanggal,
    MIN(k.jatuh_tempo) AS jatuh_tempo,

    GROUP_CONCAT(
        CONCAT(b.nama_barang, ' x ', k.quantity)
        SEPARATOR '<br>'
    ) AS daftar_barang,

    SUM(k.total) AS total_piutang

FROM kasir k

LEFT JOIN barang b
ON k.id_barang = b.id_barang

WHERE k.status_pembayaran='Belum Lunas'

GROUP BY k.nama_toko, k.tanggal

ORDER BY k.tanggal ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Piutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">← Dashboard</a>

    <h2 class="mb-4">Daftar Piutang Per Tanggal</h2>

    <table class="table table-bordered table-striped">
        <tr>
            <th>No</th>
            <th>Nama Toko</th>
            <th>Tanggal Beli</th>
            <th>Jatuh Tempo</th>
            <th>Barang Dibeli</th>
            <th>Total Piutang</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;
        $grand_total = 0;
        $hari_ini = date('Y-m-d');

        while ($row = mysqli_fetch_assoc($data)) {
            $grand_total += $row['total_piutang'];
            $warna = ($row['jatuh_tempo'] < $hari_ini) ? "table-danger" : "";
        ?>

        <tr class="<?= $warna ?>">
            <td><?= $no++; ?></td>
            <td><?= htmlspecialchars($row['nama_toko']); ?></td>
            <td><?= $row['tanggal']; ?></td>
            <td><?= $row['jatuh_tempo']; ?></td>
            <td><?= $row['daftar_barang']; ?></td>
            <td>Rp<?= number_format($row['total_piutang'], 0, ',', '.'); ?></td>
            <td>
                <span class="badge bg-warning text-dark">Belum Lunas</span>
            </td>
            <td>
                <a href="piutang.php?lunas_group=<?= urlencode($row['nama_toko']); ?>&tanggal=<?= $row['tanggal']; ?>"
                   onclick="return confirm('Yakin semua transaksi toko ini pada tanggal tersebut sudah lunas?')"
                   class="btn btn-success btn-sm">
                    Jadikan Lunas
                </a>
            </td>
        </tr>

        <?php } ?>

        <tr>
            <th colspan="5">Total Semua Piutang</th>
            <th colspan="3">Rp<?= number_format($grand_total, 0, ',', '.'); ?></th>
        </tr>
    </table>

</div>

</body>
</html>