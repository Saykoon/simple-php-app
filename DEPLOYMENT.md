# GitHub Actions Secrets Setup Guide

## Required GitHub Secrets

Aby workflow działał poprawnie, musisz ustawić następujące sekrety w swoim repozytorium GitHub:

### 1. Idź do Settings → Secrets and variables → Actions

### 2. Dodaj następujące sekrety:

#### Server Configuration:
- **PRIVATE_SSH_KEY**: Klucz SSH do łączenia się z serwerem
  ```
  Pobierz klucz z: github.com/valdemarcz/uwb_app/gcp_vm_key
  Skopiuj zawartość całego klucza prywatnego (włącznie z -----BEGIN ... END-----)
  ```

- **SSH_PASSPHRASE**: `github` (hasło do klucza SSH)
- **SERVER_HOST**: `136.116.111.59`
- **SSH_USER**: `github-actions`

#### Database Configuration:
- **DB_HOST**: `34.58.246.93` (Google Cloud SQL)
- **DB_NAME**: `stud[NUMER_ALBUMU]` (np. stud88327)
- **DB_USER**: `stud`
- **DB_PASSWORD**: `Uwb123!!`

**Uwaga:** Baza danych używa Google Cloud SQL. Upewnij się, że IP serwera (136.116.111.59) jest dozwolone w Cloud SQL Network.

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