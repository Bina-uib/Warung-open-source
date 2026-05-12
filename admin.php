<?php
session_start();
include "config.php";
 if(!isset($_SESSION['admin'])){
header("Location: admin_login.php");
exit;
}
 // Handle clear notif
if(isset($_POST['clear_notif'])){
$conn->query("DELETE FROM waiter_calls WHERE is_read = 0");
header("Location: admin.php");
exit;
}
 // Handle update status pesanan
if(isset($_POST['update_status'])){
$order_id = (int)$_POST['order_id'];
$status = $_POST['status'];
$allowed = ['pending','proses','selesai'];
if(in_array($status, $allowed)){
$stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();
}
header("Location: admin.php");
exit;
}
 // Ambil panggilan pelayan yang belum dibaca
$calls = $conn->query("
SELECT wc.*, t.table_number 
FROM waiter_calls wc 
JOIN tables t ON wc.table_id = t.id 
WHERE wc.is_read = 0 
ORDER BY wc.created_at DESC
");
$call_count = $calls->num_rows;
 // Ambil semua pesanan
$orders = $conn->query("SELECT o.id, o.total, o.status, o.created_at,
c.phone, t.table_number
FROM orders o
JOIN customers c ON o.customer_id = c.id
JOIN tables t ON c.table_id = t.id
ORDER BY o.created_at DESC
LIMIT 50
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin - Warung OS</title>
<link rel="stylesheet" href="style.css">
<style>
body {
display: block;
padding: 20px;
min-height: 100vh;
}
.admin-header {
display: flex;
justify-content: space-between;
align-items: center;
background: #fff8f0;
padding: 15px 25px;
border-radius: 15px;
margin-bottom: 25px;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.admin-header h1 { margin: 0; font-size: 24px; color: #4e2d1f; }
.notif-bar {
background: #ff4444;
color: white;
padding: 15px 20px;
border-radius: 12px;
margin-bottom: 20px;
display: flex;
justify-content: space-between;
align-items: center;animation: pulse 1s infinite;
}
@keyframes pulse {
0%,100% { opacity: 1; }
50% { opacity: 0.8; }
}
.notif-bar button {
background: white;
color: #ff4444;
border: none;
padding: 8px 16px;
border-radius: 8px;
cursor: pointer;
font-weight: bold;
}
.section-title {
font-size: 20px;
font-weight: bold;
color: #4e2d1f;
margin: 20px 0 12px;
padding-left: 5px;
border-left: 4px solid #a34700;
}
table {
width: 100%;
border-collapse: collapse;
background: #fff8f0;
border-radius: 12px;
overflow: hidden;
box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
thead { background: #a34700; color: white; }
th, td {
padding: 12px 16px;
text-align: left;
border-bottom: 1px solid #f0dcc8;
font-size: 14px;
}
tr:last-child td { border-bottom: none; }
tr:hover td { background: #fff0e0; }
.badge {
padding: 4px 10px;border-radius: 20px;
font-size: 12px;
font-weight: bold;
}
.badge-pending { background: #fff3cd; color: #856404; }
.badge-proses { background: #cce5ff; color: #004085; }
.badge-selesai { background: #d4edda; color: #155724; }
select.status-select {
padding: 5px 10px;
border-radius: 8px;
border: 1px solid #ddd;
font-size: 13px;
cursor: pointer;
}
.btn-update {
padding: 6px 14px;
background: #a34700;
color: white;
border: none;
border-radius: 8px;
cursor: pointer;
font-size: 13px;
}
.btn-update:hover { background: #7a3300; }
.btn-logout {
padding: 8px 18px;
background: #c0392b;
color: white;
border: none;
border-radius: 8px;
cursor: pointer;
font-size: 14px;
text-decoration: none;
}
.btn-logout:hover { background: #922b21; }
.empty-msg {
text-align: center;
padding: 30px;
color: #999;
background: #fff8f0;
border-radius: 12px;
}.refresh-info {
font-size: 12px;
color: #999;
text-align: right;
margin-top: 8px;
}
</style>
</head>
<body>
 <div class="admin-header">
<h1>🍜 Halaman Admin</h1>
<div>
<span style="color:#999;font-size:13px;margin-right:15px;">
Login sebagai: <b><?= htmlspecialchars($_SESSION['admin_user'] ?? 'admin')
?></b>
</span>
<a href="logout.php" class="btn-logout">Logout</a>
</div>
</div>
 <?php if($call_count > 0): ?>
<form method="POST">
<div class="notif-bar">
<span>
🔔 <b><?= $call_count ?> meja</b> memanggil pelayan!
<?php
$calls->data_seek(0);
$tables_calling = [];
while($c = $calls->fetch_assoc()){
$tables_calling[] = "Meja " . $c['table_number'];
}
echo " (" . implode(", ", $tables_calling) . ")";
?>
</span>
<button type="submit" name="clear_notif">✓ Tandai Selesai</button>
</div>
</form>
<?php endif; ?>
 <div class="section-title">📋 Daftar Pesanan</div>
<?php if($orders->num_rows === 0): ?>
<div class="empty-msg">Belum ada pesanan masuk.</div>
<?php else: ?>
<table>
<thead>
<tr>
<th>#</th>
<th>Waktu</th>
<th>No Meja</th>
<th>No HP</th>
<th>Total</th>
<th>Status</th>
<th>Ubah Status</th>
</tr>
</thead>
<tbody>
<?php while($o = $orders->fetch_assoc()): ?>
<tr>
<td><?= $o['id'] ?></td>
<td><?= date('d/m H:i', strtotime($o['created_at'])) ?></td>
<td>Meja <?= htmlspecialchars($o['table_number']) ?></td>
<td><?= htmlspecialchars($o['phone']) ?></td>
<td>Rp <?= number_format($o['total']) ?></td>
<td>
<span class="badge badge-<?= $o['status'] ?>">
<?= ucfirst($o['status']) ?>
</span>
</td>
<td>
<form method="POST" style="display:flex;gap:6px;">
<input type="hidden" name="order_id" value="<?= $o['id'] ?>">
<select name="status" class="status-select">
<option value="pending" <?= $o['status']==='pending' ?'selected':'' 
>>Pending</option>
<option value="proses" <?= $o['status']==='proses' ?'selected':'' 
>>Proses</option>
<option value="selesai" <?= $o['status']==='selesai' ?'selected':'' 
>>Selesai</option>
</select>
<button type="submit" name="update_status" class="btn
update">Simpan</button>
</form></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<p class="refresh-info">Auto-refresh setiap 10 detik</p>
<?php endif; ?>
 <script>
// Auto refresh halaman tiap 10 detik untuk cek notif & pesanan baru
setTimeout(function(){ location.reload(); }, 10000);
</script>
 </body>
</html>
