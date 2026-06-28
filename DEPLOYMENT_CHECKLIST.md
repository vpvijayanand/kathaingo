# Quick Deployment Checklist

Use this checklist alongside the [PRODUCTION_DEPLOYMENT_GUIDE.md](file:///c:/apps/katiango/PRODUCTION_DEPLOYMENT_GUIDE.md)

## Pre-Deployment
- [ ] Fresh Ubuntu 22.04 LTS server ready
- [ ] Domain (kathaingo.com) DNS points to server IP
- [ ] SSH access configured
- [ ] Root or sudo access available

## Server Setup
- [ ] System packages updated (`apt update && apt upgrade`)
- [ ] Essential tools installed (`curl wget git unzip`)
- [ ] Non-root user created (optional but recommended)

## PHP Installation
- [ ] PHP 8.2 repository added
- [ ] PHP 8.2 and extensions installed
- [ ] PHP configuration optimized (`/etc/php/8.2/fpm/php.ini`)
- [ ] PHP-FPM service running

## Dependencies
- [ ] Composer installed globally
- [ ] Node.js 20.x installed
- [ ] npm available

## Database
- [ ] PostgreSQL installed
- [ ] PostgreSQL service running
- [ ] Database `kathaingo` created
- [ ] Database user `kathaingo_user` created
- [ ] User granted all privileges

## Web Server
- [ ] Nginx installed
- [ ] Nginx service running
- [ ] Firewall configured (ports 80, 443, 22)

## Application
- [ ] Repository cloned to `/var/kathaingo`
- [ ] Ownership set to `www-data`
- [ ] Composer dependencies installed (`composer install --no-dev`)
- [ ] Node dependencies installed (`npm ci`)
- [ ] Assets built (`npm run build`)

## Configuration
- [ ] `.env` file created and configured
- [ ] APP_KEY generated
- [ ] Database credentials set in `.env`
- [ ] APP_DEBUG set to `false`
- [ ] APP_ENV set to `production`
- [ ] Migrations run (`php artisan migrate --force`)
- [ ] Seeders run (`php artisan db:seed --force`)
- [ ] Laravel optimizations run (config, route, view cache)

## Permissions
- [ ] Storage directory writable (`775`)
- [ ] Bootstrap/cache directory writable (`775`)
- [ ] All files owned by `www-data:www-data`

## Nginx
- [ ] Virtual host configuration created
- [ ] Site enabled (symlink created)
- [ ] Default site disabled
- [ ] Configuration tested (`nginx -t`)
- [ ] Nginx restarted

## SSL Certificate
- [ ] Certbot installed
- [ ] SSL certificate obtained
- [ ] Auto-renewal configured
- [ ] HTTPS redirect working
- [ ] Security headers added

## Background Tasks
- [ ] Cron job for Laravel scheduler added
- [ ] Supervisor installed (for queues)
- [ ] Queue workers configured
- [ ] Supervisor running

## Testing
- [ ] Website accessible via HTTPS
- [ ] Admin login working
- [ ] Database connection working
- [ ] Static assets loading (CSS/JS)
- [ ] No errors in Laravel logs

## Optional Optimizations
- [ ] Redis installed and configured
- [ ] Log rotation configured
- [ ] Monitoring tools installed
- [ ] Backup script created

## Security
- [ ] Default passwords changed
- [ ] Admin password changed
- [ ] Firewall enabled
- [ ] fail2ban configured (optional)

## Documentation
- [ ] Server details documented
- [ ] Database credentials saved securely
- [ ] Backup procedure documented

---

## Quick Commands Reference

### Check Service Status
```bash
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status postgresql
sudo supervisorctl status
```

### View Logs
```bash
tail -f /var/kathaingo/storage/logs/laravel.log
sudo tail -f /var/log/nginx/kathaingo.com-error.log
```

### Deploy Updates
```bash
cd /var/kathaingo
git pull origin main
composer install --no-dev
npm ci && npm run build
php artisan migrate --force
php artisan config:cache
sudo systemctl restart php8.2-fpm
```

### Emergency Fixes
```bash
# Fix permissions
sudo chown -R www-data:www-data /var/kathaingo/storage
sudo chmod -R 775 /var/kathaingo/storage

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Restart services
sudo systemctl restart nginx php8.2-fpm
```
