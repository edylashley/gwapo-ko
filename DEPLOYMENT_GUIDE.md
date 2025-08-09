# 🚀 Budget Control System - Deployment Guide

## 📋 Prerequisites
- PHP 8.2 or higher
- MySQL/MariaDB
- Composer
- Node.js & NPM (for assets)

## 🌐 Option 1: Local Network Access (Quick Setup)

### Step 1: Configure Environment
1. Update `.env` file:
   ```env
   APP_URL=http://10.3.57.0:8000
   APP_ENV=production
   APP_DEBUG=false
   ```

### Step 2: Deploy Locally
1. Run the deployment script:
   ```bash
   deploy-local.bat
   ```

2. Or manually:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   php artisan migrate --force
   php artisan serve --host=0.0.0.0 --port=8000
   ```

### Step 3: Access from Other Devices
- **Same WiFi Network**: http://10.3.57.0:8000
- **VirtualBox Network**: http://192.168.56.1:8000

## 🖥️ Option 2: XAMPP Integration

### Step 1: Move to XAMPP htdocs
1. Copy project to: `C:\xampp\htdocs\budget_control`
2. Update `.env`:
   ```env
   APP_URL=http://localhost/budget_control/public
   ```

### Step 2: Configure Virtual Host
1. Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "C:/xampp/htdocs/budget_control/public"
       ServerName budget-control.local
       <Directory "C:/xampp/htdocs/budget_control/public">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

2. Edit `C:\Windows\System32\drivers\etc\hosts`:
   ```
   127.0.0.1 budget-control.local
   10.3.57.0 budget-control.local
   ```

### Step 3: Access
- Local: http://budget-control.local
- Network: http://10.3.57.0/budget_control/public

## ☁️ Option 3: Cloud Deployment

### Heroku Deployment
1. Install Heroku CLI
2. Create `Procfile`:
   ```
   web: vendor/bin/heroku-php-apache2 public/
   ```
3. Deploy:
   ```bash
   heroku create budget-control-app
   heroku addons:create cleardb:ignite
   git push heroku main
   ```

### DigitalOcean/AWS/VPS
1. Use Laravel Forge or manual setup
2. Configure Nginx/Apache
3. Set up SSL certificate
4. Configure database

## 🔧 Production Configuration

### Environment Variables (.env)
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database
DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_PORT=3306
DB_DATABASE=budget_control
DB_USERNAME=your-username
DB_PASSWORD=your-password

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Security Checklist
- [ ] Set `APP_DEBUG=false`
- [ ] Use strong `APP_KEY`
- [ ] Configure HTTPS
- [ ] Set up firewall rules
- [ ] Regular backups
- [ ] Update dependencies

## 📱 Mobile Access
Once deployed, the system is fully responsive and accessible on:
- Smartphones
- Tablets
- Desktop computers
- Any device with a web browser

## 🔒 Network Security
- Configure Windows Firewall to allow port 8000
- Use VPN for remote access
- Consider authentication middleware
- Regular security updates

## 📞 Support
For deployment issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify database connection
3. Ensure proper file permissions
4. Check PHP extensions
