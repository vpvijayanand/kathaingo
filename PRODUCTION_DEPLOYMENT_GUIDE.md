# Complete Production Deployment Guide for kathaingo.com

Complete step-by-step guide to deploy your Laravel application on Ubuntu server from scratch.

## Prerequisites

- Fresh Ubuntu 22.04 LTS server (or 20.04)
- Root or sudo access
- Domain name (kathaingo.com) pointing to your server IP
- SSH access to your server

---

## Step 1: Initial Server Setup

### 1.1 Update System Packages

```bash
# SSH into your server
ssh root@your-server-ip

# Update package list and upgrade existing packages
sudo apt update
sudo apt upgrade -y

# Install essential tools
sudo apt install -y curl wget git unzip software-properties-common
```

### 1.2 Create a Non-Root User (Recommended)

```bash
# Create new user
adduser deployer

# Add user to sudo group
usermod -aG sudo deployer

# Switch to new user
su - deployer
```

---

## Step 2: Install PHP 8.2

### 2.1 Add PHP Repository

```bash
# Add Ondřej's PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
```

### 2.2 Install PHP and Required Extensions

```bash
# Install PHP 8.2 and extensions required for Laravel
sudo apt install -y php8.2 \
    php8.2-fpm \
    php8.2-cli \
    php8.2-common \
    php8.2-mysql \
    php8.2-pgsql \
    php8.2-mbstring \
    php8.2-xml \
    php8.2-bcmath \
    php8.2-curl \
    php8.2-gd \
    php8.2-zip \
    php8.2-intl \
    php8.2-readline \
    php8.2-opcache

# Verify PHP installation
php -v
```

### 2.3 Configure PHP

```bash
# Edit PHP-FPM configuration
sudo nano /etc/php/8.2/fpm/php.ini
```

Update these values:
```ini
upload_max_filesize = 100M
post_max_size = 100M
memory_limit = 512M
max_execution_time = 300
date.timezone = Asia/Kolkata

# Enable OPcache for production
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
```

Save and exit (Ctrl+X, Y, Enter)

---

## Step 3: Install Composer

```bash
# Download Composer installer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php

# Install Composer globally
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer

# Verify installation
composer --version

# Clean up
rm composer-setup.php
```

---

## Step 4: Install Node.js and npm

```bash
# Install Node.js 20.x LTS
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Verify installation
node --version
npm --version
```

---

## Step 5: Install and Configure PostgreSQL

### 5.1 Install PostgreSQL

```bash
# Install PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Start PostgreSQL service
sudo systemctl start postgresql
sudo systemctl enable postgresql

# Verify installation
sudo systemctl status postgresql
```

### 5.2 Create Database and User

```bash
# Switch to postgres user
sudo -u postgres psql

# In PostgreSQL prompt, run:
```

```sql
-- Create database
CREATE DATABASE kathaingo;

-- Create user with password
CREATE USER kathaingo_user WITH PASSWORD 'your_secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE kathaingo TO kathaingo_user;

-- Grant schema privileges
\c kathaingo
GRANT ALL ON SCHEMA public TO kathaingo_user;

-- Exit PostgreSQL
\q
```

### 5.3 Configure PostgreSQL for Remote Connections (Optional)

```bash
# Edit PostgreSQL configuration
sudo nano /etc/postgresql/14/main/postgresql.conf
```

Find and update:
```conf
listen_addresses = 'localhost'
```

```bash
# Edit pg_hba.conf for authentication
sudo nano /etc/postgresql/14/main/pg_hba.conf
```

Add this line:
```conf
host    kathaingo    kathaingo_user    127.0.0.1/32    md5
```

Restart PostgreSQL:
```bash
sudo systemctl restart postgresql
```

---

## Step 6: Install and Configure Nginx

### 6.1 Install Nginx

```bash
# Install Nginx
sudo apt install -y nginx

# Start Nginx service
sudo systemctl start nginx
sudo systemctl enable nginx

# Verify installation
sudo systemctl status nginx
```

### 6.2 Configure Firewall

```bash
# Allow Nginx through firewall
sudo ufw allow 'Nginx Full'
sudo ufw allow OpenSSH
sudo ufw enable

# Check firewall status
sudo ufw status
```

---

## Step 7: Deploy Application Code

### 7.1 Clone Repository

```bash
# Navigate to /var
cd /var

# Clone your repository
sudo git clone https://github.com/vpvijayanand/kathaingo.git kathaingo

# Set ownership
sudo chown -R www-data:www-data /var/kathaingo
sudo chown -R $USER:www-data /var/kathaingo
```

### 7.2 Install Dependencies

