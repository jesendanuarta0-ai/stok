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

    $nama_barang       = $_POST['nama_barang'];
    $harga_beli        = $_POST['harga_beli'];
    $diskon_beli       = $_POST['diskon_beli'];
    $diskon_tambahan   = $_POST['diskon_tambahan'];
    $harga_jual        = $_POST['harga_jual'];
    $stok              = $_POST['stok'];
    $kategori          = $_POST['kategori'];

    // =============================
    // HITUNG DISKON BERTINGKAT
    // =============================
    $setelah_diskon_1 = $harga_beli - ($harga_beli * $diskon_beli / 100);
    $harga_beli_final = $setelah_diskon_1 - ($setelah_diskon_1 * $diskon_tambahan / 100);

    // =============================
    // SIMPAN KE DATABASE
    // =============================
    mysqli_query($conn, "
        INSERT INTO barang
        (
           
            nama_barang,
            harga_beli,
            diskon_beli,
            diskon_tambahan,
            harga_beli_final,
            harga_jual,
            stok,
            kategori
        )
        VALUES
        (
            
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

    echo "<script>
            alert('Barang berhasil ditambahkan!');
            window.location='daftar_barang.php';
          </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Barang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <a href="dashboard.php" class="btn btn-secondary mb-3">
        Kembali ke Dashboard
    </a>

    <h1 class="text-center mb-4">Tambah Barang</h1>

    <!-- =============================
         FORM TAMBAH BARANG
    ============================== -->
    <div class="card shadow p-4">

        <form method="POST">
                    

            <div class="mb-3">
                <label>Nama Barang</label>
                <input 
                    type="text" 
                    name="nama_barang" 
                    class="form-control" 
                    required
                >
            </div>

            <div class="mb-3">
                <label>Harga Beli Awal</label>
                <input 
                    type="number" 
                    name="harga_beli" 
                    class="form-control" 
                    required
                >
            </div>

           <div class="mb-3">
    <label>Diskon Supplier (%)</label>
    <input 
        type="number" 
        name="diskon_beli" 
        class="form-control" 
        value="0"
        step="0.01"
        required
    >
</div>

<div class="mb-3">
    <label>Diskon Tambahan (%)</label>
    <input 
        type="number" 
        name="diskon_tambahan" 
        class="form-control" 
        value="0"
        step="0.01"
        required
    >
</div>

            <div class="mb-3">
                <label>Harga Jual</label>
                <input 
                    type="number" 
                    name="harga_jual" 
                    class="form-control" 
                    required
                >
            </div>

            <div class="mb-3">
                <label>Stok Awal</label>
                <input 
                    type="number" 
                    name="stok" 
                    class="form-control" 
                    required
                >
            </div>

            <div class="mb-3">
                <label>Kategori</label>
                <input 
                    type="text" 
                    name="kategori" 
                    class="form-control"
                >
            </div>

            <button type="submit" class="btn btn-primary w-100">
                Simpan Barang
            </button>

        </form>

    </div>

</div>

</body>
</html>