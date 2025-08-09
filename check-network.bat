@echo off
echo ========================================
echo   Network Configuration Checker
echo ========================================
echo.

echo Your IP Addresses:
ipconfig | findstr "IPv4"

echo.
echo Testing network connectivity...
ping -n 1 8.8.8.8 > nul
if %errorlevel% == 0 (
    echo ✓ Internet connection: OK
) else (
    echo ✗ Internet connection: FAILED
)

echo.
echo Checking if port 8000 is available...
netstat -an | findstr ":8000" > nul
if %errorlevel% == 0 (
    echo ✗ Port 8000 is already in use
    echo   Active connections on port 8000:
    netstat -an | findstr ":8000"
) else (
    echo ✓ Port 8000 is available
)

echo.
echo Windows Firewall Status:
netsh advfirewall show allprofiles state

echo.
echo ========================================
echo   Network Setup Instructions:
echo ========================================
echo.
echo 1. Make sure other devices are on the same WiFi network
echo 2. Use these URLs from other devices:
echo    - http://10.3.57.0:8000 (WiFi network)
echo    - http://192.168.56.1:8000 (VirtualBox network)
echo.
echo 3. If connection fails, run as Administrator:
echo    netsh advfirewall firewall add rule name="Laravel Dev Server" dir=in action=allow protocol=TCP localport=8000
echo.
pause