```bash
# Navigate to project directory
cd /var/kathaingo

# Install PHP dependencies (production only, no dev dependencies)
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm ci

# Build frontend assets
npm run build
```

---

## Step 8: Configure Application Environment

### 8.1 Setup Environment File

```bash
# Copy .env.example to .env
cp .env.example .env

# Edit .env file
nano .env
```

Update these values:
```env
APP_NAME=கதைங்கோ
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://kathaingo.com

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=kathaingo
DB_USERNAME=kathaingo_user
DB_PASSWORD=your_secure_password_here

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
```

### 8.2 Generate Application Key

```bash
# Generate APP_KEY
php artisan key:generate

# Verify .env file has APP_KEY set
cat .env | grep APP_KEY
```

### 8.3 Run Migrations and Seeders

```bash
# Run database migrations
php artisan migrate --force

# Run seeders (this will create admin user and sample posts)
php artisan db:seed --force
```

### 8.4 Optimize Laravel

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Clear any existing caches
php artisan cache:clear
```

---

## Step 9: Set File Permissions

```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/kathaingo

# Set directory permissions
sudo find /var/kathaingo -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/kathaingo -type f -exec chmod 644 {} \;

# Make storage and cache writable
sudo chmod -R 775 /var/kathaingo/storage
sudo chmod -R 775 /var/kathaingo/bootstrap/cache

# Set ownership for storage and cache
sudo chown -R www-data:www-data /var/kathaingo/storage
sudo chown -R www-data:www-data /var/kathaingo/bootstrap/cache
```

---

## Step 10: Configure Nginx Virtual Host

### 10.1 Create Nginx Configuration

```bash
# Create new site configuration
sudo nano /etc/nginx/sites-available/kathaingo.com
```

Paste this configuration:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name kathaingo.com www.kathaingo.com;
    
    root /var/kathaingo/public;
    index index.php index.html;

    # Logging
    access_log /var/log/nginx/kathaingo.com-access.log;
    error_log /var/log/nginx/kathaingo.com-error.log;

    # Max upload size
    client_max_body_size 100M;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json application/javascript;

    # Laravel specific configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 300;
    }

    # Deny access to hidden files
    location ~ /\. {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # Block access to sensitive files
    location ~ /\.env {
        deny all;
        return 404;
    }
}
```

### 10.2 Enable Site and Test Configuration

```bash
# Enable the site
sudo ln -s /etc/nginx/sites-available/kathaingo.com /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# If test passes, restart Nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

---

## Step 11: Install SSL Certificate (Let's Encrypt)

### 11.1 Install Certbot

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx
```

### 11.2 Obtain SSL Certificate

```bash
# Get SSL certificate (Certbot will automatically configure Nginx)
sudo certbot --nginx -d kathaingo.com -d www.kathaingo.com

# Follow the prompts:
# - Enter your email address
# - Agree to terms of service
# - Choose whether to redirect HTTP to HTTPS (select redirect)
```

### 11.3 Test Auto-Renewal

```bash
# Test certificate renewal
sudo certbot renew --dry-run

# Certbot auto-renewal is handled by systemd timer, check it:
sudo systemctl status certbot.timer
```

### 11.4 Update Nginx Configuration for SSL

After Certbot runs, edit the configuration to add security headers:

```bash
sudo nano /etc/nginx/sites-available/kathaingo.com
```

Add these lines inside the SSL server block (after the SSL certificate lines):

```nginx
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
```

Restart Nginx:
```bash
sudo nginx -t
sudo systemctl restart nginx
```

---

## Step 12: Setup Cron Jobs for Laravel Scheduler

