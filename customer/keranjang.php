<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['add'])) {
    $id = $_GET['add'];
    $q = mysqli_query($conn, "SELECT * FROM produk WHERE id=$id");
    $p = mysqli_fetch_assoc($q);
    if ($p) {
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['qty']++;
        else $_SESSION['cart'][$id] = ['id' => $p['id'], 'nama' => $p['nama_produk'], 'harga' => $p['harga'], 'qty' => 1, 'stok' => $p['stok']];
    }
    header("Location: keranjang.php");
}
if (isset($_POST['update'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id]['qty'] = $qty;
    }
    header("Location: keranjang.php");
}
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: keranjang.php");
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Keranjang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <h3>Keranjang Belanja</h3>
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="alert alert-info">Keranjang kosong. <a href="../index.php">Belanja sekarang</a></div>
        <?php else: ?>
            <form method="POST">
                <div class="cart-table">
                    <?php $total = 0;
                    foreach ($_SESSION['cart'] as $id => $item): $sub = $item['harga'] * $item['qty'];
                        $total += $sub; ?>
                        <div class="cart-item">
                            <div class="cart-item-img"><i class="fas fa-drumstick-bite fa-2x"></i></div>
                            <div class="cart-item-info"><strong><?= $item['nama'] ?></strong><br><small>Stok: <?= $item['stok'] ?> kg</small></div>
                            <div class="cart-item-price">Rp <?= number_format($item['harga'], 0, ',', '.') ?></div>
                            <div class="cart-item-qty"><input type="number" name="qty[<?= $id ?>]" value="<?= $item['qty'] ?>" min="0" max="<?= $item['stok'] ?>"></div>
                            <div class="cart-item-subtotal">Rp <?= number_format($sub, 0, ',', '.') ?></div>
                            <div class="cart-item-remove"><a href="keranjang.php?remove=<?= $id ?>" class="text-danger"><i class="fas fa-trash-alt"></i></a></div>
                        </div>
                    <?php endforeach; ?>
                    <div class="p-3 text-end bg-light"><strong>Total: Rp <?= number_format($total, 0, ',', '.') ?></strong></div>
                </div>
                <div class="mt-3">
                    <button type="submit" name="update" class="btn btn-secondary">Update</button>
                    <a href="checkout.php" class="btn-shopee">Checkout</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>

</html>