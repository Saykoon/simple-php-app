# GitHub Actions Secrets Setup Guide

## Required GitHub Secrets

Aby workflow działał poprawnie, musisz ustawić następujące sekrety w swoim repozytorium GitHub:

### 1. Idź do Settings → Secrets and variables → Actions

### 2. Dodaj następujące sekrety:

#### Server Configuration:
- **SSH_PRIVATE_KEY**: Klucz SSH do łączenia się z serwerem
  ```
  Pobierz klucz z: github.com/valdemarcz/uwb_app/gcp_vm_key
  Skopiuj zawartość całego klucza prywatnego (włącznie z -----BEGIN ... END-----)
  ```

- **SERVER_HOST**: `136.116.111.59`
- **SSH_USER**: `github-actions`

#### Database Configuration:
- **DB_HOST**: `136.114.93.122`
- **DB_PORT**: `8002`
- **DB_NAME**: `[TWÓJ_NUMER_ALBUMU]` (zastąp swoim numerem albumu)
- **DB_USER**: `stud`
- **DB_PASSWORD**: `Uwb123!!`

#### GCP Configuration (jeśli potrzebne):
- **GCP_SA_KEY**: Zawartość pliku JSON z kluczem serwisowym
  ```
  Pobierz z: github.com/valdemarcz/uwb_app/blob/main/peak-vista-478015-f6-6e6f1f882985.json
  Skopiuj całą zawartość pliku JSON
  ```

## Instrukcje ustawiania sekretów:

1. Idź do swojego repozytorium na GitHub
2. Kliknij **Settings** (tab w górnym menu repozytorium)
3. W lewym menu kliknij **Secrets and variables** → **Actions**
4. Kliknij **New repository secret**
5. Wpisz nazwę sekretu (np. SSH_PRIVATE_KEY) i wartość
6. Kliknij **Add secret**
7. Powtórz dla wszystkich sekretów

## Workflow będzie uruchamiany automatycznie po:
- Każdym push do brancha `main`
- Każdym pull request do brancha `main`

## Co robi deployment:
1. Pobiera najnowszy kod z repozytorium
2. Łączy się z serwerem przez SSH
3. Klonuje/aktualizuje kod w katalogu `/var/www/vc/simple-php-app/`
4. Aktualizuje konfigurację bazy danych
5. Ustawia odpowiednie uprawnienia plików
6. Restartuje serwer web
7. Weryfikuje deployment

## URL aplikacji po deployment:
`http://136.116.111.59/simple-php-app/`