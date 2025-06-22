<?php

require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $note_content = trim($_POST['note_content']);

    $user_id = $_SESSION['user_id'];

    if (!empty($note_content)) {

        $sql = "INSERT INTO notes (user_id, content) VALUES (?, ?)";

        if ($stmt = $conn->prepare($sql)) {

            $stmt->bind_param("is", $user_id, $note_content);

            $stmt->execute();

            $stmt->close();
        }
    }
}

header("location: dashboard.php");
exit;
?>
