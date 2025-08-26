@echo off
title Installation du microservice Flask
echo ====================================
echo   Installation du microservice Flask
echo ====================================
echo.

:: Vérifier si Python est installé
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] Python n'est pas installé.
    echo Installe Python 3.12 (64 bits) avant de continuer.
    pause
    exit /b 1
)

:: Supprimer l'ancien environnement s'il existe
if exist venv (
    echo [INFO] Suppression de l'ancien environnement virtuel...
    rmdir /s /q venv
)

:: Créer un environnement virtuel
echo [INFO] Création de l'environnement virtuel (venv)...
python -m venv venv

:: Activer l'environnement virtuel
echo [INFO] Activation de l'environnement virtuel...
call venv\Scripts\activate

:: Mettre pip à jour
echo [INFO] Mise à jour de pip...
python -m pip install --upgrade pip

:: Installer dlib via le fichier wheel local
echo [INFO] Installation de dlib depuis le fichier local .whl...
pip install "%~dp0dlib-19.24.2-cp312-cp312-win_amd64.whl"

:: Installer les dépendances restantes
echo [INFO] Installation des dépendances depuis requirements.txt...
pip install -r requirements.txt

echo.
echo ====================================
echo   Installation terminée avec succès !
echo ====================================
echo.
echo Pour lancer le service :
echo.
echo   1. activer l'environnement :
echo      venv\Scripts\activate
echo.
echo   2. lancer le serveur Flask :
echo      python app.py
echo.
echo ====================================
pause
