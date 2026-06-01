#!/bin/bash

if [ "$EUID" -ne 0 ]; then
  echo "Please run as root"
  exit
fi

apt update && apt upgrade -y
apt install -y nginx software-properties-common curl git unzip certbot python3-certbot-nginx

add-apt-repository pps:ondrej/php -y
apt update
apt install -y php8.3-fpm php8.3-cli php8.3-mysql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl

curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

read -p "Enter your domain (e.g., panel.domain.com): " DOMAIN
read -p "Enter your email for SSL: " EMAIL

certbot --nginx -d $DOMAIN --non-interactive --agree-tos -m $EMAIL

cat <<EOF > /etc/nginx/sites-available/kurayami
server {
    listen 80;
    listen [::]:80;
    server_name $DOMAIN;
    return 301 https://\$server_name\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name $DOMAIN;
    root /var/www/kurayami/public;

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php\$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
        include fastcgi_params;
    }
}
EOF

ln -s /etc/nginx/sites-available/kurayami /etc/nginx/sites-enabled/
systemctl restart nginx

mkdir -p /var/www/kurayami
cp -r . /var/www/kurayami
cd /var/www/kurayami

composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate --force

read -p "Database Host [127.0.0.1]: " DB_HOST
read -p "Database Name: " DB_NAME
read -p "Database User: " DB_USER
read -s -p "Database Password: " DB_PASS
echo ""

sed -i "s/DB_HOST=.*/DB_HOST=${DB_HOST:-127.0.0.1}/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=${DB_NAME}/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=${DB_USER}/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${DB_PASS}/" .env

chown -R www-data:www-data /var/www/kurayami
chmod -R 755 /var/www/kurayami/storage

php artisan kurayami:install