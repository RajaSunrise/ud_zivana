<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') header("Location: ../login.php");
include '../config/database.php';

// Folder penyimpanan gambar
$target_dir = "../uploads/produk/";
if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

// Tambah produk dengan gambar
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $gambar = null;

    if (!empty($_FILES['gambar']['name'])) {
        $file_name = time() . "_" . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $file_name;
        }
    }

    mysqli_query($conn, "INSERT INTO produk (nama_produk, stok, harga, deskripsi, gambar) 
                         VALUES ('$nama','$stok','$harga','$deskripsi','$gambar')");
    echo "<script>alert('Produk ditambahkan'); window.location='produk.php';</script>";
}

// Hapus produk (serta file gambar)
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    
    // Cek apakah produk pernah dipesan
    $cek = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM detail_pesanan WHERE produk_id = $id");
    $row = mysqli_fetch_assoc($cek);
    if ($row['cnt'] > 0) {
        echo "<script>alert('Produk tidak bisa dihapus karena sudah pernah dipesan.'); window.location='produk.php';</script>";
        exit;
    }
    
    $gambar = mysqli_fetch_assoc(mysqli_query($conn, "SELECT gambar FROM produk WHERE id=$id"))['gambar'];
    if ($gambar && file_exists($target_dir . $gambar)) unlink($target_dir . $gambar);
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    echo "<script>alert('Produk dihapus'); window.location='produk.php';</script>";
}

// Edit produk dengan kemungkinan ganti gambar
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $stok = $_POST['stok'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $gambar_lama = $_POST['gambar_lama'];

    $gambar = $gambar_lama;
    if (!empty($_FILES['gambar']['name'])) {
        $file_name = time() . "_" . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            // Hapus gambar lama jika ada
            if ($gambar_lama && file_exists($target_dir . $gambar_lama)) unlink($target_dir . $gambar_lama);
            $gambar = $file_name;
        }
    }
    // Hapus gambar jika checkbox centang
    if (isset($_POST['hapus_gambar'])) {
        if ($gambar_lama && file_exists($target_dir . $gambar_lama)) unlink($target_dir . $gambar_lama);
        $gambar = null;
    }

    mysqli_query($conn, "UPDATE produk SET nama_produk='$nama', stok='$stok', harga='$harga', deskripsi='$deskripsi', gambar='$gambar' WHERE id=$id");
    echo "<script>alert('Produk diupdate'); window.location='produk.php';</script>";
}

$produk = mysqli_query($conn, "SELECT * FROM produk");
?>
<!DOCTYPE html>
<html>

<head>
    <title>Kelola Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_admin.php'; ?>
    <div class="container mt-4">
        <h2>Kelola Produk</h2>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Produk</button>
        <table class="table table-bordered table-custom">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Gambar</th>
                    <th>Nama</th>
                    <th>Stok (kg)</th>
                    <th>Harga/kg</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($produk)): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td>
                            <?php if ($row['gambar']): ?>
                                <img src="../uploads/produk/<?= $row['gambar'] ?>" width="50" height="50" style="object-fit: cover;">
                            <?php else: ?>
                                <i class="fas fa-drumstick-bite fa-2x"></i>
                            <?php endif; ?>
                        </td>
                        <td><?= $row['nama_produk'] ?></td>
                        <td><?= $row['stok'] ?> kg</td>
                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id'] ?>">Edit</button>
                            <a href="produk.php?hapus=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin?')">Hapus</a>
                        </td>
                    </tr>
                    <!-- Modal Edit -->
                    <div class="modal fade" id="modalEdit<?= $row['id'] ?>">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="POST" enctype="multipart/form-data">
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <input type="hidden" name="gambar_lama" value="<?= $row['gambar'] ?>">
                                        <div class="mb-2"><label>Nama</label><input type="text" name="nama" class="form-control" value="<?= $row['nama_produk'] ?>" required></div>
                                        <div class="mb-2"><label>Stok (kg)</label><input type="number" name="stok" class="form-control" value="<?= $row['stok'] ?>" required></div>
                                        <div class="mb-2"><label>Harga/kg</label><input type="number" name="harga" class="form-control" value="<?= $row['harga'] ?>" required></div>
                                        <div class="mb-2"><label>Deskripsi</label><textarea name="deskripsi" class="form-control"><?= $row['deskripsi'] ?></textarea></div>
                                        <div class="mb-2">
                                            <label>Gambar Saat Ini</label><br>
                                            <?php if ($row['gambar']): ?>
                                                <img src="../uploads/produk/<?= $row['gambar'] ?>" width="100" class="mb-2"><br>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="hapus_gambar" id="hapusGambar<?= $row['id'] ?>">
                                                    <label class="form-check-label" for="hapusGambar<?= $row['id'] ?>">Hapus gambar</label>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">Belum ada gambar</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="mb-2"><label>Ganti Gambar</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                                        <button type="submit" name="edit" class="btn btn-success">Update</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal Tambah -->
    <div class="modal fade" id="modalTambah">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-2"><label>Nama Produk</label><input type="text" name="nama" class="form-control" required></div>
                        <div class="mb-2"><label>Stok (kg)</label><input type="number" name="stok" class="form-control" required></div>
                        <div class="mb-2"><label>Harga/kg</label><input type="number" name="harga" class="form-control" required></div>
                        <div class="mb-2"><label>Deskripsi</label><textarea name="deskripsi" class="form-control"></textarea></div>
                        <div class="mb-2"><label>Gambar Produk</label><input type="file" name="gambar" class="form-control" accept="image/*"></div>
                        <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>