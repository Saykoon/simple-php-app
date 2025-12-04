# Sprawozdanie: Wdrożenie aplikacji PHP za pomocą GitHub Actions

**Autor:** [Twoje imię i nazwisko]  
**Data:** 27 listopada 2025  
**Przedmiot:** [Nazwa przedmiotu]  
**Temat:** Automatyczne wdrażanie aplikacji PHP na serwer za pomocą CI/CD

## 1. Wprowadzenie

Celem projektu było utworzenie prostej aplikacji PHP z połączeniem do bazy danych oraz skonfigurowanie automatycznego procesu wdrażania (deployment) na serwer produkcyjny za pomocą GitHub Actions.

### 1.1 Specyfikacja środowiska
- **Serwer docelowy:** 136.116.111.59
- **Port SSH:** 8002 (niestandardowy)
- **Użytkownik SSH:** github-actions
- **Katalog deployment:** /var/www/html/simple-php-app
- **Baza danych:** Google Cloud SQL (host: 34.58.246.93)
- **Repozytorium:** https://github.com/Saykoon/simple-php-app

## 2. Architektura aplikacji

### 2.1 Struktura projektu
```
simple-php-app/
├── index.php              # Główna aplikacja z interfejsem użytkownika
├── config.php             # Konfiguracja połączenia z bazą danych (lokalna)
├── config.production.php  # Szablon konfiguracji produkcyjnej
├── database.sql           # Struktura bazy danych i przykładowe dane
├── README.md              # Dokumentacja projektu
├── DEPLOYMENT.md          # Instrukcje wdrażania
├── .github/workflows/
│   └── deploy.yml         # Workflow GitHub Actions
└── .gitignore             # Pliki ignorowane przez Git
```

### 2.2 Funkcjonalności aplikacji
- Dodawanie nowych użytkowników (imię, nazwisko, email, wiek)
- Wyświetlanie listy użytkowników z bazy danych
- Edycja istniejących użytkowników
- Usuwanie użytkowników z potwierdzeniem
- Responsywny interfejs HTML/CSS
- Bezpieczne połączenie z bazą danych (PDO, prepared statements)

## 3. Konfiguracja GitHub Actions

### 3.1 Workflow CI/CD (deploy.yml)

Utworzono workflow automatycznego wdrażania składający się z następujących kroków:

```yaml
name: Deploy PHP Application to Server

on:
  push:
    branches: [ main ]
  pull_request:
    branches: [ main ]
```

### 3.2 Kroki procesu wdrażania

#### Krok 1: Checkout kodu
```yaml
- name: Checkout code
  uses: actions/checkout@v4
```
Pobiera najnowszy kod z repozytorium GitHub.

#### Krok 2: Generowanie konfiguracji produkcyjnej
```yaml
- name: Create production config
  run: |
    cat > config.php << 'EOF'
    <?php
    define('DB_HOST', '${{ secrets.DB_HOST }}');
    define('DB_PORT', '${{ secrets.DB_PORT }}');
    // ... pozostała konfiguracja
```
Tworzy plik config.php z danymi produkcyjnymi pobranymi z GitHub Secrets.

#### Krok 3: Transfer plików na serwer
```yaml
- name: Copy files to server
  uses: appleboy/scp-action@v0.1.7
  with:
    host: ${{ secrets.SERVER_HOST }}
    username: ${{ secrets.SSH_USER }}
    key: ${{ secrets.PRIVATE_SSH_KEY }}
    passphrase: ${{ secrets.SSH_PASSPHRASE }}
```
Wykorzystuje akcję appleboy/scp-action do bezpiecznego przesłania plików przez SSH.

#### Krok 4: Konfiguracja środowiska i bazy danych
```yaml
- name: Set permissions and setup database
  uses: appleboy/ssh-action@v1.0.3
```
Ustawia uprawnienia plików i inicjalizuje tabele bazy danych.

### 3.3 GitHub Secrets

Skonfigurowano następujące zmienne środowiskowe w GitHub Secrets:

| Nazwa | Opis | Przykładowa wartość |
|-------|------|---------------------|
| `PRIVATE_SSH_KEY` | Klucz prywatny SSH | -----BEGIN OPENSSH PRIVATE KEY----- |
| `SSH_PASSPHRASE` | Hasło do klucza SSH | github |
| `SERVER_HOST` | Adres IP serwera | 136.116.111.59 |
| `SSH_USER` | Nazwa użytkownika SSH | github-actions |
| `DB_HOST` | Host bazy danych (Google Cloud SQL) | 34.58.246.93 |
| `DB_NAME` | Nazwa bazy danych | stud[numer albumu] |
| `DB_USER` | Użytkownik bazy danych | stud |
| `DB_PASSWORD` | Hasło do bazy danych | Uwb123!! |

## 4. Proces wdrażania

### 4.1 Przygotowanie środowiska lokalnego

1. **Utworzenie aplikacji PHP**
   - Implementacja interfejsu CRUD w pliku index.php
   - Konfiguracja połączenia PDO z MySQL
   - Walidacja danych po stronie serwera

