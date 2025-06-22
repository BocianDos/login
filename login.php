<?php
require_once 'db_config.php';
// Jeśli użytkownik jest już zalogowany, przekieruj go na stronę z notatkami
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Logowanie</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Zaloguj się</h2>
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="message error">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']);
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="message success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']);
        }
        ?>
        <form action="login_process.php" method="post">
            <input type="text" name="login" placeholder="Login" required>
            <input type="password" name="password" placeholder="Hasło" required>
            <button type="submit">Login</button>
        </form>
         <p style="margin-top: 20px;">Nie masz konta? <a href="register.php">Stwórz je tutaj</a>.</p>
    </div>
</body>
</html>