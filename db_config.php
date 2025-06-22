<?php
// Konfiguracja połączenia z bazą danych
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'user'); // <-- Zmień na swoją nazwę użytkownika
define('DB_PASSWORD', '1qazXSW@'); // <-- Zmień na swoje hasło
define('DB_NAME', 'aplikacja_db'); // <-- Zmień na nazwę swojej bazy danych

// Utworzenie połączenia
$conn = new mysqli('localhost', 'root', '', 'aplikacja_db');


// Sprawdzenie połączenia
if ($conn->connect_error) {
    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
}

// Ustawienie kodowania znaków
$conn->set_charset("utf8mb4");

// Uruchomienie sesji na początku, aby była dostępna w całej aplikacji
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>