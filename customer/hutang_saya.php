<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$uid = $_SESSION['user_id'];
$hutang = mysqli_query($conn, "SELECT h.*, p.tanggal_pesan FROM hutang h JOIN pesanan p ON h.pesanan_id=p.id WHERE h.user_id=$uid AND h.status='belum_lunas'");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Hutang Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <h2>Hutang Saya</h2>
        <?php if (mysqli_num_rows($hutang) == 0): ?>
            <div class="alert alert-success">Tidak ada hutang.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tgl Pesan</th>
                        <th>Total Hutang</th>
                        <th>Sisa Hutang</th>
                        <th>Jatuh Tempo</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($hutang)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['tanggal_pesan'] ?></td>
                            <td>Rp <?= number_format($row['total_hutang'], 0, ',', '.') ?></td>
                            <td>Rp <?= number_format($row['sisa_hutang'], 0, ',', '.') ?></td>
                            <td><?= $row['jatuh_tempo'] ?></td>
                            <td><a href="bayar_hutang.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Bayar</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>