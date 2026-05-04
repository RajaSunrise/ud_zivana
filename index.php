<?php
session_start();
include 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UD Zivana - Ayam Potong Segar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .product-img img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        .product-img .no-image {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- Navbar Guest -->
    <nav class="nav-shopee">
        <div class="container">
            <a href="index.php" class="logo"><i class="fas fa-drumstick-bite"></i> UD Zivana</a>
            <div class="nav-links">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin/dashboard.php"><i class="fas fa-tachometer-alt"></i> Admin</a>
                    <?php else: ?>
                        <a href="customer/dashboard.php"><i class="fas fa-user"></i> Akun Saya</a>
                        <a href="customer/keranjang.php"><i class="fas fa-shopping-cart"></i> Keranjang</a>
                    <?php endif; ?>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- PRODUK DI ATAS (sesuai permintaan: produk jadi atas) -->
    <div class="container" id="produk">
        <h2 class="mb-4" style="color: var(--primary);">🌟 Produk Unggulan</h2>
        <div class="row">
            <?php
            $query = "SELECT * FROM produk ORDER BY id DESC";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) == 0): ?>
                <div class="alert alert-warning">Belum ada produk.</div>
            <?php endif;
            while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="product-card">
                        <div class="product-img">
                            <?php if ($row['gambar'] && file_exists("uploads/produk/" . $row['gambar'])): ?>
                                <img src="uploads/produk/<?= $row['gambar'] ?>" alt="<?= htmlspecialchars($row['nama_produk']) ?>">
                            <?php else: ?>
                                <div class="no-image">
                                    <i class="fas fa-drumstick-bite fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="product-info">
                            <div class="product-title"><?= htmlspecialchars($row['nama_produk']) ?></div>
                            <div class="product-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                            <div class="product-stock"><i class="fas fa-box"></i> Stok: <?= $row['stok'] ?> kg</div>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer'): ?>
                                <a href="customer/keranjang.php?add=<?= $row['id'] ?>" class="btn-shopee"><i class="fas fa-cart-plus"></i> Beli</a>
                            <?php elseif (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin'): ?>
                                <button class="btn-shopee" disabled style="background:#95a5a6;">Admin</button>
                            <?php else: ?>
                                <a href="login.php" class="btn-shopee">Login dulu</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- HERO SECTION DIPINDAHKAN KE BAWAH PRODUK -->
    <div class="container mt-4">
        <div class="hero-section">
            <h1><i class="fas fa-drumstick-bite"></i> Ayam Potong Segar UD Zivana</h1>
            <p>Frozen & Fresh • Halal • Harga Terjangkau • Siap Kirim</p>
            <a href="#produk" class="btn btn-light mt-3">Belanja Sekarang</a>
        </div>
    </div>

    <footer class="footer-shopee">
        <div class="container">
            <p>© 2025 UD Zivana - Ayam Potong Segar</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>