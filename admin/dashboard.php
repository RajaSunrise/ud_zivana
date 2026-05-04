<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
include '../config/database.php';

$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesanan"))['t'];
$total_pendapatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as t FROM pesanan WHERE status_pembayaran='lunas'"))['t'];
$total_hutang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(sisa_hutang) as t FROM hutang WHERE status='belum_lunas'"))['t'];
$total_pelanggan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users WHERE role='customer'"))['t'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Admin UD.ZIVANA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_admin.php'; ?>
    <div class="container mt-4">
        <h2>Dashboard Admin</h2>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5>Total Pesanan</h5>
                        <h3><?= $total_pesanan ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5>Pendapatan Lunas</h5>
                        <h3>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5>Total Piutang</h5>
                        <h3>Rp <?= number_format($total_hutang, 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5>Pelanggan</h5>
                        <h3><?= $total_pelanggan ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>