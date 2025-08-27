@echo off
title Installation du microservice Flask
echo ====================================
echo   Lancement du Projet Flask
echo ====================================
echo.

:: Activer l'environnement virtuel
echo [INFO] Activation de l'environnement virtuel...
call venv\Scripts\activate

call  python app.py
