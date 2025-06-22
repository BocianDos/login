<?php
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $note_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $note_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("location: dashboard.php");
exit;
?>
