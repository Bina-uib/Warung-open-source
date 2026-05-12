<?php
session_start();
include "config.php";
 // Handle registrasi customer
if(isset($_POST['submit'])){
$phone = trim($_POST['phone'] ?? '');
$table_id = (int)($_POST['table'] ?? 0);
  if(!preg_match('/^08[0-9]{8,11}$/', $phone)){
$reg_error = "No HP harus diawali 08 dan 10-13 digit angka!";
} elseif($table_id <= 0){
$reg_error = "Pilih nomor meja terlebih dahulu!";
} else {
$stmt = $conn->prepare("INSERT INTO customers (phone, table_id) VALUES (?,
?)");
$stmt->bind_param("si", $phone, $table_id);
$stmt->execute();
  $_SESSION['customer_id'] = $stmt->insert_id;
$_SESSION['table_id'] = $table_id;
  header("Location: customer.php");
exit;
}
}
 // Handle panggil pelayan (AJAX)
if(isset($_POST['call_waiter']) && isset($_SESSION['customer_id'])){
$table_id = (int)$_SESSION['table_id'];
// Cek apakah sudah ada panggilan aktif dari meja ini
$check = $conn->prepare("SELECT id FROM waiter_calls WHERE table_id=? AND
is_read=0");
$check->bind_param("i", $table_id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
  if(!$existing){
$stmt = $conn->prepare("INSERT INTO waiter_calls (table_id) VALUES (?)");
$stmt->bind_param("i", $table_id);
$stmt->execute();
}echo json_encode(['success' => true]);
exit;
}
 $hasLogin = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer - Warung OS</title>
<link rel="stylesheet" href="style.css">
<style>
body {
display: block;
min-height: 100vh;
padding: 20px;
}
.page-header {
background: #fff8f0;
padding: 15px 25px;
border-radius: 15px;
margin-bottom: 20px;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
display: flex;
justify-content: space-between;
align-items: center;
}
.page-header h1 { margin: 0; font-size: 22px; color: #4e2d1f; }
.meja-badge {
background: #a34700;
color: white;
padding: 6px 14px;
border-radius: 20px;
font-weight: bold;
font-size: 14px;
}
.login-box {
background: #fff8f0;
padding: 40px;
border-radius: 20px;box-shadow: 0 10px 30px rgba(0,0,0,0.15);
max-width: 380px;
margin: 60px auto;
text-align: center;
}
.login-box h1 { margin-bottom: 25px; font-size: 24px; color: #4e2d1f; }
.login-box input, .login-box select {
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
.login-box input:focus, .login-box select:focus { border-color: #a34700; }
.login-box .btn-masuk {
width: 100%;
padding: 13px;
background: #a34700;
color: white;
border: none;
border-radius: 10px;
font-size: 16px;
cursor: pointer;
transition: 0.3s;
}
.login-box .btn-masuk:hover { background: #7a3300; }
.error-msg {
background: #ffe0e0;
color: #c00;
padding: 10px;
border-radius: 8px;
margin-bottom: 15px;
font-size: 14px;
}
.section-title {
font-size: 20px;
font-weight: bold;
color: #4e2d1f;margin: 25px 0 15px;
border-left: 4px solid #a34700;
padding-left: 10px;
}
.menu-grid {
display: grid;
grid-template-columns: repeat(3, 1fr);
gap: 16px;
margin-bottom: 25px;
}
@media (max-width: 768px) { .menu-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 480px) { .menu-grid { grid-template-columns: 1fr; } }
.menu-card {
background: white;
padding: 18px;
border-radius: 15px;
text-align: center;
box-shadow: 0 4px 12px rgba(0,0,0,0.12);
transition: 0.2s;
}
.menu-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px
rgba(0,0,0,0.15); }
.menu-card h4 { margin: 0 0 6px; color: #4e2d1f; font-size: 15px; }
.menu-card .price { color: #a34700; font-weight: bold; margin-bottom: 10px; font
size: 14px; }
.menu-card input[type=number] {
width: 80px;
padding: 6px;
text-align: center;
border-radius: 8px;
border: 2px solid #ddd;
font-size: 15px;
outline: none;
}
.menu-card input[type=number]:focus { border-color: #a34700; }
.btn-order {
display: block;
width: 100%;
padding: 16px;
background: #a34700;
color: white;
border: none;border-radius: 12px;
font-size: 18px;
cursor: pointer;
margin-top: 10px;
transition: 0.3s;
}
.btn-order:hover { background: #7a3300; transform: scale(1.01); }
.call-btn {
position: fixed;
bottom: 25px;
right: 25px;
background: #d32f2f;
color: white;
border: none;
padding: 16px 22px;
border-radius: 14px;
font-size: 16px;
cursor: pointer;
box-shadow: 0 8px 20px rgba(0,0,0,0.25);
transition: 0.3s;
z-index: 999;
}
.call-btn:hover { background: #b71c1c; transform: scale(1.05); }
.call-btn:disabled { background: #888; cursor: default; }
.back-link {
color: #a34700;
text-decoration: none;
font-weight: bold;
display: inline-block;
margin-bottom: 15px;
}
.back-link:hover { text-decoration: underline; }
.toast {
position: fixed;
bottom: 85px;
right: 25px;
background: #333;
color: white;
padding: 12px 20px;
border-radius: 10px;
font-size: 14px;
display: none;z-index: 1000;
box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
</style>
</head>
<body>
 <?php if(!$hasLogin): ?>
 <div class="login-box">
<h1>🧑‍🍽️ Masuk Customer</h1>
  <?php if(isset($reg_error)): ?>
<div class="error-msg">⚠️ <?= htmlspecialchars($reg_error) ?></div>
<?php endif; ?>
  <form method="POST">
<input type="tel" name="phone" placeholder="No HP (contoh: 081234567890)"
pattern="08[0-9]{8,11}" value="<?= htmlspecialchars($_POST['phone'] ?? '')
?>"
required>
  <select name="table" required>
<option value="">— Pilih No Meja —</option>
<?php
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
while($t = $tables->fetch_assoc()){
$sel = (isset($_POST['table']) && $_POST['table'] == $t['id']) ? 'selected' : '';
echo "<option value='{$t['id']}' $sel>Meja {$t['table_number']}</option>";
}
?>
</select>
  <button type="submit" name="submit" class="btn-masuk">Masuk</button>
</form>
  <br>
<a href="index.php" style="color:#a34700;">← Kembali ke Beranda</a>
</div>
 <?php else: ?>
<?php
// Ambil nomor meja
$tbl = $conn->prepare("SELECT table_number FROM tables WHERE id=?");
$tbl->bind_param("i", $_SESSION['table_id']);
$tbl->execute();
$tbl_row = $tbl->get_result()->fetch_assoc();
$table_number = $tbl_row['table_number'] ?? $_SESSION['table_id'];
?>
 <div class="page-header">
<h1>🍜 Menu Warung</h1>
<span class="meja-badge">Meja <?= htmlspecialchars($table_number) ?></span>
</div>
 <a href="logout_customer.php" class="back-link">← Ganti Meja / Keluar</a>
 <form method="POST" action="confirm_order.php" id="orderForm">
 <div class="section-title">🍛 Makanan</div>
<div class="menu-grid">
<?php
$makanan = $conn->query("SELECT * FROM products WHERE category='makanan'
ORDER BY name");
while($m = $makanan->fetch_assoc()):
?>
<div class="menu-card">
<h4><?= htmlspecialchars($m['name']) ?></h4>
<div class="price">Rp <?= number_format($m['price']) ?></div>
<input type="number" name="qty[<?= $m['id'] ?>]" min="0" max="20"
value="0">
</div>
<?php endwhile; ?>
</div>
 <div class="section-title">🥤 Minuman</div>
<div class="menu-grid">
<?php
$minuman = $conn->query("SELECT * FROM products WHERE category='minuman'
ORDER BY name");
while($d = $minuman->fetch_assoc()):
?>
<div class="menu-card"><h4><?= htmlspecialchars($d['name']) ?></h4>
<div class="price">Rp <?= number_format($d['price']) ?></div>
<input type="number" name="qty[<?= $d['id'] ?>]" min="0" max="20"
value="0">
</div>
<?php endwhile; ?>
</div>
 <button type="submit" class="btn-order">🛒 Lanjut Pesan</button>
 </form>
 <!-- Tombol Panggil Pelayan -->
<button class="call-btn" id="callBtn" onclick="panggilPelayan()">
🔔 Panggil Pelayan
</button>
 <div class="toast" id="toast"></div>
 <script>
function panggilPelayan(){
const btn = document.getElementById('callBtn');
btn.disabled = true;
btn.textContent = '⏳ Mengirim...';
  fetch('customer.php', {
method: 'POST',
headers: {'Content-Type': 'application/x-www-form-urlencoded'},
body: 'call_waiter=1'
})
.then(r => r.json())
.then(data => {
showToast('✅ Pelayan sudah dipanggil! Harap tunggu.');
btn.textContent = '✓ Pelayan Dipanggil';
setTimeout(() => {
btn.disabled = false;
btn.textContent = '🔔 Panggil Pelayan';
}, 30000); // cooldown 30 detik
})
.catch(() => {
showToast('❌ Gagal memanggil pelayan, coba lagi.');
btn.disabled = false;btn.textContent = '🔔 Panggil Pelayan';
});
}
 function showToast(msg){
const toast = document.getElementById('toast');
toast.textContent = msg;
toast.style.display = 'block';
setTimeout(() => { toast.style.display = 'none'; }, 4000);
}
 // Validasi: minimal 1 item sebelum order
document.getElementById('orderForm').addEventListener('submit', function(e){
const inputs = this.querySelectorAll('input[type=number]');
let total = 0;
inputs.forEach(i => total += parseInt(i.value) || 0);
if(total === 0){
e.preventDefault();
showToast('⚠️ Pilih minimal 1 menu terlebih dahulu!');
}
});
</script>
 <?php endif; ?>
 </body>
</html><?php
session_start();
include "config.php";
 // Handle registrasi customer
if(isset($_POST['submit'])){
$phone = trim($_POST['phone'] ?? '');
$table_id = (int)($_POST['table'] ?? 0);
  if(!preg_match('/^08[0-9]{8,11}$/', $phone)){
$reg_error = "No HP harus diawali 08 dan 10-13 digit angka!";
} elseif($table_id <= 0){
$reg_error = "Pilih nomor meja terlebih dahulu!";
} else {
$stmt = $conn->prepare("INSERT INTO customers (phone, table_id) VALUES (?,
?)");
$stmt->bind_param("si", $phone, $table_id);
$stmt->execute();
  $_SESSION['customer_id'] = $stmt->insert_id;
$_SESSION['table_id'] = $table_id;
  header("Location: customer.php");
exit;
}
}
 // Handle panggil pelayan (AJAX)
if(isset($_POST['call_waiter']) && isset($_SESSION['customer_id'])){
$table_id = (int)$_SESSION['table_id'];
// Cek apakah sudah ada panggilan aktif dari meja ini
$check = $conn->prepare("SELECT id FROM waiter_calls WHERE table_id=? AND
is_read=0");
$check->bind_param("i", $table_id);
$check->execute();
$existing = $check->get_result()->fetch_assoc();
  if(!$existing){
$stmt = $conn->prepare("INSERT INTO waiter_calls (table_id) VALUES (?)");
$stmt->bind_param("i", $table_id);
$stmt->execute();
}echo json_encode(['success' => true]);
exit;
}
 $hasLogin = isset($_SESSION['customer_id']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer - Warung OS</title>
<link rel="stylesheet" href="style.css">
<style>
body {
display: block;
min-height: 100vh;
padding: 20px;
}
.page-header {
background: #fff8f0;
padding: 15px 25px;
border-radius: 15px;
margin-bottom: 20px;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
display: flex;
justify-content: space-between;
align-items: center;
}
.page-header h1 { margin: 0; font-size: 22px; color: #4e2d1f; }
.meja-badge {
background: #a34700;
color: white;
padding: 6px 14px;
border-radius: 20px;
font-weight: bold;
font-size: 14px;
}
.login-box {
background: #fff8f0;
padding: 40px;
border-radius: 20px;box-shadow: 0 10px 30px rgba(0,0,0,0.15);
max-width: 380px;
margin: 60px auto;
text-align: center;
}
.login-box h1 { margin-bottom: 25px; font-size: 24px; color: #4e2d1f; }
.login-box input, .login-box select {
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
.login-box input:focus, .login-box select:focus { border-color: #a34700; }
.login-box .btn-masuk {
width: 100%;
padding: 13px;
background: #a34700;
color: white;
border: none;
border-radius: 10px;
font-size: 16px;
cursor: pointer;
transition: 0.3s;
}
.login-box .btn-masuk:hover { background: #7a3300; }
.error-msg {
background: #ffe0e0;
color: #c00;
padding: 10px;
border-radius: 8px;
margin-bottom: 15px;
font-size: 14px;
}
.section-title {
font-size: 20px;
font-weight: bold;
color: #4e2d1f;margin: 25px 0 15px;
border-left: 4px solid #a34700;
padding-left: 10px;
}
.menu-grid {
display: grid;
grid-template-columns: repeat(3, 1fr);
gap: 16px;
margin-bottom: 25px;
}
@media (max-width: 768px) { .menu-grid { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 480px) { .menu-grid { grid-template-columns: 1fr; } }
.menu-card {
background: white;
padding: 18px;
border-radius: 15px;
text-align: center;
box-shadow: 0 4px 12px rgba(0,0,0,0.12);
transition: 0.2s;
}
.menu-card:hover { transform: translateY(-3px); box-shadow: 0 8px 20px
rgba(0,0,0,0.15); }
.menu-card h4 { margin: 0 0 6px; color: #4e2d1f; font-size: 15px; }
.menu-card .price { color: #a34700; font-weight: bold; margin-bottom: 10px; font
size: 14px; }
.menu-card input[type=number] {
width: 80px;
padding: 6px;
text-align: center;
border-radius: 8px;
border: 2px solid #ddd;
font-size: 15px;
outline: none;
}
.menu-card input[type=number]:focus { border-color: #a34700; }
.btn-order {
display: block;
width: 100%;
padding: 16px;
background: #a34700;
color: white;
border: none;border-radius: 12px;
font-size: 18px;
cursor: pointer;
margin-top: 10px;
transition: 0.3s;
}
.btn-order:hover { background: #7a3300; transform: scale(1.01); }
.call-btn {
position: fixed;
bottom: 25px;
right: 25px;
background: #d32f2f;
color: white;
border: none;
padding: 16px 22px;
border-radius: 14px;
font-size: 16px;
cursor: pointer;
box-shadow: 0 8px 20px rgba(0,0,0,0.25);
transition: 0.3s;
z-index: 999;
}
.call-btn:hover { background: #b71c1c; transform: scale(1.05); }
.call-btn:disabled { background: #888; cursor: default; }
.back-link {
color: #a34700;
text-decoration: none;
font-weight: bold;
display: inline-block;
margin-bottom: 15px;
}
.back-link:hover { text-decoration: underline; }
.toast {
position: fixed;
bottom: 85px;
right: 25px;
background: #333;
color: white;
padding: 12px 20px;
border-radius: 10px;
font-size: 14px;
display: none;z-index: 1000;
box-shadow: 0 4px 12px rgba(0,0,0,0.3);
}
</style>
</head>
<body>
 <?php if(!$hasLogin): ?>
 <div class="login-box">
<h1>🧑‍🍽️ Masuk Customer</h1>
  <?php if(isset($reg_error)): ?>
<div class="error-msg">⚠️ <?= htmlspecialchars($reg_error) ?></div>
<?php endif; ?>
  <form method="POST">
<input type="tel" name="phone" placeholder="No HP (contoh: 081234567890)"
pattern="08[0-9]{8,11}" value="<?= htmlspecialchars($_POST['phone'] ?? '')
?>"
required>
  <select name="table" required>
<option value="">— Pilih No Meja —</option>
<?php
$tables = $conn->query("SELECT * FROM tables ORDER BY table_number");
while($t = $tables->fetch_assoc()){
$sel = (isset($_POST['table']) && $_POST['table'] == $t['id']) ? 'selected' : '';
echo "<option value='{$t['id']}' $sel>Meja {$t['table_number']}</option>";
}
?>
</select>
  <button type="submit" name="submit" class="btn-masuk">Masuk</button>
</form>
  <br>
<a href="index.php" style="color:#a34700;">← Kembali ke Beranda</a>
</div>
 <?php else: ?>
<?php
// Ambil nomor meja
$tbl = $conn->prepare("SELECT table_number FROM tables WHERE id=?");
$tbl->bind_param("i", $_SESSION['table_id']);
$tbl->execute();
$tbl_row = $tbl->get_result()->fetch_assoc();
$table_number = $tbl_row['table_number'] ?? $_SESSION['table_id'];
?>
 <div class="page-header">
<h1>🍜 Menu Warung</h1>
<span class="meja-badge">Meja <?= htmlspecialchars($table_number) ?></span>
</div>
 <a href="logout_customer.php" class="back-link">← Ganti Meja / Keluar</a>
 <form method="POST" action="confirm_order.php" id="orderForm">
 <div class="section-title">🍛 Makanan</div>
<div class="menu-grid">
<?php
$makanan = $conn->query("SELECT * FROM products WHERE category='makanan'
ORDER BY name");
while($m = $makanan->fetch_assoc()):
?>
<div class="menu-card">
<h4><?= htmlspecialchars($m['name']) ?></h4>
<div class="price">Rp <?= number_format($m['price']) ?></div>
<input type="number" name="qty[<?= $m['id'] ?>]" min="0" max="20"
value="0">
</div>
<?php endwhile; ?>
</div>
 <div class="section-title">🥤 Minuman</div>
<div class="menu-grid">
<?php
$minuman = $conn->query("SELECT * FROM products WHERE category='minuman'
ORDER BY name");
while($d = $minuman->fetch_assoc()):
?>
<div class="menu-card"><h4><?= htmlspecialchars($d['name']) ?></h4>
<div class="price">Rp <?= number_format($d['price']) ?></div>
<input type="number" name="qty[<?= $d['id'] ?>]" min="0" max="20"
value="0">
</div>
<?php endwhile; ?>
</div>
 <button type="submit" class="btn-order">🛒 Lanjut Pesan</button>
 </form>
 <!-- Tombol Panggil Pelayan -->
<button class="call-btn" id="callBtn" onclick="panggilPelayan()">
🔔 Panggil Pelayan
</button>
 <div class="toast" id="toast"></div>
 <script>
function panggilPelayan(){
const btn = document.getElementById('callBtn');
btn.disabled = true;
btn.textContent = '⏳ Mengirim...';
  fetch('customer.php', {
method: 'POST',
headers: {'Content-Type': 'application/x-www-form-urlencoded'},
body: 'call_waiter=1'
})
.then(r => r.json())
.then(data => {
showToast('✅ Pelayan sudah dipanggil! Harap tunggu.');
btn.textContent = '✓ Pelayan Dipanggil';
setTimeout(() => {
btn.disabled = false;
btn.textContent = '🔔 Panggil Pelayan';
}, 30000); // cooldown 30 detik
})
.catch(() => {
showToast('❌ Gagal memanggil pelayan, coba lagi.');
btn.disabled = false;btn.textContent = '🔔 Panggil Pelayan';
});
}
 function showToast(msg){
const toast = document.getElementById('toast');
toast.textContent = msg;
toast.style.display = 'block';
setTimeout(() => { toast.style.display = 'none'; }, 4000);
}
 // Validasi: minimal 1 item sebelum order
document.getElementById('orderForm').addEventListener('submit', function(e){
const inputs = this.querySelectorAll('input[type=number]');
let total = 0;
inputs.forEach(i => total += parseInt(i.value) || 0);
if(total === 0){
e.preventDefault();
showToast('⚠️ Pilih minimal 1 menu terlebih dahulu!');
}
});
</script>
 <?php endif; ?>
 </body>
</html><?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "warung_os";
 $conn = new mysqli($host, $user, $pass, $db);
 if ($conn->connect_error) {
die("Koneksi gagal: " . $conn->connect_error);
}
 $conn->set_charset("utf8");
?><?php
session_start();
include "config.php";

if(isset($_POST['submit'])){
    $phone = $_POST['phone'];
    $table_id = $_POST['table'];

    if(!preg_match('/^08[0-9]{8,11}$/', $phone)){
        echo "<script>alert('No HP harus angka semua & diawali 08!');</script>";
    } else {
        $stmt = $conn->prepare("INSERT INTO customers (phone, table_id) VALUES (?, ?)");
        $stmt->bind_param("si", $phone, $table_id);
        $stmt->execute();

        $_SESSION['customer_id'] = $stmt->insert_id;
        $_SESSION['table_id'] = $table_id;

        header("Location: customer.php");
        exit;
    }
}

$hasLogin = isset($_SESSION['customer_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Customer</title>
<link rel="stylesheet" href="style.css">

<style>
/* 🔥 FIX GRID */
.menu-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    width: 100%;
}

/* container jangan sempit */
.container {
    max-width: 1000px;
    margin: auto;
}

/* card */
.card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.card input {
    width: 70px;
    padding: 5px;
    text-align: center;
}

/* responsive */
@media (max-width: 768px){
    .menu-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px){
    .menu-grid {
        grid-template-columns: 1fr;
    }
}
</style>

</head>
<body>

<?php if(!$hasLogin): ?>

<div class="container">
    <h1>Masuk Customer</h1>

    <form method="POST">
        <input type="text" name="phone" placeholder="No HP"
               pattern="08[0-9]{8,11}" required><br><br>

        <select name="table" required>
            <option value="">Pilih No Meja</option>
            <?php
            $tables = $conn->query("SELECT * FROM tables");
            while($t = $tables->fetch_assoc()){
                echo "<option value='{$t['id']}'>Meja {$t['table_number']}</option>";
            }
            ?>
        </select>

        <br><br>
        <button type="submit" name="submit">Masuk</button>
    </form>
</div>

<?php else: ?>

<a href="logout_customer.php">← Kembali</a>

<div class="container">
    <h1>Halaman Customer</h1>
    <p>Meja: <?= $_SESSION['table_id'] ?></p>

<form method="POST" action="confirm_order.php">

<h3>Makanan</h3>
<div class="menu-grid">
<?php
$makanan = $conn->query("SELECT * FROM products WHERE category='makanan'");
while($m = $makanan->fetch_assoc()){
?>
    <div class="card">
        <h4><?= $m['name'] ?></h4>
        <img src="<?= $m['image'] ?>" alt="<?= $m['name'] ?>" style="width:100%;height:150px;object-fit:cover;border-radius:10px;">
        <p>Rp <?= number_format($m['price']) ?></p>
        <input type="number" name="qty[<?= $m['id'] ?>]" min="0" value="0">
    </div>
<?php } ?>
</div>

<h3>Minuman</h3>
<div class="menu-grid">
<?php
$minuman = $conn->query("SELECT * FROM products WHERE category='minuman'");
while($d = $minuman->fetch_assoc()){
?>
    <div class="card">
        <h4><?= $d['name'] ?></h4>
        <img src="<?= $d['image'] ?>" alt="<?= $d['name'] ?>" style="width:100%;height:150px;object-fit:cover;border-radius:10px;">
        <p>Rp <?= number_format($d['price']) ?></p>
        <input type="number" name="qty[<?= $d['id'] ?>]" min="0" value="0">
    </div>
<?php } ?>
</div>

<br>
<button type="submit">Lanjut Pesan</button>

</form>
</div>

<button onclick="alert('Pelayan dipanggil!')" style="position:fixed;bottom:20px;left:20px;background:red;color:white;padding:15px;border:none;border-radius:10px;">
    Panggil Pelayan
</button>

<?php endif; ?>

</body>
</html>
