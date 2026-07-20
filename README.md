# Add "add-apt-repository" command
apt -y install software-properties-common curl apt-transport-https ca-certificates gnupg unzip

# Add additional repositories for PHP (Ubuntu 22.04)
LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php

# Add Redis official APT repository
curl -fsSL https://packages.redis.io/gpg | sudo gpg --dearmor -o /usr/share/keyrings/redis-archive-keyring.gpg
echo "deb [signed-by=/usr/share/keyrings/redis-archive-keyring.gpg] https://packages.redis.io/deb $(lsb_release -cs) main" | sudo tee /etc/apt/sources.list.d/redis.list

# Update repositories list
apt update

# Install Dependencies
apt -y install php8.3 php8.3-{common,cli,gd,mysql,mbstring,bcmath,xml,fpm,curl,zip} mariadb-server nginx tar unzip git redis-server

curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

mkdir -p /var/www/dragorapanel
cd /var/www/dragorapanel

curl -Lo panel.zip https://github.com/dragoralabs/dragorapanel/releases/latest/download/panel.zip
unzip panel.zip
chmod -R 755 storage/* bootstrap/cache/

# If using MariaDB (v11.0.0+) (This is the default when installing panel by following the documentation.)
mariadb -u root -p

# If using MySQL
mysql -u root -p

CREATE USER 'dragorapanel'@'127.0.0.1' IDENTIFIED BY 'yourPassword';
CREATE DATABASE dragorapanel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON dragorapanel.* TO 'dragorapanel'@'127.0.0.1' WITH GRANT OPTION;
FLUSH PRIVILEGES;

cp .env.example .env

COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader

# Only run the command below if you are installing this Panel for
# the first time and do not have any Panel data in the database.

php artisan key:generate --force

php artisan migrate --seed --force

php artisan storage:link

npm install
npm run build

Login- admin@hostit.local / admin123

# If using NGINX, Apache or Caddy (not on RHEL / Rocky Linux / AlmaLinux)
chown -R www-data:www-data /var/www/dragorapanel/*

# If using NGINX on RHEL / Rocky Linux / AlmaLinux
chown -R nginx:nginx /var/www/dragorapanel/*

# If using Apache on RHEL / Rocky Linux / AlmaLinux
chown -R apache:apache /var/www/dragorapanel/*

/etc/systemd/system/dragorapanel.service

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

sudo systemctl enable --now redis-server

sudo systemctl enable --now dragorapanel.service

rm /etc/nginx/sites-enabled/default

/etc/nginx/sites-available/dragorapanel.conf

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
    ssl_protocols       TLSv1.2 TLSv1.3;
    ssl_ciphers         HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;
    ssl_session_cache   shared:SSL:10m;
    ssl_session_timeout 10m;

    root {{root}}/public;
    index index.php;

    access_log  {{root}}/storage/logs/access.log;
    error_log   {{root}}/storage/logs/error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass   127.0.0.1:9000;
        fastcgi_index  index.php;
        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;
        include        fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~ \.env {
        deny all;
    }

    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml text/javascript image/svg+xml;
    gzip_min_length 256;
    gzip_vary on;

    client_max_body_size 100m;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_set_header Host $host;
}


# You do not need to symlink this file if you are using RHEL, Rocky Linux, or AlmaLinux.
sudo ln -s /etc/nginx/sites-available/dragorapanel.conf /etc/nginx/sites-enabled/dragorapanel.conf

# You need to restart nginx regardless of OS.
sudo systemctl restart nginx
