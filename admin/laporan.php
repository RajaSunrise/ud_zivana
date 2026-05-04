<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
include '../config/database.php';

$laporan = mysqli_query($conn, "SELECT p.*, u.nama FROM pesanan p JOIN users u ON p.user_id=u.id ORDER BY p.tanggal_pesan DESC");
$piutang = mysqli_query($conn, "SELECT h.*, u.nama, p.tanggal_pesan FROM hutang h JOIN users u ON h.user_id=u.id JOIN pesanan p ON h.pesanan_id=p.id WHERE h.status='belum_lunas'");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_admin.php'; ?>
    <div class="container mt-4">
        <h3>Laporan Penjualan</h3>
        <div class="card mb-4">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tgl</th>
                        </tr>
                    </thead>
                    <tbody><?php while ($row = mysqli_fetch_assoc($laporan)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td><?= $row['nama'] ?></td>
                                <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                                <td><?= $row['metode_pembayaran'] ?></td>
                                <td><?= $row['status_pembayaran'] ?></td>
                                <td><?= $row['tanggal_pesan'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <h3>Piutang Belum Lunas</h3>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Tgl Pesan</th>
                            <th>Total Hutang</th>
                            <th>Sisa</th>
                            <th>Jatuh Tempo</th>
                        </tr>
                    </thead>
                    <tbody><?php while ($row = mysqli_fetch_assoc($piutang)): ?>
                            <tr>
                                <td><?= $row['nama'] ?></td>
                                <td><?= $row['tanggal_pesan'] ?></td>
                                <td>Rp <?= number_format($row['total_hutang'], 0, ',', '.') ?></td>
                                <td>Rp <?= number_format($row['sisa_hutang'], 0, ',', '.') ?></td>
                                <td><?= $row['jatuh_tempo'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>