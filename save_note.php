<?php
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_content = $_POST['note'];
    $user_id = $_SESSION['user_id'];

    $sql_check = "SELECT id FROM notes WHERE user_id = ?";
    if ($stmt_check = $conn->prepare($sql_check)) {
        $stmt_check->bind_param("i", $user_id);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows == 1) {
            $sql = "UPDATE notes SET content = ? WHERE user_id = ?";
            if ($stmt_update = $conn->prepare($sql)) {
                $stmt_update->bind_param("si", $note_content, $user_id);
                $stmt_update->execute();
                $stmt_update->close();
            }
        } else {
            $sql = "INSERT INTO notes (user_id, content) VALUES (?, ?)";
            if ($stmt_insert = $conn->prepare($sql)) {
                $stmt_insert->bind_param("is", $user_id, $note_content);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
        }
        $stmt_check->close();
    }

    header("location: dashboard.php");
    exit;
}
?>
