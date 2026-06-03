<?php
require 'database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}

// UBAH STATUS JADI LUNAS
if (isset($_GET['lunas'])) {
    $id = $_GET['lunas'];

    mysqli_query($conn, "
        UPDATE kasir
        SET status_pembayaran = 'Lunas'
        WHERE id_kasir = '$id'
    ");

    echo "<script>
            alert('Pembayaran berhasil dilunasi!');
            window.location='kasir.php';
          </script>";
    exit();
}

// SIMPAN TRANSAKSI PENJUALAN
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang = $_POST['id_barang'];
    $nama_toko = $_POST['nama_toko'];
    $tanggal = $_POST['tanggal'];
    $quantity = $_POST['quantity'];
    $metode_pembayaran = $_POST['metode_pembayaran'];

    if ($metode_pembayaran == 'Cash') {
        $status_pembayaran = 'Lunas';
        $jatuh_tempo_sql = "NULL";
    } else {
        $status_pembayaran = 'Belum Lunas';
        $jatuh_tempo = date('Y-m-d', strtotime($tanggal . ' +1 month'));
        $jatuh_tempo_sql = "'$jatuh_tempo'";
    }

    $barang = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM barang WHERE id_barang='$id_barang'")
    );

    $harga_jual = $barang['harga_jual'];
    $stok = $barang['stok'];

    if ($quantity > $stok) {
        echo "<script>alert('Stok tidak mencukupi!');</script>";
    } else {
        $total = $harga_jual * $quantity;

        mysqli_query($conn, "INSERT INTO kasir
            (
                id_barang,
                nama_toko,
                tanggal,
                jatuh_tempo,
                metode_pembayaran,
                quantity,
                harga_jual,
                total,
                status_pembayaran
            )
            VALUES
            (
                '$id_barang',
                '$nama_toko',
                '$tanggal',
                $jatuh_tempo_sql,
                '$metode_pembayaran',
                '$quantity',
                '$harga_jual',
                '$total',
                '$status_pembayaran'
            )
        ");

        mysqli_query($conn, "
            UPDATE barang
            SET stok = stok - $quantity
            WHERE id_barang='$id_barang'
        ");

        echo "<script>
                alert('Transaksi berhasil disimpan!');
                window.location='kasir.php';
              </script>";
        exit();
    }
}

$data_barang = mysqli_query($conn, "SELECT * FROM barang ORDER BY nama_barang ASC");

$data_kasir = mysqli_query($conn, "
    SELECT k.*, b.nama_barang
    FROM kasir k
    JOIN barang b ON k.id_barang = b.id_barang
    ORDER BY k.tanggal DESC
");

$total_semua = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total) AS grand_total FROM kasir")
);

$total_bulanan = mysqli_query($conn, "
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
           SUM(total) AS total_bulan
    FROM kasir
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kasir Penjualan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <h1 class="text-center mb-4">Kasir Penjualan</h1>

    <div class="card shadow p-4 mb-5">
        <h3 class="mb-3">Input Penjualan</h3>

        <form method="POST">

            <div class="mb-3">
                <label>Nama Toko Pembeli</label>
                <input type="text" name="nama_toko" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Tanggal Transaksi</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-control" required>
                    <option value="Cash">Cash - Langsung Lunas</option>
                    <option value="Bon">Bon - Tempo 1 Bulan</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Nama Barang</label>
                <select name="id_barang" class="form-control" required>
                    <option value="">-- Pilih Barang --</option>

                    <?php while($barang = mysqli_fetch_assoc($data_barang)) { ?>
                        <option value="<?= $barang['id_barang']; ?>">
                            <?= $barang['nama_barang']; ?> |
                            Stok: <?= $barang['stok']; ?> |
                            Harga: Rp<?= number_format($barang['harga_jual']); ?>
                        </option>
                    <?php } ?>

                </select>
            </div>

            <div class="mb-3">
                <label>Quantity</label>
                <input type="number" name="quantity" class="form-control" min="1" required>
            </div>

            <button type="submit" class="btn btn-primary">
                Simpan Penjualan
            </button>

        </form>
    </div>

    <div class="card shadow p-4 mb-5">
        <h3 class="mb-3">Laporan Kasir</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Tanggal</th>
                    <th>Jatuh Tempo</th>
                    <th>Nama Toko</th>
                    <th>Nama Barang</th>
                    <th>Metode</th>
                    <th>Harga Jual</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($data_kasir)) { ?>
                    <tr>
                        <td><?= $row['tanggal']; ?></td>
                        <td>
                            <?= $row['jatuh_tempo'] ? $row['jatuh_tempo'] : '-'; ?>
                        </td>
                        <td><?= $row['nama_toko']; ?></td>
                        <td><?= $row['nama_barang']; ?></td>
                        <td><?= $row['metode_pembayaran']; ?></td>
                        <td>Rp<?= number_format($row['harga_jual']); ?></td>
                        <td><?= $row['quantity']; ?></td>
                        <td>Rp<?= number_format($row['total']); ?></td>

                        <td>
                            <?php if ($row['status_pembayaran'] == 'Lunas') { ?>
                                <span class="badge bg-success">Lunas</span>
                            <?php } else { ?>
                                <span class="badge bg-warning text-dark">Belum Lunas</span>
                            <?php } ?>
                        </td>

                        <td>
                            <?php if ($row['status_pembayaran'] == 'Belum Lunas') { ?>
                                <a href="kasir.php?lunas=<?= $row['id_kasir']; ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Yakin pembayaran sudah lunas?')">
                                    Lunas
                                </a>
                            <?php } else { ?>
                                -
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>

                <tr>
                    <td colspan="7"><strong>Total Semua Penjualan</strong></td>
                    <td colspan="3">
                        <strong>
                            Rp<?= number_format($total_semua['grand_total'] ?? 0); ?>
                        </strong>
                    </td>
                </tr>

            </table>
        </div>
    </div>

    <div class="card shadow p-4">
        <h3 class="mb-3">Penghasilan Per Bulan</h3>

        <table class="table table-bordered table-striped">
            <tr>
                <th>Bulan</th>
                <th>Total Penghasilan</th>
            </tr>

            <?php while($bulan = mysqli_fetch_assoc($total_bulanan)) { ?>
                <tr>
                    <td><?= $bulan['bulan']; ?></td>
                    <td>Rp<?= number_format($bulan['total_bulan']); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>

</body>
</html>