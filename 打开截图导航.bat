@echo off
chcp 65001 >nul
cd /d "%~dp0"
if not exist "截图导航.html" (
    echo 找不到 截图导航.html
    pause
    exit /b 1
)
start "" "%~dp0截图导航.html"
