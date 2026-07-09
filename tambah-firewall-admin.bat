@echo off
:: File ini harus dijalankan sebagai Administrator
:: Klik kanan → "Run as administrator"

NET SESSION >nul 2>&1
IF %ERRORLEVEL% NEQ 0 (
    echo Harap jalankan file ini sebagai Administrator!
    echo Klik kanan pada file ini, lalu pilih "Run as administrator"
    pause
    exit /b
)

echo Menambahkan aturan firewall untuk Apache port 80...
netsh advfirewall firewall add rule name="XAMPP Apache Port 80" dir=in action=allow protocol=TCP localport=80 profile=private,domain

echo.
echo BERHASIL! HP di jaringan WiFi yang sama sekarang bisa mengakses website.
echo.
pause