```bash
# Edit crontab for www-data user
sudo crontab -u www-data -e

# Add this line:
* * * * * cd /var/kathaingo && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 13: Setup Queue Workers (Optional but Recommended)

### 13.1 Install Supervisor

```bash
sudo apt install -y supervisor
```

### 13.2 Create Supervisor Configuration

```bash
sudo nano /etc/supervisor/conf.d/kathaingo-worker.conf
```

Add this configuration:

```ini
[program:kathaingo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/kathaingo/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/kathaingo/storage/logs/worker.log
stopwaitsecs=3600
```

### 13.3 Start Supervisor

```bash
# Reread configuration
sudo supervisorctl reread

# Update supervisor
sudo supervisorctl update

# Start workers
sudo supervisorctl start kathaingo-worker:*

# Check status
sudo supervisorctl status
```

---

## Step 14: Final Verification and Testing

### 14.1 Check All Services

```bash
# Check Nginx
sudo systemctl status nginx

# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check PostgreSQL
sudo systemctl status postgresql

# Check Supervisor (if configured)
sudo systemctl status supervisor
```

### 14.2 Test Application

1. **Visit your website**: https://kathaingo.com
2. **Test login**: Use the admin credentials from your `pass` file
   - Email: admin@kathaingo.com
   - Password: password
3. **Check for errors**: Monitor logs for any issues

### 14.3 Monitor Logs

```bash
# Laravel logs
tail -f /var/kathaingo/storage/logs/laravel.log

# Nginx access logs
sudo tail -f /var/log/nginx/kathaingo.com-access.log

# Nginx error logs
sudo tail -f /var/log/nginx/kathaingo.com-error.log

# PHP-FPM logs
sudo tail -f /var/log/php8.2-fpm.log
```

---

## Step 15: Post-Deployment Optimizations

### 15.1 Enable Redis Cache (Optional but Recommended)

```bash
# Install Redis
sudo apt install -y redis-server php8.2-redis

# Start Redis
sudo systemctl start redis-server
sudo systemctl enable redis-server

# Update .env
nano /var/kathaingo/.env
```

Update cache settings:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

```bash
# Clear and recache configuration
php artisan config:clear
php artisan config:cache
```

### 15.2 Setup Log Rotation

```bash
# Create logrotate configuration
sudo nano /etc/logrotate.d/laravel
```

Add this:
```
/var/kathaingo/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

### 15.3 Setup Monitoring (Optional)

Install monitoring tools:
```bash
# Install htop for process monitoring
sudo apt install -y htop

# Install netdata for real-time monitoring (optional)
bash <(curl -Ss https://my-netdata.io/kickstart.sh)
```

---

## Troubleshooting Common Issues

### Issue 1: 502 Bad Gateway

**Solution:**
```bash
# Check PHP-FPM is running
sudo systemctl status php8.2-fpm

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm

# Check socket file exists
ls -la /var/run/php/php8.2-fpm.sock
```

### Issue 2: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
tail -50 /var/kathaingo/storage/logs/laravel.log

# Fix permissions
sudo chown -R www-data:www-data /var/kathaingo/storage
sudo chmod -R 775 /var/kathaingo/storage

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Issue 3: CSS/JS Not Loading

**Solution:**
```bash
# Rebuild assets
cd /var/kathaingo
npm run build

# Clear Laravel caches
php artisan cache:clear
php artisan view:clear

# Check file permissions
sudo chown -R www-data:www-data /var/kathaingo/public
```

### Issue 4: Database Connection Failed

**Solution:**
```bash
# Test PostgreSQL connection
psql -U kathaingo_user -d kathaingo -h 127.0.0.1

# Check PostgreSQL is running
sudo systemctl status postgresql

# Verify .env database credentials
cat /var/kathaingo/.env | grep DB_
```

---

## Deployment Updates (Future Updates)

When you need to deploy code updates:

```bash
# Navigate to project
cd /var/kathaingo

# Pull latest changes
git pull origin main

# Install any new dependencies
composer install --optimize-autoloader --no-dev
npm ci
npm run build

# Run migrations
php artisan migrate --force

# Clear and recache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:cache
php artisan config:cache
php artisan view:cache

# Restart services
sudo systemctl restart php8.2-fpm
sudo supervisorctl restart kathaingo-worker:*
```

---

## Security Checklist

- [ ] Change default database passwords
- [ ] Change admin user password from default
- [ ] Disable directory listing in Nginx
- [ ] Setup fail2ban for SSH protection
- [ ] Enable firewall (UFW)
- [ ] Keep system packages updated
- [ ] Regular database backups
- [ ] Monitor logs regularly
- [ ] Use strong passwords
- [ ] Keep Laravel and dependencies updated

---

## Backup Strategy

### Database Backup

```bash
# Create backup script
sudo nano /usr/local/bin/backup-kathaingo.sh
```

Add:
```bash
#!/bin/bash
BACKUP_DIR="/var/backups/kathaingo"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
pg_dump -U kathaingo_user kathaingo > $BACKUP_DIR/database_$DATE.sql

# Backup uploads (if any)
tar -czf $BACKUP_DIR/uploads_$DATE.tar.gz /var/kathaingo/storage/app/public

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

```bash
# Make executable
sudo chmod +x /usr/local/bin/backup-kathaingo.sh

# Add to crontab (daily at 2 AM)
sudo crontab -e
```

Add:
```
0 2 * * * /usr/local/bin/backup-kathaingo.sh
```

---

## Congratulations! 🎉

Your Laravel application is now deployed and running on production at **https://kathaingo.com**

For support and monitoring, regularly check:
- Application logs: `/var/kathaingo/storage/logs/`
- Nginx logs: `/var/log/nginx/`
- System resources: `htop`
