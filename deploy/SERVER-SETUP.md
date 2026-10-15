# সার্ভার সেটআপ গাইড (Ubuntu 24.04 VPS, Singapore)

এই গাইডের কমান্ডগুলো উপর থেকে নিচে ক্রমানুসারে কপি করে চালান। `YOUR_...` বা `STRONG_PASSWORD` লেখা জায়গাগুলো নিজের মান দিয়ে বদলে নিন।
`#` দিয়ে শুরু হওয়া লাইন মন্তব্য, চালাতে হবে না।

**স্থাপত্য:** ব্রাউজার → Cloudflare → Nginx (এই সার্ভার)।
- `fayerahammed.com` → `/var/www/frontend/dist` (স্ট্যাটিক React)
- `api.fayerahammed.com` → `/var/www/backend/public` (Laravel API + `/admin`)

**কোন কনফিগ কোথায় আছে:** সবই এই রিপোর `deploy/` ফোল্ডারে (`nginx/`, `supervisor/`, `cron/`, `deploy-backend.sh`, `scripts/`, `*.env.production.example`)।

> **ক্রম গুরুত্বপূর্ণ:** ফ্রন্টএন্ড বিল্ডের সময় লাইভ API থেকে ডেটা টেনে HTML বানানো হয় (API না থাকলে বিল্ড ব্যর্থ হয়)।
> তাই আগে ধাপ ১–১১ সেরে API চালু করুন, তারপর ধাপ ১২-এ GitHub থেকে প্রথম ডেপ্লয় চালান।

---

## ধাপ ১: Cloudflare প্রস্তুতি (ব্রাউজারে)

1. **DNS:** তিনটা `A` রেকর্ড, সবই *Proxied* (কমলা মেঘ): `@`, `www`, `api` → সার্ভারের IP।
2. **SSL/TLS → Overview:** মোড **Full (strict)**।
3. **SSL/TLS → Origin Server → Create Certificate:** hostname `fayerahammed.com` ও `*.fayerahammed.com`, মেয়াদ ১৫ বছর। **Certificate (PEM)** আর **Private key** দুটোই কপি করে রাখুন (ধাপ ৮-এ লাগবে)। Private key পরে আর দেখা যায় না।
4. **SSL/TLS → Edge Certificates:** *Always Use HTTPS* চালু, *Minimum TLS Version* 1.2।
5. **Turnstile → Add widget:** domain `fayerahammed.com`, mode *Managed*। **Site key** (ফ্রন্টএন্ডের জন্য) ও **Secret key** (ব্যাকএন্ডের জন্য) রাখুন।

## ধাপ ২: প্রথম লগইন, আপডেট ও `deploy` ইউজার

```bash
ssh root@YOUR_SERVER_IP

apt update && apt -y full-upgrade
timedatectl set-timezone Asia/Brunei

# ডেপ্লয়ের জন্য আলাদা ইউজার (পাসওয়ার্ড-ছাড়া, শুধু SSH key)
adduser --disabled-password --gecos "" deploy
mkdir -p /home/deploy/.ssh
cp /root/.ssh/authorized_keys /home/deploy/.ssh/authorized_keys
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh && chmod 600 /home/deploy/.ssh/authorized_keys
usermod -aG sudo deploy
echo 'deploy ALL=(ALL) NOPASSWD:ALL' > /etc/sudoers.d/90-deploy-setup   # সেটআপ শেষে ধাপ ১৩-তে মুছবেন
chmod 440 /etc/sudoers.d/90-deploy-setup
```

**নতুন টার্মিনালে** `ssh deploy@YOUR_SERVER_IP` দিয়ে ঢুকতে পারছেন কি না দেখুন। পারলে তবেই নিচের ধাপ করুন (নইলে নিজেকে তালাবদ্ধ করবেন):

```bash
# root লগইন ও পাসওয়ার্ড লগইন বন্ধ
cat > /etc/ssh/sshd_config.d/99-hardening.conf <<'EOF'
PermitRootLogin no
PasswordAuthentication no
KbdInteractiveAuthentication no
EOF
sshd -t && systemctl reload ssh
```

এখন থেকে `deploy` ইউজারে কাজ করুন (`sudo` দিয়ে):

```bash
exit
ssh deploy@YOUR_SERVER_IP
```

## ধাপ ৩: ফায়ারওয়াল, fail2ban, অটো সিকিউরিটি আপডেট

```bash
sudo apt install -y ufw fail2ban unattended-upgrades

sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow OpenSSH
sudo ufw allow 80/tcp     # সেটআপের সময় সাময়িক; ধাপ ১১-তে শুধু Cloudflare-এর জন্য সীমিত করা হবে
sudo ufw allow 443/tcp
sudo ufw --force enable

sudo systemctl enable --now fail2ban
sudo dpkg-reconfigure -plow unattended-upgrades   # "Yes" নির্বাচন করুন
```

