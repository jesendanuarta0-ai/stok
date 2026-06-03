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
// UPDATE DATA BARANG
// =============================
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $id_barang         = $_POST['id_barang'];
    $nama_barang       = $_POST['nama_barang'];

    $harga_beli        = $_POST['harga_beli'];
    $diskon_beli       = $_POST['diskon_beli'];
    $diskon_tambahan   = $_POST['diskon_tambahan'];

    $harga_jual        = $_POST['harga_jual'];

    // =============================
    // HITUNG DISKON BERTINGKAT
    // =============================
    $setelah_diskon_1 = $harga_beli - ($harga_beli * $diskon_beli / 100);

    $harga_beli_final = $setelah_diskon_1 - 
                        ($setelah_diskon_1 * $diskon_tambahan / 100);

    // =============================
    // UPDATE DATABASE
    // =============================
    mysqli_query($conn, "
        UPDATE barang
        SET
            nama_barang = '$nama_barang',
            harga_beli = '$harga_beli',
            diskon_beli = '$diskon_beli',
            diskon_tambahan = '$diskon_tambahan',
            harga_beli_final = '$harga_beli_final',
            harga_jual = '$harga_jual'
        WHERE id_barang = '$id_barang'
    ");

    echo "
    <script>
        alert('Harga barang berhasil diupdate!');
        window.location='update_harga.php';
    </script>
    ";
}

// =============================
// AMBIL DATA BARANG
// =============================
$data = mysqli_query($conn, "
    SELECT * FROM barang
    ORDER BY nama_barang ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Harga Barang</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <h1 class="text-center mb-4">
        Update Harga Barang
    </h1>

    <div class="card shadow p-4">

        <div class="table-responsive">

            <table class="table table-bordered table-striped">

                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Beli</th>
                    <th>Diskon 1</th>
                    <th>Diskon 2</th>
                    <th>Harga Modal Final</th>
                    <th>Harga Jual</th>
                    <th>Update</th>
                </tr>

                <?php while($row = mysqli_fetch_assoc($data)) { ?>

                <tr>

                    <form method="POST">

                        <input 
                            type="hidden"
                            name="id_barang"
                            value="<?= $row['id_barang']; ?>"
                        >

                        <td>
                            <input 
                                type="text"
                                name="nama_barang"
                                class="form-control"
                                value="<?= $row['nama_barang']; ?>"
                            >
                        </td>

                        <td>
                            <input 
                                type="number"
                                step="0.01"
                                name="harga_beli"
                                class="form-control"
                                value="<?= $row['harga_beli']; ?>"
                            >
                        </td>

                        <td>
                            <input 
                                type="number"
                                step="0.01"
                                name="diskon_beli"
                                class="form-control"
                                value="<?= $row['diskon_beli']; ?>"
                            >
                        </td>

                        <td>
                            <input 
                                type="number"
                                step="0.01"
                                name="diskon_tambahan"
                                class="form-control"
                                value="<?= $row['diskon_tambahan']; ?>"
                            >
                        </td>

                        <td>
                            Rp<?= number_format($row['harga_beli_final']); ?>
                        </td>

                        <td>
                            <input 
                                type="number"
                                step="0.01"
                                name="harga_jual"
                                class="form-control"
                                value="<?= $row['harga_jual']; ?>"
                            >
                        </td>

                        <td>
                            <button 
                                type="submit"
                                class="btn btn-warning"
                            >
                                Update
                            </button>
                        </td>

                    </form>

                </tr>

                <?php } ?>

            </table>

        </div>

    </div>

</div>

</body>
</html>