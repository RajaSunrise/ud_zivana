<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') header("Location: ../login.php");
include '../config/database.php';

$id = $_GET['id'];
$uid = $_SESSION['user_id'];
$hutang = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hutang WHERE id=$id AND user_id=$uid AND status='belum_lunas'"));
if (!$hutang) die("Hutang tidak ditemukan.");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $jumlah = $_POST['jumlah_bayar'];
    if ($jumlah > $hutang['sisa_hutang']) {
        $error = "Jumlah melebihi sisa hutang!";
    } else {
        $target_dir = "../uploads/bukti/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        $file_name = time() . "_" . basename($_FILES["bukti"]["name"]);
        move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_dir . $file_name);
        mysqli_query($conn, "INSERT INTO pembayaran_hutang (hutang_id, jumlah_bayar, bukti_transfer, dikonfirmasi_admin) VALUES ('$id','$jumlah','$file_name',0)");
        $sisa_baru = $hutang['sisa_hutang'] - $jumlah;
        if ($sisa_baru <= 0) {
            mysqli_query($conn, "UPDATE hutang SET status='lunas', sisa_hutang=0 WHERE id=$id");
            mysqli_query($conn, "UPDATE pesanan SET status_pembayaran='lunas' WHERE id=" . $hutang['pesanan_id']);
        } else {
            mysqli_query($conn, "UPDATE hutang SET sisa_hutang=$sisa_baru WHERE id=$id");
        }
        echo "<script>alert('Pembayaran dikirim, menunggu konfirmasi admin'); window.location='hutang_saya.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bayar Hutang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php include 'navbar_customer.php'; ?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h3>Bayar Hutang</h3>
                <p>Sisa Hutang: <strong>Rp <?= number_format($hutang['sisa_hutang'], 0, ',', '.') ?></strong></p>
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3"><label>Jumlah Bayar</label><input type="number" name="jumlah_bayar" class="form-control" max="<?= $hutang['sisa_hutang'] ?>" required></div>
                    <div class="mb-3"><label>Upload Bukti Transfer</label><input type="file" name="bukti" class="form-control" accept="image/*" required></div>
                    <button type="submit" class="btn btn-success">Kirim</button>
                    <a href="hutang_saya.php" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>