# Aplikacja PHP z bazą danych

Prosta aplikacja PHP do zarządzania użytkownikami z połączeniem do bazy danych MySQL.

## Funkcjonalności

- Dodawanie nowych użytkowników
- Wyświetlanie listy użytkowników
- Edycja danych użytkowników
- Usuwanie użytkowników
- Responsywny interfejs użytkownika

## Wymagania

- XAMPP (Apache, MySQL, PHP)
- PHP 7.0+
- MySQL 5.6+

## Instalacja

1. Skopiuj pliki do katalogu `c:\xampp\htdocs\phpapp`
2. Uruchom XAMPP Control Panel
3. Wystartuj Apache i MySQL
4. Otwórz phpMyAdmin (http://localhost/phpmyadmin)
5. Zaimportuj plik `database.sql` lub wykonaj zapytania SQL z tego pliku
6. Otwórz aplikację w przeglądarce: http://localhost/phpapp

## Struktura plików

- `index.php` - główna strona aplikacji z interfejsem użytkownika
- `config.php` - konfiguracja połączenia z bazą danych
- `database.sql` - struktura bazy danych i przykładowe dane

## Konfiguracja bazy danych

Domyślne ustawienia w pliku `config.php`:
- Host: localhost
- Nazwa bazy: phpapp_db
- Użytkownik: root
- Hasło: (puste)

Możesz zmienić te ustawienia w pliku `config.php` zgodnie z twoją konfiguracją.

## Użytkowanie

1. Otwórz http://localhost/phpapp
2. Dodaj nowych użytkowników używając formularza
3. Wyświetl listę wszystkich użytkowników
4. Edytuj dane użytkowników klikając "Edytuj"
5. Usuń użytkowników klikając "Usuń"

## Deployment

Aplikacja ma skonfigurowany automatyczny deployment na serwer produkcyjny za pomocą GitHub Actions.

### Automatyczny deployment:
- Uruchamia się po każdym push do brancha `main`
- Aplikacja jest wdrażana na serwer: `http://136.116.111.59/simple-php-app/`
- Zobacz `DEPLOYMENT.md` dla szczegółów konfiguracji

### Ręczny deployment:
Workflow można również uruchomić ręcznie z zakładki "Actions" w repozytorium GitHub.

## Bezpieczeństwo

Aplikacja wykorzystuje:
- PDO z prepared statements (ochrona przed SQL injection)
- Walidację danych po stronie serwera
- Potwierdzenie przed usunięciem użytkownika
- Bezpieczne przechowywanie sekretów w GitHub Actions