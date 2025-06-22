<?php
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $note_content = trim($_POST['note_content']);
    $note_id = $_POST['note_id'];
    $user_id = $_SESSION['user_id'];

    if (!empty($note_content) && !empty($note_id)) {
        $sql = "UPDATE notes SET content = ? WHERE id = ? AND user_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sii", $note_content, $note_id, $user_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header("location: dashboard.php");
exit;
?>
