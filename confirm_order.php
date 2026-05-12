<?php
session_start();
include "config.php";
 // Harus login dulu
if(!isset($_SESSION['customer_id'])){
header("Location: customer.php");
exit;
}
 if(!isset($_POST['qty']) || !is_array($_POST['qty'])){
header("Location: customer.php");
exit;
}
 $qtys = $_POST['qty'];
$total = 0;
$items = []; // simpan data item untuk ditampilkan
 foreach($qtys as $id => $qty){
$id = (int)$id;
$qty = (int)$qty;
if($qty <= 0 || $id <= 0) continue;
  // Gunakan prepared statement — aman dari SQL injection
$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
  if(!$row) continue;
  $subtotal = $row['price'] * $qty;
$total += $subtotal;
  $items[] = [
'id' => $id,
'name' => $row['name'],
'price' => $row['price'],
'qty' => $qty,
'subtotal' => $subtotal,
];}
 if(empty($items)){
header("Location: customer.php");
exit;
}
 // Ambil nomor meja untuk ditampilkan
$tbl = $conn->prepare("SELECT table_number FROM tables WHERE id=?");
$tbl->bind_param("i", $_SESSION['table_id']);
$tbl->execute();
$tbl_row = $tbl->get_result()->fetch_assoc();
$table_number = $tbl_row['table_number'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konfirmasi Pesanan - Warung OS</title>
<style>
* { box-sizing: border-box; }
body {
background: #ddb999;
font-family: 'Segoe UI', monospace;
display: flex;
justify-content: center;
align-items: flex-start;
min-height: 100vh;
margin: 0;
padding: 30px 15px;
}
.confirm-box {
background: #f8f1eb;
width: 100%;
max-width: 550px;
padding: 35px;
border-radius: 20px;
box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.back-link {
display: inline-block;margin-bottom: 20px;
text-decoration: none;
color: #4e2d1f;
font-weight: bold;
}
.back-link:hover { text-decoration: underline; }
h1 {
text-align: center;
color: #4e2d1f;
margin-bottom: 8px;
}
.meja-info {
text-align: center;
color: #7a3300;
font-size: 14px;
margin-bottom: 25px;
}
.item {
display: flex;
justify-content: space-between;
align-items: center;
padding: 14px 0;
border-bottom: 1px solid #e8d5c0;
color: #4e2d1f;
font-size: 16px;
}
.item-name { font-weight: bold; }
.item-qty { font-size: 13px; color: #7a5c3f; margin-top: 3px; }
.item-price { font-weight: bold; white-space: nowrap; }
.total {
text-align: right;
margin-top: 20px;
font-size: 26px;
font-weight: bold;
color: #4e2d1f;
border-top: 2px solid #c8a87a;
padding-top: 15px;
}
.btn-konfirm {
width: 100%;
margin-top: 25px;
padding: 16px;border: none;
border-radius: 12px;
background: #a34700;
color: white;
font-size: 18px;
cursor: pointer;
transition: 0.3s;
}
.btn-konfirm:hover { background: #7a3300; transform: scale(1.02); }
</style>
</head>
<body>
<div class="confirm-box">
  <a href="customer.php" class="back-link">← Kembali ke Menu</a>
  <h1>🛒 Konfirmasi Pesanan</h1>
<p class="meja-info">Meja <?= htmlspecialchars($table_number) ?></p>
  <form method="POST" action="save_order.php">
<?php foreach($items as $item): ?>
<div class="item">
<div>
<div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
<div class="item-qty"><?= $item['qty'] ?> × Rp <?=
number_format($item['price']) ?></div>
</div>
<div class="item-price">Rp <?= number_format($item['subtotal']) ?></div>
<input type="hidden" name="menu[<?= $item['id'] ?>]" value="<?=
$item['qty'] ?>">
</div>
<?php endforeach; ?>
  <div class="total">Total: Rp <?= number_format($total) ?></div>
  <button type="submit" class="btn-konfirm">✅ Konfirmasi Pesanan</button>
</form>
 </div>
</body>
</html><?php
session_start();
include "config.php";
 // Harus login dulu
if(!isset($_SESSION['customer_id'])){
header("Location: customer.php");
exit;
}
 if(!isset($_POST['qty']) || !is_array($_POST['qty'])){
header("Location: customer.php");
exit;
}
 $qtys = $_POST['qty'];
$total = 0;
$items = []; // simpan data item untuk ditampilkan
 foreach($qtys as $id => $qty){
$id = (int)$id;
$qty = (int)$qty;
if($qty <= 0 || $id <= 0) continue;
  // Gunakan prepared statement — aman dari SQL injection
$stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
  if(!$row) continue;
  $subtotal = $row['price'] * $qty;
$total += $subtotal;
  $items[] = [
'id' => $id,
'name' => $row['name'],
'price' => $row['price'],
'qty' => $qty,
'subtotal' => $subtotal,
];}
 if(empty($items)){
header("Location: customer.php");
exit;
}
 // Ambil nomor meja untuk ditampilkan
$tbl = $conn->prepare("SELECT table_number FROM tables WHERE id=?");
$tbl->bind_param("i", $_SESSION['table_id']);
$tbl->execute();
$tbl_row = $tbl->get_result()->fetch_assoc();
$table_number = $tbl_row['table_number'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Konfirmasi Pesanan - Warung OS</title>
<style>
* { box-sizing: border-box; }
body {
background: #ddb999;
font-family: 'Segoe UI', monospace;
display: flex;
justify-content: center;
align-items: flex-start;
min-height: 100vh;
margin: 0;
padding: 30px 15px;
}
.confirm-box {
background: #f8f1eb;
width: 100%;
max-width: 550px;
padding: 35px;
border-radius: 20px;
box-shadow: 0 10px 25px rgba(0,0,0,0.2);
}
.back-link {
display: inline-block;margin-bottom: 20px;
text-decoration: none;
color: #4e2d1f;
font-weight: bold;
}
.back-link:hover { text-decoration: underline; }
h1 {
text-align: center;
color: #4e2d1f;
margin-bottom: 8px;
}
.meja-info {
text-align: center;
color: #7a3300;
font-size: 14px;
margin-bottom: 25px;
}
.item {
display: flex;
justify-content: space-between;
align-items: center;
padding: 14px 0;
border-bottom: 1px solid #e8d5c0;
color: #4e2d1f;
font-size: 16px;
}
.item-name { font-weight: bold; }
.item-qty { font-size: 13px; color: #7a5c3f; margin-top: 3px; }
.item-price { font-weight: bold; white-space: nowrap; }
.total {
text-align: right;
margin-top: 20px;
font-size: 26px;
font-weight: bold;
color: #4e2d1f;
border-top: 2px solid #c8a87a;
padding-top: 15px;
}
.btn-konfirm {
width: 100%;
margin-top: 25px;
padding: 16px;border: none;
border-radius: 12px;
background: #a34700;
color: white;
font-size: 18px;
cursor: pointer;
transition: 0.3s;
}
.btn-konfirm:hover { background: #7a3300; transform: scale(1.02); }
</style>
</head>
<body>
<div class="confirm-box">
  <a href="customer.php" class="back-link">← Kembali ke Menu</a>
  <h1>🛒 Konfirmasi Pesanan</h1>
<p class="meja-info">Meja <?= htmlspecialchars($table_number) ?></p>
  <form method="POST" action="save_order.php">
<?php foreach($items as $item): ?>
<div class="item">
<div>
<div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
<div class="item-qty"><?= $item['qty'] ?> × Rp <?=
number_format($item['price']) ?></div>
</div>
<div class="item-price">Rp <?= number_format($item['subtotal']) ?></div>
<input type="hidden" name="menu[<?= $item['id'] ?>]" value="<?=
$item['qty'] ?>">
</div>
<?php endforeach; ?>
  <div class="total">Total: Rp <?= number_format($total) ?></div>
  <button type="submit" class="btn-konfirm">✅ Konfirmasi Pesanan</button>
</form>
 </div>
</body>
</html><?php
session_start();
include "config.php";

if(!isset($_POST['qty'])){
    echo "Tidak ada pesanan!";
    exit;
}

$qtys = $_POST['qty'];
$total = 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Konfirmasi Pesanan</title>

    <style>

    body{
        background: #ddb999;
        font-family: monospace;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        margin: 0;
    }

    .confirm-box{
        background: #f8f1eb;
        width: 550px;
        padding: 35px;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }

    h1{
        text-align: center;
        color: #4e2d1f;
        margin-bottom: 30px;
    }

    .item{
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #ddd;
        color: #4e2d1f;
        font-size: 18px;
    }

    .total{
        text-align: right;
        margin-top: 25px;
        font-size: 30px;
        font-weight: bold;
        color: #4e2d1f;
    }

    .btn{
        width: 100%;
        margin-top: 30px;
        padding: 16px;
        border: none;
        border-radius: 12px;
        background: #a34700;
        color: white;
        font-size: 18px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn:hover{
        background: #7a3300;
        transform: scale(1.02);
    }

    .back{
        display: inline-block;
        margin-bottom: 20px;
        text-decoration: none;
        color: #4e2d1f;
        font-weight: bold;
    }

    </style>
</head>
<body>

<div class="confirm-box">

<a href="customer.php" class="back">
← Kembali
</a>

<h1>Konfirmasi Pesanan</h1>

<form method="POST" action="save_order.php">

<?php

foreach($qtys as $id => $qty){

    if($qty > 0){

        $result = $conn->query("SELECT * FROM products WHERE id=$id");
        $row = $result->fetch_assoc();

        $subtotal = $row['price'] * $qty;
        $total += $subtotal;

?>

<div class="item">

<div>
    <b><?= $row['name'] ?></b><br>
    <?= $qty ?> x Rp <?= number_format($row['price']) ?>
</div>

<div>
    Rp <?= number_format($subtotal) ?>
</div>

</div>

<input type="hidden" name="menu[<?= $id ?>]" value="<?= $qty ?>">

<?php
    }
}
?>

<div class="total">
Total: Rp <?= number_format($total) ?>
</div>

<button type="submit" class="btn">
    Konfirmasi Pesanan
</button>

</form>

</div>

</body>
</html>