(ঐচ্ছিক, ২ GB-র কম RAM হলে) swap:

```bash
sudo fallocate -l 1G /swapfile && sudo chmod 600 /swapfile && sudo mkswap /swapfile && sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
```

## ধাপ ৪: প্যাকেজ ইনস্টল (Nginx, PHP 8.3, MySQL 8, Supervisor)

```bash
sudo apt install -y nginx mysql-server supervisor git unzip curl acl rsync composer \
  php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-zip php8.3-gd php8.3-intl php8.3-bcmath php8.3-opcache

php -v && nginx -v && mysql --version && composer --version
sudo usermod -aG www-data deploy     # পরের লগইন থেকে কার্যকর
```

**PHP সেটিং** (আপলোড 5 MB, OPcache চালু):

```bash
sudo tee /etc/php/8.3/fpm/conf.d/99-fayer.ini >/dev/null <<'EOF'
expose_php = Off
upload_max_filesize = 6M
post_max_size = 8M
memory_limit = 256M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 10000
; কোড বদলালে OPcache পুনরায় পড়তে হয়; ডেপ্লয় স্ক্রিপ্ট নিজে php-fpm reload করে
opcache.validate_timestamps = 0
EOF
sudo systemctl restart php8.3-fpm
```

## ধাপ ৫: MySQL ডাটাবেস ও ইউজার

```bash
sudo mysql_secure_installation   # root পাসওয়ার্ড/anonymous ইউজার/test DB — সব প্রশ্নে Y

sudo mysql <<'SQL'
CREATE DATABASE fayer_ahammed CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'fayer_app'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON fayer_ahammed.* TO 'fayer_app'@'localhost';
FLUSH PRIVILEGES;
SQL
```

`STRONG_PASSWORD` একটা লম্বা র‍্যান্ডম পাসওয়ার্ড (`openssl rand -base64 24`) দিন; একই মান ধাপ ৭-এ `.env`-এ বসবে।

## ধাপ ৬: কোড আনা (GitHub deploy key)

```bash
sudo mkdir -p /var/www/frontend/dist
sudo chown -R deploy:www-data /var/www
sudo chmod -R g+rwX /var/www

# শুধু-পড়ার deploy key
ssh-keygen -t ed25519 -C "fayer-server" -f ~/.ssh/github_deploy -N ""
cat ~/.ssh/github_deploy.pub
```

প্রিন্ট হওয়া public key GitHub রিপো → **Settings → Deploy keys → Add** (Allow write access **না**) এ বসান। তারপর:

```bash
cat >> ~/.ssh/config <<'EOF'
Host github.com
  IdentityFile ~/.ssh/github_deploy
  IdentitiesOnly yes
EOF
chmod 600 ~/.ssh/config
ssh -T git@github.com    # "Hi ...! You've successfully authenticated" দেখাবে

git clone git@github.com:YOUR_GITHUB_USER/YOUR_REPO.git /var/www/fayerahammed
ln -s /var/www/fayerahammed/backend /var/www/backend   # Nginx ও Supervisor এই পাথ ধরে
```

## ধাপ ৭: Laravel প্রথম ইনস্টল

```bash
cd /var/www/fayerahammed/backend
umask 002

# স্টোরেজ ও ক্যাশ www-data-র লেখার যোগ্য করা
sudo chgrp -R www-data storage bootstrap/cache
sudo chmod -R ug+rwX storage bootstrap/cache
sudo find storage bootstrap/cache -type d -exec chmod g+s {} +

# .env
cp ../deploy/backend.env.production.example .env
nano .env                      # প্রতিটি "CHANGE" বসান (চেকলিস্ট ফাইলের নিচে আছে)
sudo chgrp www-data .env && chmod 640 .env

composer install --no-dev --optimize-autoloader --no-interaction
php artisan key:generate --force
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder --force
php artisan storage:link
```

> **ডেমো ডেটা সিড করবেন না** (`db:seed` একা চালাবেন না): ওতে নকল ছবি/ঠিকানা আছে। অ্যাডমিন প্যানেলে ঢুকে আসল তথ্য দিন।
> চাইলে সেটিংসের কাঠামো পেতে একবার `php artisan db:seed --class=SettingSeeder --force` চালিয়ে পরে সম্পাদনা করতে পারেন।
> কাজ শেষে `.env` থেকে `ADMIN_PASSWORD` মুছে দেওয়া ভালো।

**ডেপ্লয় স্ক্রিপ্টের জন্য** `deploy` ইউজারকে শুধু php-fpm reload-এর অনুমতি:

