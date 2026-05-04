<?php
session_start();

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Halaman Admin</h1>

    <div id="notif" style="display:none; background:red; padding:15px; border-radius:8px;">
        ⚠️ Ada pelanggan memanggil pelayan!
    </div>

    <br>
    <button onclick="clearNotif()">Clear Notif</button>

    <br><br>
    <a href="index.php">Kembali</a>
</div>

<script>
function checkNotif(){
    let call = localStorage.getItem("call_waiter");

    if(call === "1"){
        document.getElementById("notif").style.display = "block";
    }
}

// hapus notif
function clearNotif(){
    localStorage.removeItem("call_waiter");
    location.reload();
}

// cek tiap 1 detik
setInterval(checkNotif, 1000);
</script>
<a href="logout.php" class="back-btn">Logout</a>
</body>
</html>