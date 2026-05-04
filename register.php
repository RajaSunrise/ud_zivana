<?php
include 'config/database.php';

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $query = "INSERT INTO users (nama, email, password, no_hp, alamat, role) VALUES ('$nama', '$email', '$pass', '$no_hp', '$alamat', 'customer')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        $error = "Registrasi gagal, email mungkin sudah terdaftar.";
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Register - UD Zivana</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="bg-light">
    <div class="container mt-5" style="max-width: 500px;">
        <div class="card border-0 shadow rounded-4">
            <div class="card-header bg-primary text-white text-center fw-bold">Daftar Akun</div>
            <div class="card-body">
                <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form method="POST">
                    <div class="mb-3"><label>Nama Lengkap</label><input type="text" name="nama" class="form-control" required></div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="mb-3"><label>No HP</label><input type="text" name="no_hp" class="form-control" required></div>
                    <div class="mb-3"><label>Alamat</label><textarea name="alamat" class="form-control" required></textarea></div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
                </form>
                <p class="mt-3 text-center">Sudah punya akun? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>

</html>