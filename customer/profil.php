<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$uid = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    mysqli_query($conn, "UPDATE users SET nama='$nama', no_hp='$no_hp', alamat='$alamat' WHERE id=$uid");
    $_SESSION['nama'] = $nama;
    echo "<script>alert('Profil berhasil diupdate'); window.location='profil.php';</script>";
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4" style="max-width: 600px;">
        <div class="card border-0 shadow rounded-4">
            <div class="card-header bg-white fw-bold">Edit Profil</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['nama']) ?>" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" class="form-control" value="<?= $user['email'] ?>" disabled></div>
                    <div class="mb-3"><label>No HP</label><input type="text" name="no_hp" class="form-control" value="<?= $user['no_hp'] ?>" required></div>
                    <div class="mb-3"><label>Alamat Lengkap</label><textarea name="alamat" class="form-control" rows="4" required><?= htmlspecialchars($user['alamat']) ?></textarea></div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                    <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>