2. **Inicjalizacja repozytorium Git**
   ```bash
   git init
   git add .
   git commit -m "Initial commit: Simple PHP application with database connection"
   git remote add origin https://github.com/Saykoon/simple-php-app.git
   git push -u origin main
   ```

### 4.2 Konfiguracja GitHub Actions

1. **Utworzenie workflow**
   - Dodanie pliku .github/workflows/deploy.yml
   - Konfiguracja triggerów (push do main branch)
   - Definicja kroków deployment

2. **Dodanie sekretów**
   - Settings → Secrets and variables → Actions
   - Repository secrets → New repository secret
   - Dodanie wszystkich wymaganych zmiennych

### 4.3 Debugging i rozwiązywanie problemów

Podczas implementacji napotkano następujące problemy i ich rozwiązania:

#### Problem 1: Permission denied (publickey)
**Przyczyna:** Nieprawidłowy klucz SSH lub błędna nazwa sekretu
**Rozwiązanie:** 
- Poprawienie nazwy sekretu z SSH_PRIVATE_KEY na PRIVATE_SSH_KEY
- Dodanie obsługi passphrase dla klucza SSH

#### Problem 2: Connection timed out (baza danych)
**Przyczyna:** Nieprawidłowy format connection string PDO lub brak dostępu do Cloud SQL
**Rozwiązanie:** 
- Zmiana z `mysql:host=host:port` na `mysql:host=host` (bez portu dla Cloud SQL)
- Zwiększenie timeout parameter do 15 sekund
- Weryfikacja czy IP serwera jest na whitelist w Cloud SQL

#### Problem 3: Not Found (404)
**Przyczyna:** Błędna ścieżka deployment
**Rozwiązanie:** 
- Zmiana ścieżki z /var/www/vc na /var/www/html
- Dodanie debugging do sprawdzenia lokalizacji plików

#### Problem 4: SSH Connection Timeout
**Przyczyna:** Firewall serwera blokuje połączenia z GitHub Actions lub używany jest niestandardowy port SSH
**Rozwiązanie:** 
- Konfiguracja portu SSH na 8002 (niestandardowy port używany przez serwer)
- Zwiększenie timeoutów połączenia (60s timeout, 10m command timeout)
- Dodanie testów connectivity przed deployment

## 5. Weryfikacja wdrożenia

### 5.1 Testy funkcjonalne
Po udanym deployment przeprowadzono następujące testy:

1. **Test połączenia z aplikacją**
   - URL: http://136.116.111.59/simple-php-app/
   - Status: ✅ Aplikacja dostępna

2. **Test funkcji CRUD**
   - ✅ Dodawanie użytkowników
   - ✅ Wyświetlanie listy użytkowników
   - ✅ Edycja danych użytkowników
   - ✅ Usuwanie użytkowników

3. **Test połączenia z bazą danych**
   - ✅ Pomyślne połączenie z MySQL
   - ✅ Operacje SELECT/INSERT/UPDATE/DELETE

### 5.2 Automatyzacja CI/CD
- ✅ Automatyczne wdrażanie przy push do main branch
- ✅ Bezpieczne przechowywanie credentials w GitHub Secrets
- ✅ Proper error handling i logging

## 6. Wnioski

### 6.1 Osiągnięte cele
1. Pomyślnie utworzono funkcjonalną aplikację PHP z interfejsem CRUD
2. Skonfigurowano automatyczny proces CI/CD za pomocą GitHub Actions
3. Wdrożono aplikację na serwer produkcyjny z prawidłowym połączeniem do bazy danych
4. Zabezpieczono proces deployment poprzez GitHub Secrets

### 6.2 Korzyści z zastosowanego rozwiązania
- **Automatyzacja:** Każda zmiana w kodzie automatycznie trafia na serwer
- **Bezpieczeństwo:** Credentials są bezpiecznie przechowywane w GitHub Secrets
- **Kontrola wersji:** Pełna historia zmian w Git
- **Niezawodność:** Wykorzystanie sprawdzonych akcji (appleboy/scp-action, appleboy/ssh-action)

### 6.3 Możliwe ulepszenia
- Dodanie testów automatycznych przed deployment
- Implementacja rollback mechanism
- Konfiguracja różnych środowisk (development, staging, production)
- Monitoring i alerting aplikacji produkcyjnej

## 7. Załączniki

### 7.1 Linki
- **Repozytorium:** https://github.com/Saykoon/simple-php-app
- **Aplikacja produkcyjna:** http://136.116.111.59/simple-php-app/
- **GitHub Actions:** https://github.com/Saykoon/simple-php-app/actions

### 7.2 Kluczowe pliki konfiguracyjne
- `.github/workflows/deploy.yml` - Workflow CI/CD
- `config.php` - Konfiguracja bazy danych
- `database.sql` - Schema bazy danych
- `DEPLOYMENT.md` - Instrukcje deployment

---

**Podsumowanie:** Projekt zakończył się sukcesem. Aplikacja PHP została pomyślnie wdrożona na serwer za pomocą w pełni automatycznego procesu GitHub Actions, demonstrując praktyczne zastosowanie nowoczesnych praktyk DevOps w rozwoju aplikacji webowych.