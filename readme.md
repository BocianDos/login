# Kompleksowa Dokumentacja Projektu: Aplikacja do Notatek

## 1. Wprowadzenie

**1.1. Cel i Zakres Projektu**
Projekt "Aplikacja do Notatek" to system internetowy zrealizowany w technologii PHP,
którego głównym celem jest zapewnienie użytkownikom platformy do bezpiecznego
tworzenia, edytowania i zarządzania osobistymi notatkami. Aplikacja została
zaprojektowana w taki sposób, aby każdy użytkownik posiadał prywatną, odizolowaną
przestrzeń roboczą, chronioną przez indywidualny system uwierzytelniania.
**1.2. Stos Technologiczny**
● **Logika Aplikacji (Backend):** PHP w wersji 7.x lub nowszej.
● **Baza Danych:** System zarządzania bazą danych MySQL (lub kompatybilny, np.
MariaDB).
● **Struktura i Prezentacja (Frontend):** HTML5 i CSS3.
● **Środowisko Serwerowe:** Serwer WWW z obsługą PHP (np. Apache, Nginx).

## 2. Architektura Aplikacji

System został zbudowany w oparciu o klasyczny, serwerowy model renderowania
stron (Server-Side Rendering). Każda interakcja użytkownika (kliknięcie linku, wysłanie
formularza) inicjuje żądanie HTTP do serwera, które jest przetwarzane przez
odpowiedni skrypt PHP.
**2.1. Cykl Żądanie-Odpowiedź**

1. **Akcja Użytkownika:** Użytkownik wykonuje czynność w przeglądarce (np.
    wypełnia formularz logowania).
2. **Żądanie HTTP:** Przeglądarka wysyła żądanie (POST lub GET) do wskazanego
    skryptu PHP na serwerze.
3. **Przetwarzanie przez PHP:**
    ○ Skrypt jest wykonywany na serwerze.
    ○ Następuje dołączenie konfiguracji bazy danych (db_config.php).
    ○ Walidowane są dane wejściowe.
    ○ Wykonywane są operacje na bazie danych (odczyt, zapis, aktualizacja,
       usunięcie).
    ○ Obsługiwana jest sesja użytkownika w celu weryfikacji tożsamości.
4. **Odpowiedź HTTP:** Skrypt PHP generuje kompletną stronę HTML, która jest
    odsyłana do przeglądarki użytkownika jako odpowiedź.
5. **Renderowanie:** Przeglądarka renderuje otrzymany kod HTML, wyświetlając wynik


operacji.
**2.2. Zarządzanie Stanem Aplikacji**
Stan uwierzytelnienia użytkownika jest zarządzany za pomocą **sesji PHP**. Po
pomyślnym zalogowaniu, w superglobalnej tablicy $_SESSION zapisywane są
kluczowe informacje, takie jak ID i login użytkownika. Identyfikator sesji jest
przechowywany w przeglądarce (zazwyczaj jako plik cookie), co pozwala na
identyfikację użytkownika przy kolejnych żądaniach.

## 3. Struktura i Analiza Bazy Danych

