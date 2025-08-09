@echo off
echo ========================================
echo   Budget Control System - Local Deploy
echo ========================================
echo.

echo [1/4] Clearing Laravel cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo.
echo [2/4] Optimizing application...
php artisan config:cache
php artisan route:cache

echo.
echo [3/4] Running database migrations...
php artisan migrate --force

echo.
echo [4/4] Starting server on all network interfaces...
echo.
echo ========================================
echo   Server will be accessible at:
echo   - Local: http://localhost:8000
echo   - Network: http://10.3.57.0:8000
echo   - Network: http://192.168.56.1:8000
echo ========================================
echo.
echo Press Ctrl+C to stop the server
echo.

php artisan serve --host=0.0.0.0 --port=8000
