<?php
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("location: dashboard.php");
    exit;
}

$note_id = $_GET['id'];
$user_id = $_SESSION['user_id'];
$note_content = "";

$sql = "SELECT content FROM notes WHERE id = ? AND user_id = ?";
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $note_id, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($content);
        $stmt->fetch();
        $note_content = $content;
    } else {
        header("location: dashboard.php");
        exit;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Edytuj Notatkę</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container" style="max-width: 600px;">
        <h2>Edytuj notatkę</h2>
        <form action="update_note.php" method="post">
            <input type="hidden" name="note_id" value="<?php echo $note_id; ?>">
            <textarea name="note_content" required><?php echo htmlspecialchars($note_content); ?></textarea>
            <button type="submit">Zapisz zmiany</button>
        </form>
        <p style="margin-top: 20px;"><a href="dashboard.php">Anuluj i wróć do panelu</a></p>
    </div>
</body>
</html>
