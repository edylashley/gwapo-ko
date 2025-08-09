@echo off
echo ========================================
echo   Budget Control - Production Deploy
echo ========================================
echo.

echo [1/8] Installing/Updating Composer dependencies...
composer install --optimize-autoloader --no-dev

echo.
echo [2/8] Installing/Updating NPM dependencies...
npm install

echo.
echo [3/8] Building production assets...
npm run build

echo.
echo [4/8] Clearing all caches...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo.
echo [5/8] Running database migrations...
php artisan migrate --force

echo.
echo [6/8] Optimizing application...
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo.
echo [7/8] Creating storage link...
php artisan storage:link

echo.
echo [8/8] Setting up production environment...
echo.

echo ========================================
echo   Production deployment completed!
echo.
echo   Access URLs:
echo   - Local: http://localhost:8000
echo   - WiFi Network: http://10.3.57.0:8000
echo   - VirtualBox: http://192.168.56.1:8000
echo ========================================
echo.

echo Starting production server...
php artisan serve --host=0.0.0.0 --port=8000
