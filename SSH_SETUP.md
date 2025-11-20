# Instrukcje utworzenia własnego klucza SSH

## Krok 1: Wygeneruj nowy klucz SSH

```bash
# W PowerShell lub Git Bash
ssh-keygen -t rsa -b 4096 -C "your-email@example.com" -f gcp_vm_key
```

To utworzy dwa pliki:
- `gcp_vm_key` (klucz prywatny) 
- `gcp_vm_key.pub` (klucz publiczny)

## Krok 2: Dodaj klucz publiczny do serwera

Musisz skontaktować się z profesorem/administratorem serwera i poprosić o:

1. **Dodanie twojego klucza publicznego** (`gcp_vm_key.pub`) do serwera
2. **Potwierdzenie nazwy użytkownika** SSH (czy to rzeczywiście `github-actions`)
3. **Sprawdzenie czy katalog `/var/www/vc` istnieje** i masz do niego dostęp

## Krok 3: Dodaj klucz prywatny do GitHub Secrets

- Skopiuj zawartość pliku `gcp_vm_key` (cały, od -----BEGIN do -----END)
- Dodaj jako `SSH_PRIVATE_KEY` w GitHub Secrets

## Kontakt z administratorem serwera:

**Email do profesora/administratora:**

```
Temat: Prośba o dodanie klucza SSH do serwera 136.116.111.59

Dzień dobry,

Pracuję nad projektem PHP z automatycznym deployment i potrzebuję dostępu SSH do serwera.

Czy mogę prosić o:
1. Dodanie mojego klucza publicznego SSH do serwera
2. Potwierdzenie nazwy użytkownika SSH (github-actions?)
3. Sprawdzenie uprawnień do katalogu /var/www/vc

Mój klucz publiczny SSH:
[tutaj wklej zawartość gcp_vm_key.pub]

Z góry dziękuję,
[Twoje imię i nazwisko]
```