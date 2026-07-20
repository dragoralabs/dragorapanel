# 🚀 DragoraPanel Installation Guide

> Install **DragoraPanel** on **Ubuntu 22.04+** using **Nginx, PHP 8.3, MariaDB/MySQL, and Redis**.

---

##  Requirements

- Ubuntu 22.04 or newer
- Root or sudo access
- Domain name (recommended)
- SSL certificate (recommended)

---

# 1️ Install Required Packages

```bash
# Install required utilities
apt update
apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg unzip
```

---

# 2️ Add PHP Repository

```bash
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php
```

---

# 3️ Add Redis Repository

```bash
curl -fsSL https://packages.redis.io/gpg | sudo gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg

echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/redis.list
```

---

# 4️ Update Package Lists

```bash
apt update
```

---

# 5️ Install Dependencies

```bash
apt -y install \
php8.3 \
php8.3-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} \
mariadb-server \
nginx \
tar \
unzip \
git \
redis-server
```

---

# 6️ Install Composer

```bash
curl -sS https://getcomposer.org/installer | sudo php -- \
--install-dir=/usr/local/bin \
--filename=composer
```

---

# 7️ Download DragoraPanel

```bash
mkdir -p /var/www/dragorapanel
cd /var/www/dragorapanel

curl -Lo panel.zip \
https://github.com/dragoralabs/dragorapanel/releases/latest/download/panel.zip

unzip panel.zip

chmod -R 755 storage/* bootstrap/cache/
```

---

# 8️ Create Database

### MariaDB

```bash
mariadb -u root -p
```

### MySQL

```bash
mysql -u root -p
```

Run:

```sql
CREATE DATABASE dragorapanel
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

CREATE USER 'dragorapanel'@'127.0.0.1'
IDENTIFIED BY 'yourPassword';

GRANT ALL PRIVILEGES
ON dragorapanel.*
TO 'dragorapanel'@'127.0.0.1'
WITH GRANT OPTION;

FLUSH PRIVILEGES;
```

---

# 9️ Install Panel

```bash
cp .env.example .env

COMPOSER_ALLOW_SUPERUSER=1 composer install \
--no-dev \
--optimize-autoloader

php artisan key:generate --force

php artisan migrate --seed --force

php artisan storage:link

npm install
npm run build
```

---

#  Default Login

> **Change these immediately after logging in.**

| Email | Password |
|-------|----------|
| admin@hostit.local | admin123 |

---

# 10 Set Permissions

### Ubuntu / Debian (Nginx)

```bash
chown -R www-data:www-data /var/www/dragorapanel/*
```

### RHEL / Rocky / AlmaLinux (Nginx)

```bash
chown -R nginx:nginx /var/www/dragorapanel/*
```

### Apache (RHEL / Rocky / AlmaLinux)

```bash
chown -R apache:apache /var/www/dragorapanel/*
```

---

# 1️1 Create Queue Worker

Create:

```
/etc/systemd/system/dragorapanel.service
```

```ini
[Unit]
Description=DragoraPanel Queue Worker
After=network.target redis.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/dragorapanel

ExecStart=/usr/bin/php artisan queue:work \
--queue=high,default \
--sleep=3 \
--tries=3 \
--timeout=300

Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

Enable services:

```bash
sudo systemctl enable --now redis-server
sudo systemctl enable --now dragorapanel.service
```

---

# 12 Configure Nginx

Remove the default site:

```bash
sudo rm /etc/nginx/sites-enabled/default
```

Create:

```
/etc/nginx/sites-available/dragorapanel.conf
```

```nginx
server {
    listen 80;
    server_name {{domain}};
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name {{domain}};

    ssl_certificate     {{ssl_cert}};
    ssl_certificate_key {{ssl_key}};

    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    root {{root}}/public;
    index index.php;

    access_log {{root}}/storage/logs/access.log;
    error_log  {{root}}/storage/logs/error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;

        include fastcgi_params;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.env {
        deny all;
    }

    gzip on;
    gzip_min_length 256;

    gzip_types
        text/plain
        text/css
        application/json
        application/javascript
        text/xml
        application/xml
        text/javascript
        image/svg+xml;

    gzip_vary on;

    client_max_body_size 100m;

    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header Host $host;
}
```

---

# 13 Enable Site

Ubuntu/Debian:

```bash
sudo ln -s \
/etc/nginx/sites-available/dragorapanel.conf \
/etc/nginx/sites-enabled/dragorapanel.conf
```

> Skip this step on **RHEL**, **Rocky Linux**, or **AlmaLinux**.

---

# 1️4 Restart Nginx

```bash
sudo systemctl restart nginx
```

---

# ✅ Installation Complete

Your DragoraPanel installation should now be available at:

```
https://your-domain.com
```

---

## Welcome to DragoraPanel!

If you encounter any issues, please open an issue on the GitHub repository.
