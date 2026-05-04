<?php
session_start();
include "config.php";

$customer_id = $_SESSION['customer_id'];
$table_id = $_SESSION['table_id'];
$menus = $_POST['menu'];

// buat order
$conn->query("INSERT INTO orders (table_id) VALUES ($table_id)");
$order_id = $conn->insert_id;

// simpan item + qty
foreach($menus as $id => $qty){
    $conn->query("INSERT INTO order_items (order_id, product_id, qty) VALUES ($order_id, $id, $qty)");
}

echo "<script>alert('Pesanan berhasil!'); window.location='customer.php';</script>";