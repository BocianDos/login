<?php
require_once 'db_config.php';

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Sprawdzenie czy ID notatki jest przekazane
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $note_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Usunięcie notatki z weryfikacją, czy należy do zalogowanego użytkownika
    $sql = "DELETE FROM notes WHERE id = ? AND user_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $note_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Przekieruj z powrotem do panelu
header("location: dashboard.php");
exit;
?>