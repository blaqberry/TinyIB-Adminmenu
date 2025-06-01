<?php
session_start();

// Change these credentials as needed
$admin_user = 'CHANGEME';
$admin_pass = 'CHANGEME';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['logged_in'] = true;
        header("Location: select.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
?>

<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
    <h2>Admin Login</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="post">
        Username: <input type="text" name="username" /><br><br>
        Password: <input type="password" name="password" /><br><br>
        <input type="submit" value="Login" />
    </form>
</body>
</html>
