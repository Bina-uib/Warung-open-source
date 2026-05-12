<?php
session_start();
 if(isset($_SESSION['admin'])){
header("Location: admin.php");
exit;
}
 // Kredensial disimpan sebagai hash (generate dengan: password_hash('admin22',
PASSWORD_DEFAULT))
// Default: username=admin123, password=admin22
define('ADMIN_USER', 'admin123');
define('ADMIN_PASS_HASH',
'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 
// Hash di atas = "password" (default Laravel), ganti dengan hash milikmu
// Untuk generate: php -r "echo password_hash('admin22', PASSWORD_DEFAULT);"
 $error = "";
 if($_SERVER["REQUEST_METHOD"] == "POST"){
$user = trim($_POST['username'] ?? '');
$pass = trim($_POST['password'] ?? '');
  // Validasi input tidak kosong
if(empty($user) || empty($pass)){
$error = "Username dan password wajib diisi!";
} elseif($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH))
{
$_SESSION['admin'] = true;
$_SESSION['admin_user'] = $user;
header("Location: admin.php");
exit;
} else {
// Fallback: cek langsung (untuk kemudahan development, hapus di production)
if($user === 'admin123' && $pass === 'admin22'){
$_SESSION['admin'] = true;
$_SESSION['admin_user'] = $user;
header("Location: admin.php");
exit;
}
$error = "Username / Password salah!";
}}
?>
 <!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login Admin - Warung OS</title>
<link rel="stylesheet" href="style.css">
<style>
.login-box {
background: #fff8f0;
padding: 40px;
border-radius: 20px;
box-shadow: 0 10px 30px rgba(0,0,0,0.15);
width: 340px;
text-align: center;
}
.login-box h1 {
margin-bottom: 25px;
font-size: 26px;
color: #4e2d1f;
}
.login-box input {
width: 100%;
padding: 12px;
margin-bottom: 15px;
border-radius: 10px;
border: 2px solid #ddd;
font-size: 15px;
box-sizing: border-box;
outline: none;
transition: border 0.2s;
}
.login-box input:focus {
border-color: #a34700;
}
.login-box .btn {
width: 100%;
padding: 13px;
background: #a34700;color: white;
border: none;
border-radius: 10px;
font-size: 16px;
cursor: pointer;
transition: 0.3s;
}
.login-box .btn:hover {
background: #7a3300;
transform: scale(1.02);
}
.error-msg {
background: #ffe0e0;
color: #c00;
padding: 10px;
border-radius: 8px;
margin-bottom: 15px;
font-size: 14px;
}
.back-link {
display: inline-block;
margin-top: 20px;
color: #a34700;
text-decoration: none;
font-weight: bold;
}
.back-link:hover { text-decoration: underline; }
</style>
</head>
<body>
 <div class="login-box">
<h1>🔐 Login Admin</h1>
  <?php if($error): ?>
<div class="error-msg">⚠️ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>
  <form method="POST" autocomplete="off">
<input type="text" name="username" placeholder="Username" 
value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
required autofocus><input type="password" name="password" placeholder="Password" required>
<button type="submit" class="btn">Login</button>
</form>
  <a href="index.php" class="back-link">← Kembali ke Beranda</a>
</div>
 </body>
</html>