```bash
echo 'deploy ALL=(root) NOPASSWD: /usr/bin/systemctl reload php8.3-fpm' | sudo tee /etc/sudoers.d/deploy-fpm
sudo chmod 440 /etc/sudoers.d/deploy-fpm && sudo visudo -cf /etc/sudoers.d/deploy-fpm
```

## ধাপ ৮: Nginx

```bash
cd /var/www/fayerahammed/deploy/nginx

sudo cp conf.d/00-fayer.conf /etc/nginx/conf.d/
sudo cp snippets/*.conf /etc/nginx/snippets/
sudo cp sites-available/*.conf /etc/nginx/sites-available/

# Cloudflare Origin সার্টিফিকেট (ধাপ ১ থেকে)
sudo mkdir -p /etc/ssl/cloudflare
sudo nano /etc/ssl/cloudflare/fayerahammed.com.pem   # Certificate (PEM) পেস্ট
sudo nano /etc/ssl/cloudflare/fayerahammed.com.key   # Private key পেস্ট
sudo chmod 600 /etc/ssl/cloudflare/fayerahammed.com.key

sudo ln -s /etc/nginx/sites-available/fayerahammed.com.conf /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/api.fayerahammed.com.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

sudo nginx -t && sudo systemctl reload nginx
```

কাজ করছে কি না (ফ্রন্টএন্ড এখনো আপলোড হয়নি, তাই শুধু API দেখুন):

```bash
curl -sI https://api.fayerahammed.com/api/v1/home | head -12      # 200 + সিকিউরিটি হেডার
curl -s  https://api.fayerahammed.com/api/v1/settings | head -c 300
```

## ধাপ ৯: Supervisor (queue worker) ও cron (scheduler)

```bash
sudo cp /var/www/fayerahammed/deploy/supervisor/fayer-queue.conf /etc/supervisor/conf.d/
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl status          # fayer-queue:fayer-queue_00   RUNNING

sudo cp /var/www/fayerahammed/deploy/cron/fayer-scheduler /etc/cron.d/fayer-scheduler
sudo chown root:root /etc/cron.d/fayer-scheduler && sudo chmod 644 /etc/cron.d/fayer-scheduler
```

## ধাপ ১০: ব্যাকআপ যাচাই

প্রতিদিন রাত ০১:৩০-এ ডাটাবেস + আপলোড করা ছবির ব্যাকআপ হয়, ১৪ দিন রাখা হয়; ব্যর্থ হলে `ADMIN_NOTIFY_EMAIL`-এ মেইল যায়।

```bash
cd /var/www/backend
sudo -u www-data php artisan backup:run
sudo -u www-data php artisan backup:list
sudo unzip -l storage/app/private/*/*.zip | head -20   # db-dumps/*.sql ও public/... ফাইল থাকতে হবে
```

> ব্যাকআপ এই সার্ভারেই থাকে। **সার্ভার হারালে ব্যাকআপও যায়।** সপ্তাহে অন্তত একবার অন্য কোথাও কপি করুন, যেমন নিজের কম্পিউটারে:
> `rsync -az deploy@YOUR_SERVER_IP:/var/www/backend/storage/app/private/ ~/fayer-backups/`
> অথবা `config/filesystems.php`-এ S3/Backblaze ডিস্ক যোগ করে `config/backup.php`-এর `destination.disks`-এ বসান।

**রিস্টোর:** `unzip` করে `mysql -u fayer_app -p fayer_ahammed < db-dumps/mysql-fayer_ahammed.sql`, আর `public/` ফোল্ডারের ফাইল `storage/app/public/`-এ কপি।

## ধাপ ১১: ফায়ারওয়াল শুধু Cloudflare-এর জন্য (সাইট Cloudflare দিয়ে চলছে নিশ্চিত হওয়ার পর)

এতে কেউ সরাসরি সার্ভারের IP-তে এসে Cloudflare এড়াতে পারবে না।

```bash
sudo bash /var/www/fayerahammed/deploy/scripts/allow-cloudflare-firewall.sh

# Cloudflare-এর IP তালিকা মাসে একবার হালনাগাদ (Nginx real-IP + ফায়ারওয়াল)
echo '0 4 1 * * root /var/www/fayerahammed/deploy/scripts/update-cloudflare-ips.sh && /var/www/fayerahammed/deploy/scripts/allow-cloudflare-firewall.sh >/dev/null' \
  | sudo tee /etc/cron.d/cloudflare-ips
```

## ধাপ ১২: GitHub Actions (স্বয়ংক্রিয় ডেপ্লয়)

**ক) CI-র জন্য আলাদা SSH key বানান** (নিজের কম্পিউটারে):

```bash
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ./gh_deploy -N ""
ssh-copy-id -i ./gh_deploy.pub deploy@YOUR_SERVER_IP      # অথবা .pub-এর লেখা সার্ভারের ~/.ssh/authorized_keys-তে যোগ করুন
ssh-keyscan -H YOUR_SERVER_IP                             # এই আউটপুট SSH_KNOWN_HOSTS হবে
```

