<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$uid = $_SESSION['user_id'];
$total_pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pesanan WHERE user_id=$uid"))['t'];
$total_hutang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(sisa_hutang) as t FROM hutang WHERE user_id=$uid AND status='belum_lunas'"))['t'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Customer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <div class="alert alert-primary bg-opacity-10 border-0 rounded-4">
            <h3>Selamat datang, <?= $_SESSION['nama'] ?>!</h3>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5>Total Pesanan</h5>
                        <h2><?= $total_pesanan ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-body">
                        <h5>Sisa Hutang</h5>
                        <h2>Rp <?= number_format($total_hutang, 0, ',', '.') ?></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>