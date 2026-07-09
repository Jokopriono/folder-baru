@echo off
title Buka Website MTs Muhammadiyah Bireuen

echo Memeriksa layanan XAMPP...

:: Cek dan start Apache jika belum jalan
tasklist /FI "IMAGENAME eq httpd.exe" 2>NUL | find /I "httpd.exe" >NUL
if errorlevel 1 (
    echo Menjalankan Apache...
    start "" /B "C:\xampp\apache_start.bat"
    timeout /t 3 /nobreak >NUL
) else (
    echo Apache sudah berjalan.
)

:: Cek dan start MySQL jika belum jalan
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if errorlevel 1 (
    echo Menjalankan MySQL...
    start "" /B "C:\xampp\mysql_start.bat"
    timeout /t 3 /nobreak >NUL
) else (
    echo MySQL sudah berjalan.
)

:: Ambil IP Address WiFi
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr /v "127.0.0.1"') do (
    set IPADDR=%%a
    goto :found
)
:found
set IPADDR=%IPADDR: =%

echo.
echo =====================================================
echo   Website MTs Muhammadiyah Bireuen
echo =====================================================
echo.
echo   Di Laptop  : http://localhost/mts-bireuen/
echo   Di HP/Tablet: http://%IPADDR%/mts-bireuen/
echo   (HP harus terhubung ke WiFi yang sama)
echo.
echo =====================================================
echo.

:: Buka di browser
start "" "http://localhost/mts-bireuen/index.html"

pause
exit