Aplikacja opiera się na relacyjnej bazie danych, składającej się z dwóch głównych
tabel, które logicznie oddzielają dane użytkowników od treści notatek.
**Schemat SQL (wnioskowany):**
CREATE DATABASE IF NOT EXISTS aplikacja_db;
USE aplikacja_db;
CREATE TABLE `users` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`login` VARCHAR(255) NOT NULL UNIQUE,
`password_hash` VARCHAR(255) NOT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE `notes` (
`id` INT AUTO_INCREMENT PRIMARY KEY,
`user_id` INT NOT NULL,
`content` TEXT NOT NULL,
`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE
CURRENT_TIMESTAMP,
FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
**Analiza Tabel:**
● **users** :
○ id: Unikalny identyfikator numeryczny użytkownika (klucz główny).
○ login: Unikalna nazwa użytkownika, używana do logowania. Klauzula UNIQUE


```
zapewnia, że nie będzie dwóch kont o tej samej nazwie.
○ password_hash: Przechowuje bezpieczny, jednokierunkowy hash hasła
użytkownika. Nigdy nie przechowuje hasła w formie czystego tekstu.
● notes :
○ id: Unikalny identyfikator notatki (klucz główny).
○ user_id: Klucz obcy, który tworzy relację jeden-do-wielu (jeden użytkownik
może mieć wiele notatek). Zapewnia integralność danych. Klauzula ON
DELETE CASCADE sprawia, że usunięcie użytkownika automatycznie usunie
wszystkie jego notatki.
○ content: Przechowuje treść notatki jako typ TEXT, co pozwala na zapisywanie
długich treści.
○ updated_at: Znacznik czasu, który jest automatycznie aktualizowany przy
każdej modyfikacji rekordu. Jest on używany do sortowania notatek od
najnowszej do najstarszej na stronie głównej panelu.
```
## 4. Szczegółowa Analiza Plików i Logiki

**4.1. Moduł Konfiguracji**
● **db_config.php** : Fundament całej aplikacji. Jest dołączany za pomocą
require_once w niemal każdym skrypcie. Odpowiada za dwie krytyczne operacje:

1. **Nawiązanie połączenia z bazą danych:** Używa obiektu mysqli do połączenia
    z serwerem.
2. **Inicjalizacja sesji:** Wywołanie session_start() pozwala na korzystanie ze
    zmiennych sesyjnych na przestrzeni całej aplikacji.
**4.2. Moduł Uwierzytelniania**
● **Rejestracja (register.php, register_process.php):**
○ register_process.php stosuje kluczową funkcję bezpieczeństwa
password_hash($password, PASSWORD_DEFAULT). PASSWORD_DEFAULT
gwarantuje użycie najsilniejszego, aktualnie zalecanego algorytmu
haszującego (np. bcrypt).
○ Komunikacja z użytkownikiem (np. o zajętym loginie) odbywa się poprzez
zapisywanie komunikatów w $_SESSION i wyświetlanie ich na stronie
formularza.
● **Logowanie (login.php, login_process.php):**
○ Logika w login_process.php pobiera użytkownika na podstawie loginu, a
następnie weryfikuje hasło za pomocą password_verify($password,
$hashed_password). Jest to jedyny bezpieczny sposób na sprawdzenie
poprawności hasła bez jego dekodowania.
○ Po pomyślnej weryfikacji, w sesji ustawiane są kluczowe flagi (loggedin,


user_id, user_login), które będą wykorzystywane w innych częściach aplikacji
do autoryzacji.
○ Niewielki skrypt JS opóźniający przekierowanie poprawia doświadczenie
użytkownika (UX), dając mu czas na przeczytanie komunikatu o sukcesie.
**4.3. Moduł Zarządzania Notatkami (Logika Biznesowa)**
● **Panel Główny (dashboard.php):**
○ Pełni rolę centralnego punktu aplikacji dla zalogowanego użytkownika.
○ Przed wykonaniem jakiejkolwiek logiki, sprawdza, czy użytkownik jest
zalogowany (if (!isset($_SESSION["loggedin"]) ...)). Ten wzorzec jest
powtórzony we wszystkich plikach wymagających autoryzacji.
○ Dane wyjściowe (treść notatek, login użytkownika) są zabezpieczane funkcją
htmlspecialchars(), co stanowi fundamentalną ochronę przed atakami
**Cross-Site Scripting (XSS)**.
○ Funkcja nl2br() jest używana do zachowania formatowania (entera) w treści
notatek.
● **Operacje CRUD (Create, Read, Update, Delete):**
○ **Ochrona przed SQL Injection:** Wszystkie operacje na bazie danych
wykorzystują **zapytania parametryzowane (prepared statements)**. Zamiast
wklejać dane bezpośrednio do zapytania, używane są symbole zastępcze (?),
a dane są bezpiecznie dołączane za pomocą bind_param(). To najważniejszy
mechanizm obronny przed atakami typu SQL Injection.
○ **Izolacja Danych Użytkownika:** W skryptach update_note.php i
delete_note.php klauzula WHERE zawiera warunek AND user_id = ?. Jest to
krytyczne zabezpieczenie, które uniemożliwia złośliwemu użytkownikowi
modyfikację lub usunięcie notatki innego użytkownika poprzez manipulację
parametrem id w adresie URL.

## 5. Analiza Bezpieczeństwa Aplikacji

**5.1. Zaimplementowane Zabezpieczenia (Mocne Strony)**
● **SQL Injection:** Skuteczna ochrona dzięki konsekwentnemu stosowaniu zapytań
parametryzowanych.
● **Cross-Site Scripting (XSS):** Podstawowa ochrona zapewniona przez
htmlspecialchars() przy wyświetlaniu danych pochodzących od użytkownika.
● **Bezpieczeństwo Haseł:** Wzorowe użycie password_hash() i password_verify()
zgodnie z najlepszymi praktykami.
● **Kontrola Dostępu:** Poprawna walidacja sesji i sprawdzanie przynależności
notatek do zalogowanego użytkownika.


**5.2. Potencjalne Zagrożenia i Rekomendacje**
● **Cross-Site Request Forgery (CSRF):** Aplikacja jest podatna na ataki CSRF.
Złośliwa strona mogłaby zmusić przeglądarkę zalogowanego użytkownika do
nieświadomego wykonania akcji (np. usunięcia notatki).
○ **Rekomendacja:** Implementacja tokenów anty-CSRF. Przy generowaniu
formularza, w sesji i w ukrytym polu formularza powinien być umieszczany
unikalny, losowy token. Skrypt przetwarzający formularz musi zweryfikować
zgodność obu tokenów przed wykonaniem operacji.
● **Session Fixation:**
○ **Rekomendacja:** Po pomyślnym zalogowaniu użytkownika, należy
zregenerować identyfikator sesji za pomocą funkcji
session_regenerate_id(true). Zapobiega to sytuacji, w której atakujący
narzuca ofierze swój identyfikator sesji.
● **Obsługa Błędów w Środowisku Produkcyjnym:** Wyświetlanie szczegółowych
błędów połączenia z bazą ($conn->connect_error) jest przydatne w fazie
deweloperskiej, ale w środowisku produkcyjnym może ujawnić wrażliwe
informacje.
○ **Rekomendacja:** Skonfigurowanie PHP tak, aby w środowisku produkcyjnym
błędy były logowane do pliku na serwerze, a użytkownikowi wyświetlany był
jedynie ogólny komunikat o błędzie.

## 6. Podsumowanie i Wnioski

Aplikacja do notatek jest solidnie napisanym, funkcjonalnym projektem, który
poprawnie realizuje wszystkie postawione przed nim cele. Na szczególną pochwałę
zasługuje konsekwentne stosowanie fundamentalnych zasad bezpieczeństwa aplikacji
webowych, takich jak ochrona przed SQL Injection, XSS oraz bezpieczne
przechowywanie haseł.
Kod jest przejrzysty i logicznie podzielony na pliki o określonej odpowiedzialności.
Stanowi to doskonałą bazę do dalszego rozwoju, na przykład poprzez wprowadzenie
bardziej zaawansowanych funkcji (np. udostępnianie notatek) lub modernizację
interfejsu z użyciem technologii takich jak AJAX, aby zapewnić płynniejszą interakcję
bez konieczności przeładowywania całej strony.


