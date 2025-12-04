# Instrukcje dla Administratora Serwera

## Problem: GitHub Actions nie może połączyć się z serwerem

**Błąd:** `dial tcp ***:22: i/o timeout`

### Przyczyna
GitHub Actions używa dynamicznych adresów IP z centrów danych Microsoft Azure i nie może przejść przez firewall serwera.

---

## Rozwiązanie 1: Whitelist IP GitHub Actions (ZALECANE)

### Krok 1: Pobierz listę IP GitHub Actions
```bash
curl https://api.github.com/meta | jq -r '.actions[]'
```

### Krok 2: Dodaj IP do firewall
```bash
# Dla UFW (Ubuntu)
for ip in $(curl -s https://api.github.com/meta | jq -r '.actions[]'); do
  sudo ufw allow from $ip to any port 22 proto tcp
done

# Dla iptables
for ip in $(curl -s https://api.github.com/meta | jq -r '.actions[]'); do
  sudo iptables -A INPUT -p tcp --dport 22 -s $ip -j ACCEPT
done

# Dla Google Cloud Firewall
gcloud compute firewall-rules create allow-github-actions-ssh \
  --direction=INGRESS \
  --priority=1000 \
  --network=default \
  --action=ALLOW \
  --rules=tcp:22 \
  --source-ranges=$(curl -s https://api.github.com/meta | jq -r '.actions[]' | tr '\n' ',')
```

---

## Rozwiązanie 2: Self-hosted GitHub Runner

### Instalacja runnera na serwerze
```bash
# Utwórz użytkownika dla runnera
sudo useradd -m -s /bin/bash github-runner
sudo usermod -aG sudo github-runner

# Zaloguj się jako github-runner
sudo su - github-runner

# Pobierz runnera
mkdir actions-runner && cd actions-runner
curl -o actions-runner-linux-x64-2.311.0.tar.gz -L https://github.com/actions/runner/releases/download/v2.311.0/actions-runner-linux-x64-2.311.0.tar.gz
tar xzf ./actions-runner-linux-x64-2.311.0.tar.gz

# Skonfiguruj runnera
./config.sh --url https://github.com/Saykoon/simple-php-app --token [TOKEN_Z_GITHUB]

# Uruchom jako service
sudo ./svc.sh install
sudo ./svc.sh start
```

### Token uzyskasz z:
GitHub → Repository → Settings → Actions → Runners → New self-hosted runner

---

## Rozwiązanie 3: Alternatywny port SSH

Jeśli port 22 jest problematyczny, skonfiguruj SSH na innym porcie:

```bash
# Edytuj konfigurację SSH
sudo nano /etc/ssh/sshd_config

# Dodaj lub zmień linię:
Port 2222

# Restart SSH
sudo systemctl restart sshd

# Otwórz port w firewall
sudo ufw allow 2222/tcp
```

Następnie dodaj sekret w GitHub: `SSH_PORT` = `2222`

---

## Rozwiązanie 4: Reverse Proxy/Tunnel

Użyj tunelu SSH przez publiczny serwer jump/bastion:

```bash
# Na serwerze
ssh -R 2222:localhost:22 user@jump-server.com

# W GitHub Actions
ssh -p 2222 user@jump-server.com
```

---

## Weryfikacja problemu

### Sprawdź logi firewall
```bash
# UFW logs
sudo tail -f /var/log/ufw.log

# iptables logs
sudo tail -f /var/log/syslog | grep iptables

# Google Cloud Firewall logs
gcloud logging read "resource.type=gce_firewall_rule" --limit 50
```

### Sprawdź blokowane połączenia
```bash
sudo tcpdump -i any port 22 -n
```

### Test z zewnątrz
```bash
# Z innego serwera/komputera
telnet 136.116.111.59 22
nc -zv 136.116.111.59 22
```

---

## Kontakt
Jeśli potrzebujesz pomocy z konfiguracją, skontaktuj się z:
- Student: [Twoje dane kontaktowe]
- Repozytorium: https://github.com/Saykoon/simple-php-app
- Email: [Twój email]

## Dodatkowe informacje
- IP GitHub Actions: https://api.github.com/meta
- Dokumentacja: https://docs.github.com/en/actions/hosting-your-own-runners