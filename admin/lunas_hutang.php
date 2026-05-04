<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
include '../config/database.php';

if (isset($_GET['konfirmasi_bayar'])) {
    $id = $_GET['konfirmasi_bayar'];
    $bayar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pembayaran_hutang WHERE id=$id"));
    if ($bayar && $bayar['dikonfirmasi_admin'] == 0) {
        mysqli_query($conn, "UPDATE pembayaran_hutang SET dikonfirmasi_admin=1 WHERE id=$id");
        echo "<script>alert('Pembayaran hutang dikonfirmasi'); window.location='lunas_hutang.php';</script>";
    }
}
$pembayaran = mysqli_query($conn, "SELECT ph.*, h.user_id, u.nama FROM pembayaran_hutang ph JOIN hutang h ON ph.hutang_id=h.id JOIN users u ON h.user_id=u.id WHERE ph.dikonfirmasi_admin=0 ORDER BY ph.tanggal_bayar DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Konfirmasi Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_admin.php'; ?>
    <div class="container mt-4">
        <h2>Konfirmasi Pembayaran Hutang</h2>
        <?php if (mysqli_num_rows($pembayaran) == 0): ?>
            <div class="alert alert-info">Tidak ada pembayaran hutang menunggu konfirmasi.</div>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Jumlah Bayar</th>
                        <th>Bukti Transfer</th>
                        <th>Tgl Bayar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($pembayaran)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= $row['nama'] ?></td>
                            <td>Rp <?= number_format($row['jumlah_bayar'], 0, ',', '.') ?></td>
                            <td><a href="../uploads/bukti/<?= $row['bukti_transfer'] ?>" target="_blank">Lihat</a></td>
                            <td><?= $row['tanggal_bayar'] ?></td>
                            <td><a href="lunas_hutang.php?konfirmasi_bayar=<?= $row['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Konfirmasi?')">Konfirmasi</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>