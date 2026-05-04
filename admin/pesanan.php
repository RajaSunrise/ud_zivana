<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
include '../config/database.php';

if (isset($_POST['update_status'])) {
    $id = $_POST['id_pesanan'];
    $status = $_POST['status'];
    mysqli_query($conn, "UPDATE pesanan SET status_pesanan='$status' WHERE id=$id");
    echo "<script>alert('Status pesanan diperbarui'); window.location='pesanan.php';</script>";
}
if (isset($_GET['konfirmasi_transfer'])) {
    $id = $_GET['konfirmasi_transfer'];
    mysqli_query($conn, "UPDATE pesanan SET status_pembayaran='lunas' WHERE id=$id");
    echo "<script>alert('Pembayaran dikonfirmasi'); window.location='pesanan.php';</script>";
}
if (isset($_GET['tolak_bukti'])) {
    $id = $_GET['tolak_bukti'];
    mysqli_query($conn, "UPDATE pesanan SET bukti_transfer=NULL WHERE id=$id");
    echo "<script>alert('Bukti ditolak'); window.location='pesanan.php';</script>";
}
$pesanan = mysqli_query($conn, "SELECT p.*, u.nama, u.no_hp, u.alamat FROM pesanan p JOIN users u ON p.user_id=u.id ORDER BY p.tanggal_pesan DESC");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Pesanan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_admin.php'; ?>
    <div class="container mt-4">
        <h3>Daftar Pesanan</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-custom">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Pelanggan</th>
                        <th>Total</th>
                        <th>Metode</th>
                        <th>Status Bayar</th>
                        <th>Status Pesanan</th>
                        <th>Catatan</th>
                        <th>Bukti</th>
                        <th>Tgl</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($pesanan)): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td>
                                <strong><?= $row['nama'] ?></strong><br>
                                <small><?= $row['no_hp'] ?></small><br>
                                <small class="text-muted"><?= nl2br(htmlspecialchars($row['alamat'])) ?></small>
                            </td>
                            <td>Rp <?= number_format($row['total_harga'], 0, ',', '.') ?></td>
                            <td><?= $row['metode_pembayaran'] ?></td>
                            <td><span class="status-badge status-<?= $row['status_pembayaran'] == 'lunas' ? 'selesai' : ($row['status_pembayaran'] == 'hutang' ? 'diproses' : 'pending') ?>"><?= ucfirst($row['status_pembayaran']) ?></span></td>
                            <td><span class="status-badge status-<?= $row['status_pesanan'] ?>"><?= $row['status_pesanan'] ?></span></td>
                            <td><?= nl2br(htmlspecialchars($row['catatan'])) ?></td>
                            <td><?php if ($row['bukti_transfer']): ?><a href="../uploads/bukti/<?= $row['bukti_transfer'] ?>" target="_blank" class="btn btn-sm btn-info">Lihat</a><?php else: ?>-<?php endif; ?></td>
                            <td><?= $row['tanggal_pesan'] ?></td>
                            <td>
                                <form method="POST" class="d-flex gap-1">
                                    <input type="hidden" name="id_pesanan" value="<?= $row['id'] ?>">
                                    <select name="status" class="form-select form-select-sm" style="width:120px;">
                                        <option value="pending" <?= $row['status_pesanan'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="diproses" <?= $row['status_pesanan'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="dikirim" <?= $row['status_pesanan'] == 'dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option value="selesai" <?= $row['status_pesanan'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="dibatalkan" <?= $row['status_pesanan'] == 'dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                </form>
                                <?php if ($row['metode_pembayaran'] == 'transfer' && $row['status_pembayaran'] == 'pending' && $row['bukti_transfer']): ?>
                                    <a href="pesanan.php?konfirmasi_transfer=<?= $row['id'] ?>" class="btn btn-success btn-sm mt-1">Konfirmasi Bayar</a>
                                    <a href="pesanan.php?tolak_bukti=<?= $row['id'] ?>" class="btn btn-danger btn-sm mt-1">Tolak</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>