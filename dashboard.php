<?php
session_start();
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>

<html>
<head>
    <title>Dashboard Stok Toko</title>

```
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body{
        background:#f4f6f9;
    }

    .card-menu{
        transition:0.3s;
        border:none;
        border-radius:15px;
    }

    .card-menu:hover{
        transform:translateY(-5px);
    }

    .icon{
        font-size:50px;
    }
</style>
```

</head>
<body>

<div class="container mt-5">

```
<div class="text-center mb-4">
    <h1>📦 Dashboard Stok Toko</h1>
    <p class="text-muted">Sistem Manajemen Persediaan Barang</p>
</div>

<div class="row g-4">

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">📋</div>
                <h5>Daftar Barang</h5>
                <a href="daftar_barang.php" class="btn btn-primary">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">📦</div>
                <h5>Data Barang</h5>
                <a href="barang.php" class="btn btn-primary">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">📥</div>
                <h5>Barang Masuk</h5>
                <a href="barang_masuk.php" class="btn btn-success">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">💰</div>
                <h5>Piutang</h5>
                <a href="piutang.php" class="btn btn-danger">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">💰</div>
                <h5>Update Harga</h5>
                <a href="update_harga.php" class="btn btn-warning">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">📊</div>
                <h5>Laporan</h5>
                <a href="laporan.php" class="btn btn-info">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">🛒</div>
                <h5>Kasir</h5>
                <a href="kasir.php" class="btn btn-secondary">Masuk</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card card-menu shadow">
            <div class="card-body text-center">
                <div class="icon">📈</div>
                <h5>Laba Rugi</h5>
                <a href="laba.php" class="btn btn-dark">Masuk</a>
            </div>
        </div>
    </div>

</div>

<div class="text-center mt-5">
    <a href="logout.php" class="btn btn-outline-danger btn-lg">
        Logout
    </a>
</div>
```

</div>

</body>
</html>
?>
