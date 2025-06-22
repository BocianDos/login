<?php
/**
 * Plik: create_note.php
 * Cel: Obsługa tworzenia nowej notatki dla zalogowanego użytkownika.
 */

// Dołączenie pliku konfiguracyjnego bazy danych i uruchomienie sesji
require_once 'db_config.php';

// KROK 1: Zabezpieczenie - Sprawdzenie, czy użytkownik jest zalogowany.
// Jeśli nikt nie jest zalogowany, skrypt przerywa działanie i przekierowuje do strony logowania.
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// KROK 2: Sprawdzenie, czy dane zostały wysłane z formularza (metodą POST).
// Kod wewnątrz tego bloku wykona się tylko po kliknięciu przycisku "Dodaj notatkę".
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Pobranie treści notatki z wysłanego formularza.
    // Funkcja trim() usuwa białe znaki (spacje, tabulatory) z początku i końca tekstu.
    $note_content = trim($_POST['note_content']);
    
    // Pobranie ID zalogowanego użytkownika. Zostało ono zapisane w sesji podczas logowania.
    $user_id = $_SESSION['user_id'];

    // KROK 3: Walidacja - Sprawdzenie, czy treść notatki nie jest pusta.
    // Nie chcemy zapisywać pustych notatek w bazie danych.
    if (!empty($note_content)) {
        
        // KROK 4: Przygotowanie zapytania do bazy danych w celu wstawienia nowej notatki.
        // Znak zapytania (?) to placeholder, który zostanie później bezpiecznie zastąpiony danymi.
        $sql = "INSERT INTO notes (user_id, content) VALUES (?, ?)";
        
        // Użycie "prepared statements" to najlepsza ochrona przed atakami SQL Injection.
        if ($stmt = $conn->prepare($sql)) {
            // Powiązanie zmiennych PHP z placeholderami w zapytaniu SQL.
            // "is" oznacza, że pierwszy parametr ($user_id) to Integer (liczba), a drugi ($note_content) to String (tekst).
            $stmt->bind_param("is", $user_id, $note_content);
            
            // Wykonanie zapytania z przygotowanymi danymi.
            $stmt->execute();
            
            // Zamknięcie obiektu zapytania, aby zwolnić zasoby.
            $stmt->close();
        }
    }
}

// KROK 5: Przekierowanie z powrotem do panelu głównego.
// Niezależnie od tego, czy notatka została dodana, czy nie (np. była pusta),
// użytkownik wraca do dashboard.php, który odświeży listę notatek.
header("location: dashboard.php");
exit;
?>