**খ) GitHub রিপো → Settings → Secrets and variables → Actions → New repository secret:**

| Secret | মান |
|---|---|
| `SSH_HOST` | সার্ভারের IP বা hostname |
| `SSH_USER` | `deploy` |
| `SSH_PORT` | (ঐচ্ছিক) ডিফল্ট `22` |
| `SSH_PRIVATE_KEY` | `gh_deploy` ফাইলের পুরো লেখা (`-----BEGIN` থেকে `END-----` পর্যন্ত) |
| `SSH_KNOWN_HOSTS` | `ssh-keyscan` আউটপুট |
| `VITE_API_URL` | `https://api.fayerahammed.com/api/v1` |
| `VITE_SITE_URL` | `https://fayerahammed.com` |
| `VITE_TURNSTILE_SITE_KEY` | Turnstile **site** key |

Settings → Environments → **production** তৈরি করুন (চাইলে *Required reviewers* দিন, তাহলে প্রতিবার আপনার অনুমোদনের পর ডেপ্লয় হবে)।

**গ) `main` ব্রাঞ্চে push করুন।** Actions ট্যাবে দেখবেন: ব্যাকএন্ড টেস্ট → ফ্রন্টএন্ড বিল্ড → ডেপ্লয় (ব্যাকএন্ড, তারপর ফ্রন্টএন্ড আপলোড, তারপর smoke test)।
পুল রিকোয়েস্টে শুধু টেস্ট ও লিন্ট চলে, ডেপ্লয় হয় না।

**ঘ) সাইট খুলে দেখুন:** `https://fayerahammed.com`, `https://api.fayerahammed.com/admin`

## ধাপ ১৩: সেটআপ শেষের পরিষ্কার-পরিচ্ছন্নতা

```bash
sudo rm /etc/sudoers.d/90-deploy-setup      # সেটআপের সময়কার পূর্ণ sudo অধিকার তুলে নেওয়া
# ডেপ্লয় ইউজারের শুধু php-fpm reload অধিকার (deploy-fpm ফাইলে) থাকবে
```

---

## শেষ যাচাই তালিকা

```bash
# হেডার (HSTS, CSP, nosniff, X-Frame-Options)
curl -sI https://fayerahammed.com/ | grep -iE "strict-transport|content-security|x-frame|x-content|cache-control"
# হ্যাশ করা ফাইলের ক্যাশ (immutable, এক বছর)
curl -sI https://fayerahammed.com/assets/$(ls /var/www/frontend/dist/assets | grep css | head -1) | grep -i cache-control
# robots / sitemap
curl -s https://fayerahammed.com/robots.txt; curl -s https://fayerahammed.com/sitemap.xml | head -5
# সত্যিকারের ভিজিটর IP লগে আসছে কি না (Cloudflare-এর IP নয়)
sudo tail -3 /var/log/nginx/api.fayerahammed.com.access.log
# queue ও scheduler
sudo supervisorctl status; cd /var/www/backend && php artisan schedule:list
```

সাইট থেকে একটা কন্ট্যাক্ট মেসেজ পাঠান → `ADMIN_NOTIFY_EMAIL`-এ মেইল ও প্রেরকের কাছে অটো-রিপ্লাই আসছে কি না দেখুন। না এলে: `sudo tail -50 /var/www/backend/storage/logs/laravel-*.log` ও `sudo tail /var/log/supervisor/fayer-queue.log`।

## দৈনন্দিন কাজ

| কাজ | কমান্ড |
|---|---|
| ডেপ্লয় | `main`-এ push (অথবা সার্ভারে `bash /var/www/fayerahammed/deploy/deploy-backend.sh`) |
| আগের সংস্করণে ফেরা | `cd /var/www/fayerahammed && git reset --hard COMMIT_SHA && bash deploy/deploy-backend.sh` |
| অ্যাডমিনে কনটেন্ট বদলের পর Google-এর জন্য HTML হালনাগাদ | GitHub → Actions → *CI and deploy* → **Run workflow** (ফ্রন্টএন্ড নতুন করে বিল্ড হবে) |
| লগ | `/var/log/nginx/*.log`, `/var/www/backend/storage/logs/`, `/var/log/supervisor/fayer-queue.log` |
| ছবির সাইজ/কনভার্শন বদলালে (নতুন ছবিতে নিজে প্রযোজ্য, পুরনোগুলোর জন্য) | `cd /var/www/backend && sudo -u www-data php artisan media-library:regenerate --force` |
| Nginx পরিবর্তনের পর | `sudo nginx -t && sudo systemctl reload nginx` |
