# DragoraPanel

A Minecraft server management panel built with Laravel.

## Requirements

- PHP 8.3+
- Composer 2.x
- Node.js 20+ with npm
- MySQL 8.0+ or MariaDB 10.3+
- Redis 6+ (for queue and cache)
- Java 21+ (on node agents)

### Required PHP Extensions

```
pdo_mysql mbstring xml curl gd bcmath fileinfo openssl ctype json tokenizer
```

## Installation

### 1. Clone the Repository

```bash
git clone <repo-url> dragorapanel
cd dragorapanel
```

### 2. Configure Environment

```bash
cp .env.example .env
nano .env
```

Set these values:

```
APP_NAME=DragoraPanel
APP_URL=http://your-domain.com
APP_ENV=production
APP_DEBUG=false

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dragorapanel
DB_USERNAME=root
DB_PASSWORD=yourpassword

SESSION_DRIVER=file
QUEUE_CONNECTION=redis
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 3. Generate App Key

```bash
php artisan key:generate
```

### 4. Create Database

```bash
mysql -u root -p -e "CREATE DATABASE dragorapanel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### 5. Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

### 6. Run Migrations

```bash
php artisan migrate --force
```

### 7. Seed Initial Data

```bash
curl http://localhost:8000/api/setup
```

This creates an admin user (`admin@hostit.local` / `admin123`) and default settings.

### 8. Storage Link

```bash
php artisan storage:link
```

### 9. Install & Build Frontend

```bash
npm install
npm run build
```

### 10. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 11. Configure Queue Worker

The panel uses Redis for queue processing. Run the worker:

```bash
php artisan queue:work --queue=high,default --tries=3 --timeout=300
```

For production, use a supervisor/systemd service (see below).

## Web Server Setup

### Nginx (Without SSL)

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/dragorapanel/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Nginx (With SSL)

```nginx
server {
    listen 443 ssl http2;
    server_name your-domain.com;
    root /var/www/dragorapanel/public;

    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000" always;

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

server {
    listen 80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}
```

### Apache (Without SSL)

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /var/www/dragorapanel/public

    <Directory /var/www/dragorapanel/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dragorapanel-error.log
    CustomLog ${APACHE_LOG_DIR}/dragorapanel-access.log combined
</VirtualHost>
```

Enable `mod_rewrite`:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Apache (With SSL)

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    Redirect permanent / https://your-domain.com/
</VirtualHost>

<VirtualHost *:443>
    ServerName your-domain.com
    DocumentRoot /var/www/dragorapanel/public

    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/your-domain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/your-domain.com/privkey.pem

    <Directory /var/www/dragorapanel/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/dragorapanel-error.log
    CustomLog ${APACHE_LOG_DIR}/dragorapanel-access.log combined
</VirtualHost>
```

### Caddy (With Automatic SSL)

```caddy
your-domain.com {
    root * /var/www/dragorapanel/public
    encode gzip
    php_fastcgi unix//var/run/php/php8.3-fpm.sock
    file_server
}
```

### Caddy (Without SSL)

```caddy
your-domain.com:80 {
    root * /var/www/dragorapanel/public
    encode gzip
    php_fastcgi unix//var/run/php/php8.3-fpm.sock
    file_server
}
```

## Systemd Services

### Queue Worker

Create `/etc/systemd/system/dragorapanel-queue.service`:

```ini
[Unit]
Description=DragoraPanel Queue Worker
After=network.target redis.service

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/dragorapanel
ExecStart=/usr/bin/php artisan queue:work --queue=high,default --sleep=3 --tries=3 --timeout=300
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now dragorapanel-queue
```

### Node Agent

Create `/etc/systemd/system/dragorapanel-agent.service`:

```ini
[Unit]
Description=DragoraPanel Node Agent
After=network.target mysql.service redis.service

[Service]
User=nodeuser
WorkingDirectory=/var/www/dragorapanel/node_agent
ExecStart=/usr/bin/node index.js
Restart=always
RestartSec=5

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable --now dragorapanel-agent
```

## Node Agent Setup

The node agent runs on game server hosts and communicates with the panel.

### 1. Create a Node in the Panel

In the admin panel, go to Nodes > Add Node. Copy the generated token.

### 2. Configure the Agent

```bash
cd node_agent
cp .env.example .env
nano .env
```

Set:

```
NODE_NAME=node1
NODE_TOKEN=<token-from-panel>
PANEL_URL=http://your-panel-domain.com/api
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=dragorapanel
DB_USER=root
DB_PASSWORD=yourpassword
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
SERVER_DIR=./servers
BACKUP_DIR=./backups
JAVA_PATH=/usr/bin/java
```

### 3. Install Dependencies & Start

```bash
npm install
node index.js
```

## First Login

Visit `http://your-domain.com/auth/login` and log in with:

- **Email:** admin@hostit.local
- **Password:** admin123

Change the password immediately after first login.

## Development Server

For local development:

```bash
php artisan serve --port=8050
```

Then visit `http://localhost:8050`.

## Update

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
npm install && npm run build
php artisan config:cache
php artisan optimize:clear
sudo systemctl restart dragorapanel-queue
```

## Backup

```bash
# Database
mysqldump -u root -p dragorapanel > backup-$(date +%Y%m%d).sql

# Storage
tar -czf storage-backup-$(date +%Y%m%d).tar.gz storage/app/public/

# Restore
mysql -u root -p dragorapanel < backup-20260101.sql
tar -xzf storage-backup-20260101.tar.gz
```
