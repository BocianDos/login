<?php
require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    if (empty($login) || empty($password)) {
        $_SESSION['error_message'] = "Wprowadź login i hasło.";
        header("location: login.php");
        exit;
    }

    $sql = "SELECT id, login, password_hash FROM users WHERE login = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id, $login, $hashed_password);
            if ($stmt->fetch()) {
                if (password_verify($password, $hashed_password)) {
                    // Hasło poprawne, start sesji
                    $_SESSION["loggedin"] = true;
                    $_SESSION["user_id"] = $id;
                    $_SESSION["user_login"] = $login;

                    echo "<!DOCTYPE html><html><head><link rel='stylesheet' href='style.css'></head><body><div class='container'><div class='message success'>Zalogowano pomyślnie!</div></div><script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 1500);</script></body></html>";

                } else {
                    $_SESSION['error_message'] = "Nieprawidłowe hasło.";
                    header("location: login.php");
                }
            }
        } else {
            $_SESSION['error_message'] = "Nie znaleziono konta o tym loginie.";
            header("location: login.php");
        }
        $stmt->close();
    }
    $conn->close();
}
?>
