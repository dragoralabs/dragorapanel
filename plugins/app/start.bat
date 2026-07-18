@echo off
title Dragora Panel Plugin Builder
cd /d "%~dp0"

set SRC=..\test_plugin
if not "%1"=="" set SRC=%1

if "%2"=="" (
  for %%F in ("%SRC%") do set DEST=%%~nxF.dpp
) else (
  set DEST=%2
)

echo.
echo  ╔══════════════════════════════════════╗
echo  ║   Dragora Panel Plugin Builder      ║
echo  ╚══════════════════════════════════════╝
echo.
echo  Source: %SRC%
echo  Output: %DEST%
echo.

php "%~dp0dpp-build.php" "%SRC%" "%DEST%"
if errorlevel 1 (
    echo [!] Build failed.
    pause
    exit /b 1
)
echo.
echo  [✓] Built: %DEST%
echo.
pause
