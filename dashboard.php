<?php
require_once 'db_config.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$notes = [];
$sql = "SELECT id, content, updated_at FROM notes WHERE user_id = ? ORDER BY updated_at DESC";
if($stmt = $conn->prepare($sql)){
    $stmt->bind_param("i", $user_id);
    if($stmt->execute()){
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $notes[] = $row;
        }
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Twoje Notatki</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .note-list { list-style: none; padding: 0; }
        .note-item { background: #f9f9f9; border: 1px solid #eee; padding: 15px; margin-bottom: 10px; border-radius: 5px; text-align: left; }
        .note-content { margin-bottom: 10px; white-space: pre-wrap; }
        .note-meta { font-size: 0.8em; color: #777; }
        .note-actions { margin-top: 10px; }
        .note-actions a { margin-right: 10px; text-decoration: none; }
        .note-actions a.edit { color: #3498db; }
        .note-actions a.delete { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="container" style="max-width: 800px;">
        <h1>Witaj, <?php echo htmlspecialchars($_SESSION["user_login"]); ?>!</h1>
        
        <div style="text-align: left; margin-bottom: 30px;">
            <h2>Stwórz nową notatkę</h2>
            <form action="create_note.php" method="post">
                <textarea name="note_content" placeholder="Wpisz treść nowej notatki..." required></textarea>
                <button type="submit">Dodaj notatkę</button>
            </form>
        </div>

        <h2>Twoje istniejące notatki</h2>
        <?php if (empty($notes)): ?>
            <p>Nie masz jeszcze żadnych notatek. Stwórz pierwszą!</p>
        <?php else: ?>
            <ul class="note-list">
                <?php foreach ($notes as $note): ?>
                    <li class="note-item">
                        <div class="note-content"><?php echo nl2br(htmlspecialchars($note['content'])); ?></div>
                        <div class="note-meta">Ostatnia aktualizacja: <?php echo date("d.m.Y, H:i", strtotime($note['updated_at'])); ?></div>
                        <div class="note-actions">
                            <a href="edit_note.php?id=<?php echo $note['id']; ?>" class="edit">Edytuj</a>
                            <a href="delete_note.php?id=<?php echo $note['id']; ?>" class="delete" onclick="return confirm('Czy na pewno chcesz usunąć tę notatkę?');">Usuń</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <p style="margin-top: 30px;"><a href="logout.php">Wyloguj się</a></p>
    </div>
</body>
</html>
