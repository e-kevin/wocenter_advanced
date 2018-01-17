@echo off

rem -------------------------------------------------------------
rem  WoCenter command line bootstrap script for Windows.
rem -------------------------------------------------------------

@setlocal

set YII_PATH=%~dp0

if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%YII_PATH%cmd" %*

@endlocal
