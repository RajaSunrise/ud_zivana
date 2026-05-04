<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$uid = $_SESSION['user_id'];
$pesanan = mysqli_query($conn, "SELECT * FROM pesanan WHERE user_id=$uid ORDER BY tanggal_pesan DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Pesanan Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <h3>Pesanan Saya</h3>
        <?php if (mysqli_num_rows($pesanan) == 0): ?>
            <div class="alert alert-info">Belum ada pesanan.</div>
            <?php else: while ($row = mysqli_fetch_assoc($pesanan)): ?>
                <div class="card mb-3 shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white d-flex justify-content-between">
                        <span><strong>Pesanan #<?= $row['id'] ?></strong> | <?= date('d M Y H:i', strtotime($row['tanggal_pesan'])) ?></span>
                        <span class="status-badge status-<?= $row['status_pesanan'] ?>"><?= ucfirst($row['status_pesanan']) ?></span>
                    </div>
                    <div class="card-body">
                        <div>Total: <strong>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></strong> | Metode: <?= ucfirst($row['metode_pembayaran']) ?></div>
                        <div>Status Pembayaran: <span class="status-badge status-<?= $row['status_pembayaran'] == 'lunas' ? 'selesai' : ($row['status_pembayaran'] == 'hutang' ? 'diproses' : 'pending') ?>"><?= ucfirst($row['status_pembayaran']) ?></span></div>
                        <?php if ($row['catatan']): ?><div class="mt-2"><small><strong>Catatan:</strong> <?= nl2br(htmlspecialchars($row['catatan'])) ?></small></div><?php endif; ?>
                        <?php if ($row['metode_pembayaran'] == 'transfer' && $row['status_pembayaran'] == 'pending'): ?>
                            <?php if (!$row['bukti_transfer']): ?>
                                <div class="mt-2"><a href="upload_bukti.php?id=<?= $row['id'] ?>" class="btn btn-primary btn-sm">Upload Bukti Transfer</a></div>
                            <?php else: ?>
                                <div class="mt-2 text-warning">Menunggu konfirmasi admin | <a href="../uploads/bukti/<?= $row['bukti_transfer'] ?>" target="_blank">Lihat Bukti</a></div>
                            <?php endif; ?>
                        <?php elseif ($row['metode_pembayaran'] == 'hutang' && $row['status_pembayaran'] == 'hutang'): ?>
                            <?php $hutang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM hutang WHERE pesanan_id={$row['id']}")); ?>
                            <div class="mt-2"><a href="bayar_hutang.php?id=<?= $hutang['id'] ?>" class="btn btn-warning btn-sm">Bayar Hutang</a></div>
                        <?php endif; ?>
                    </div>
                </div>
        <?php endwhile;
        endif; ?>
    </div>
</body>

</html>