<?php
session_start();

if(isset($_SESSION['admin'])){
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Admin</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Login Admin</h1>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" class="btn">Login</button>
    </form>

    <br>
    <a href="index.php">Kembali</a>
</div>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    if($user === "admin123" && $pass === "admin22"){
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        echo "<p style='color:red;'>Username / Password salah</p>";
    }
}
?>

</body>
</html>