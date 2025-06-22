<?php
// Inicjalizacja sesji
session_start();

// Usunięcie wszystkich zmiennych sesji
$_SESSION = array();

// Zniszczenie sesji
session_destroy();

// Przekierowanie na stronę główną
header("location: index.php");
exit;
?>