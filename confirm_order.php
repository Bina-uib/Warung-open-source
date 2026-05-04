<?php
session_start();
include "config.php";

if(!isset($_POST['qty'])){
    echo "Tidak ada pesanan!";
    exit;
}

$qtys = $_POST['qty'];
$total = 0;
?>

<h2>Konfirmasi Pesanan</h2>

<form method="POST" action="save_order.php">

<?php
foreach($qtys as $id => $qty){
    if($qty > 0){
        $result = $conn->query("SELECT * FROM products WHERE id=$id");
        $row = $result->fetch_assoc();

        $subtotal = $row['price'] * $qty;
        $total += $subtotal;

        echo "
        <div>
            {$row['name']} x $qty = Rp $subtotal
            <input type='hidden' name='menu[$id]' value='$qty'>
        </div>
        ";
    }
}
?>

<h3>Total: Rp <?= number_format($total) ?></h3>

<button type="submit">Konfirmasi Pesanan</button>

</form>