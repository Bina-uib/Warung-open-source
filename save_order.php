<?php
session_start();
include "config.php";
 // Wajib login sebagai customer
if(!isset($_SESSION['customer_id'])){
header("Location: customer.php");
exit;
}
 if(!isset($_POST['menu']) || !is_array($_POST['menu'])){
header("Location: customer.php");
exit;
}
 $customer_id = (int)$_SESSION['customer_id'];
$table_id = (int)$_SESSION['table_id'];
$menus = $_POST['menu'];
$total = 0;
$order_items = [];
 /* =========================
VALIDASI & HITUNG TOTAL
========================= */
foreach($menus as $product_id => $qty){
$product_id = (int)$product_id;
$qty = (int)$qty;
  if($product_id <= 0 || $qty <= 0) continue;
  $stmt = $conn->prepare("SELECT id, name, price FROM products WHERE id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
  if(!$product) continue;
  $subtotal = $product['price'] * $qty;
$total += $subtotal;
  $order_items[] = [
'product_id' => $product_id,'qty' => $qty,
'price' => $subtotal,
'name' => $product['name'],
];
}
 if(empty($order_items)){
header("Location: customer.php");
exit;
}
 /* =========================
SIMPAN KE ORDERS
========================= */
$stmt = $conn->prepare("
INSERT INTO orders (customer_id, table_id, total, status)
VALUES (?, ?, ?, 'pending')
");
$stmt->bind_param("iii", $customer_id, $table_id, $total);
$stmt->execute();
$order_id = $stmt->insert_id;
 /* =========================
SIMPAN ORDER ITEMS
========================= */
foreach($order_items as $item){
$stmt2 = $conn->prepare("
INSERT INTO order_items (order_id, product_id, qty, price)
VALUES (?, ?, ?, ?)
");
$stmt2->bind_param(
"iiii",
$order_id,
$item['product_id'],
$item['qty'],
$item['price']
);
$stmt2->execute();
}
?>
<!DOCTYPE html>
<html lang="id"><head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Berhasil - Warung OS</title>
<style>
* { box-sizing: border-box; }
body {
background: #ddb999;
font-family: 'Segoe UI', monospace;
display: flex;
justify-content: center;
align-items: center;
min-height: 100vh;
margin: 0;
padding: 20px;
}
.box {
background: #fff;
padding: 45px 40px;
border-radius: 20px;
text-align: center;
box-shadow: 0 10px 30px rgba(0,0,0,0.2);
max-width: 420px;
width: 100%;
}
.checkmark {
font-size: 64px;
margin-bottom: 10px;
}
h1 { color: #4e2d1f; margin-bottom: 8px; font-size: 26px; }
.order-id { color: #888; font-size: 14px; margin-bottom: 20px; }
.summary {
background: #f8f1eb;
border-radius: 12px;
padding: 15px 20px;
margin-bottom: 20px;
text-align: left;
}
.summary-row {
display: flex;
justify-content: space-between;
padding: 6px 0;border-bottom: 1px solid #ecdcc8;
font-size: 14px;
color: #4e2d1f;
}
.summary-row:last-child { border: none; font-weight: bold; font-size: 16px; }
p { color: #555; font-size: 14px; margin-bottom: 25px; }
a {
display: inline-block;
padding: 13px 30px;
background: #a34700;
color: white;
text-decoration: none;
border-radius: 12px;
font-size: 16px;
transition: 0.3s;
}
a:hover { background: #7a3300; }
</style>
</head>
<body>
<div class="box">
<div class="checkmark">✅</div>
<h1>Pesanan Berhasil!</h1>
<p class="order-id">Order #<?= $order_id ?></p>
  <div class="summary">
<?php foreach($order_items as $item): ?>
<div class="summary-row">
<span><?= htmlspecialchars($item['name']) ?> ×<?= $item['qty'] ?></span>
<span>Rp <?= number_format($item['price']) ?></span>
</div>
<?php endforeach; ?>
<div class="summary-row">
<span>Total</span>
<span>Rp <?= number_format($total) ?></span>
</div>
</div>
  <p>Pesanan Anda sedang diproses oleh dapur. Terima kasih! 🙏</p>
  <a href="customer.php">← Kembali ke Menu</a>
</div></body>
</html>
