<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $_SESSION['error_message'] = "Login i hasło są wymagane.";
        header("location: register.php");
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (login, password_hash) VALUES (?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $login, $password_hash);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Konto zostało pomyślnie utworzone! Możesz się teraz zalogować.";
            header("location: login.php");
        } else {
            $_SESSION['error_message'] = "Ten login jest już zajęty. Spróbuj innego.";
            header("location: register.php");
        }
        $stmt->close();
    }
    $conn->close();
}
?>
