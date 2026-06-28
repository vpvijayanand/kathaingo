# Laravel Deployment Instructions for kathaingo.com

## Server Setup Steps

### 1. Upload the Nginx Configuration

Copy the `kathaingo.com.conf` file to your Ubuntu server:

```bash
# On your local machine (Windows)
scp kathaingo.com.conf user@your-server:/tmp/

# On your Ubuntu server
sudo mv /tmp/kathaingo.com.conf /etc/nginx/sites-available/kathaingo.com
```

### 2. Enable the Site

```bash
# Create symbolic link to enable the site
sudo ln -s /etc/nginx/sites-available/kathaingo.com /etc/nginx/sites-enabled/

# Remove default site if it exists
sudo rm /etc/nginx/sites-enabled/default
```

### 3. Verify PHP-FPM Version

Check your installed PHP version and update the config if needed:

```bash
# Check PHP version
php -v

# Update the socket path in the config file if needed
# For PHP 8.1: /var/run/php/php8.1-fpm.sock
# For PHP 8.2: /var/run/php/php8.2-fpm.sock
# For PHP 8.3: /var/run/php/php8.3-fpm.sock

sudo nano /etc/nginx/sites-available/kathaingo.com
# Update line 58: fastcgi_pass unix:/var/run/php/phpX.X-fpm.sock;
```

### 4. Configure SSL Certificate (Let's Encrypt)

If you haven't set up SSL yet, install certbot:

```bash
# Install certbot
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d kathaingo.com -d www.kathaingo.com

# Certbot will automatically update your nginx config with the correct SSL paths
```

If you already have SSL certificates elsewhere, update these lines in the config:
```nginx
ssl_certificate /path/to/your/fullchain.pem;
ssl_certificate_key /path/to/your/privkey.pem;
```

### 5. Set Proper Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/kathaingo

# Set permissions for directories
sudo find /var/kathaingo -type d -exec chmod 755 {} \;

# Set permissions for files
sudo find /var/kathaingo -type f -exec chmod 644 {} \;

# Make storage and bootstrap/cache writable
sudo chmod -R 775 /var/kathaingo/storage
sudo chmod -R 775 /var/kathaingo/bootstrap/cache
```

### 6. Configure Laravel Environment

```bash
# Navigate to your project
cd /var/kathaingo

# Copy .env file if not already present
cp .env.example .env

# Edit .env file
nano .env

# Update these values:
# APP_ENV=production
# APP_DEBUG=false
# APP_URL=https://kathaingo.com
# DB_DATABASE=kathaingo
# DB_USERNAME=your_db_user
# DB_PASSWORD=your_db_password

# Generate application key (if not set)
php artisan key:generate

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Run seeders (if needed)
php artisan db:seed --force
```

### 7. Install Dependencies

```bash
# Install Composer dependencies (production only)
composer install --optimize-autoloader --no-dev

# Build frontend assets (if not already built)
npm ci
npm run build
```

### 8. Test and Restart Nginx

```bash
# Test nginx configuration
sudo nginx -t

# If test passes, restart nginx
sudo systemctl restart nginx

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm  # Adjust version as needed
```

### 9. Enable Services on Boot

```bash
sudo systemctl enable nginx
sudo systemctl enable php8.2-fpm  # Adjust version as needed
```

## Troubleshooting

### Check Nginx Error Logs
```bash
sudo tail -f /var/log/nginx/kathaingo.com-error.log
```

### Check PHP-FPM Logs
```bash
sudo tail -f /var/log/php8.2-fpm.log
```

### Check Laravel Logs
```bash
tail -f /var/kathaingo/storage/logs/laravel.log
```

### Common Issues

1. **500 Error**: Check storage permissions
   ```bash
   sudo chmod -R 775 /var/kathaingo/storage
   sudo chown -R www-data:www-data /var/kathaingo/storage
   ```

2. **404 on all routes**: Ensure nginx root points to `/var/kathaingo/public`

3. **CSS/JS not loading**: Run `npm run build` and clear cache:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

4. **Database connection error**: Check `.env` database credentials and ensure PostgreSQL is running:
   ```bash
   sudo systemctl status postgresql
   ```

## SSL Certificate Auto-Renewal

If using Let's Encrypt, certbot auto-renewal should be set up automatically. Test it:

```bash
sudo certbot renew --dry-run
```

## Performance Optimization

### Enable OPcache
Edit PHP configuration:
```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Add/update these values:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

### Queue Workers (Optional)
If using queues, set up a supervisor:

```bash
sudo apt install supervisor

# Create supervisor config
sudo nano /etc/supervisor/conf.d/kathaingo-worker.conf
```

Add:
```ini
[program:kathaingo-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/kathaingo/artisan queue:work --sleep=3 --tries=3 --max-time=3600
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

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start kathaingo-worker:*
```

## Monitoring

Monitor your site:
- Check https://kathaingo.com/ in browser
- Monitor logs regularly
- Set up uptime monitoring (e.g., UptimeRobot, Pingdom)
