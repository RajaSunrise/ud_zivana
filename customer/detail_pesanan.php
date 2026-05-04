<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$id = $_GET['id'];
$uid = $_SESSION['user_id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$id AND user_id=$uid"));
if (!$pesanan) die("Pesanan tidak ditemukan.");

$detail = mysqli_query($conn, "SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.produk_id=p.id WHERE d.pesanan_id=$id");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Detail Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <h2>Detail Pesanan #<?= $id ?></h2>
        <p>Total: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?> | Metode: <?= $pesanan['metode_pembayaran'] ?> | Status: <?= $pesanan['status_pesanan'] ?></p>
        <table class="table">
            <tr>
                <th>Produk</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
            <?php while ($d = mysqli_fetch_assoc($detail)): $sub = $d['jumlah'] * $d['harga_satuan']; ?>
                <tr>
                    <td><?= $d['nama_produk'] ?></td>
                    <td><?= $d['jumlah'] ?> kg</td>
                    <td>Rp <?= number_format($d['harga_satuan'], 0, ',', '.') ?></td>
                    <td>Rp <?= number_format($sub, 0, ',', '.') ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <a href="pesanan_saya.php" class="btn btn-secondary">Kembali</a>
    </div>
</body>

</html>