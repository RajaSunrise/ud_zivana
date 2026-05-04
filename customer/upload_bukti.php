<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$pesanan_id = $_GET['id'];
$uid = $_SESSION['user_id'];
$pesanan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM pesanan WHERE id=$pesanan_id AND user_id=$uid AND metode_pembayaran='transfer' AND status_pembayaran='pending'"));
if (!$pesanan) die("Pesanan tidak valid.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $target_dir = "../uploads/bukti/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
    $file_name = time() . "_" . basename($_FILES["bukti"]["name"]);
    if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_dir . $file_name)) {
        mysqli_query($conn, "UPDATE pesanan SET bukti_transfer='$file_name' WHERE id=$pesanan_id");
        echo "<script>alert('Bukti terupload'); window.location='pesanan_saya.php';</script>";
    } else {
        $error = "Gagal upload.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Upload Bukti</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">Upload Bukti Transfer</div>
            <div class="card-body">
                <p>Pesanan #<?= $pesanan_id ?> - Total: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>
                <p>Transfer ke BCA 1234567890 a.n UD Zivana atau Mandiri 9876543210 a.n UD Zivana</p>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label>Bukti Transfer</label><input type="file" name="bukti" class="form-control" accept="image/*" required></div>
                    <button type="submit" class="btn btn-success">Kirim</button>
                    <a href="pesanan_saya.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>