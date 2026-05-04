<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

if (empty($_SESSION['cart'])) header("Location: keranjang.php");

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT alamat FROM users WHERE id=$uid"));
$alamat_terdaftar = $user['alamat'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $metode = $_POST['metode'];
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    if (!empty($alamat)) mysqli_query($conn, "UPDATE users SET alamat='$alamat' WHERE id=$uid");

    $total = 0;
    foreach ($_SESSION['cart'] as $item) $total += $item['harga'] * $item['qty'];
    $status_bayar = ($metode == 'hutang') ? 'hutang' : 'pending';

    mysqli_query($conn, "INSERT INTO pesanan (user_id, total_harga, metode_pembayaran, status_pembayaran, status_pesanan, catatan) VALUES ('$uid','$total','$metode','$status_bayar','pending','$catatan')");
    $pesanan_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $item) {
        mysqli_query($conn, "INSERT INTO detail_pesanan (pesanan_id, produk_id, jumlah, harga_satuan) VALUES ('$pesanan_id','{$item['id']}','{$item['qty']}','{$item['harga']}')");
        mysqli_query($conn, "UPDATE produk SET stok = stok - {$item['qty']} WHERE id={$item['id']}");
    }

    if ($metode == 'hutang') {
        $jatuh = date('Y-m-d', strtotime('+30 days'));
        mysqli_query($conn, "INSERT INTO hutang (pesanan_id, user_id, total_hutang, sisa_hutang, jatuh_tempo, status) VALUES ('$pesanan_id','$uid','$total','$total','$jatuh','belum_lunas')");
    }
    unset($_SESSION['cart']);
    echo "<script>alert('Pesanan berhasil!'); window.location='pesanan_saya.php';</script>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Checkout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <h3>Checkout</h3>
        <div class="checkout-container">
            <div class="checkout-left">
                <h5>Ringkasan Pesanan</h5>
                <table class="table">
                    <tr>
                        <th>Produk</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                    <?php $total = 0;
                    foreach ($_SESSION['cart'] as $item): $sub = $item['harga'] * $item['qty'];
                        $total += $sub; ?>
                        <tr>
                            <td><?= $item['nama'] ?></td>
                            <td><?= $item['qty'] ?> kg</td>
                            <td>Rp <?= number_format($sub, 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="fw-bold">
                        <td colspan="2">Total</td>
                        <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
                    </tr>
                </table>
            </div>
            <div class="checkout-right">
                <h5>Detail Pengiriman</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label>Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="alamat" class="form-control" rows="3" required placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kota, Kode Pos"><?= htmlspecialchars($alamat_terdaftar) ?></textarea>
                        <small class="text-muted">Pastikan alamat akurat</small>
                    </div>
                    <div class="mb-3">
                        <label>Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: Tolong pisahkan paha dan dada"></textarea>
                    </div>
                    <h5>Metode Pembayaran</h5>
                    <div class="method-option" data-method="tunai"><i class="fas fa-money-bill-wave"></i> Tunai (COD)</div>
                    <div class="method-option" data-method="transfer"><i class="fas fa-university"></i> Transfer Bank</div>
                    <div class="method-option" data-method="hutang"><i class="fas fa-hand-holding-usd"></i> Hutang (bayar nanti)</div>
                    <input type="hidden" name="metode" id="metode" required>
                    <button type="submit" class="btn-shopee w-100 mt-3">Konfirmasi Pesanan</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.method-option').forEach(opt => {
            opt.addEventListener('click', function() {
                document.querySelectorAll('.method-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('metode').value = this.dataset.method;
            });
        });
    </script>
</body>

</html>