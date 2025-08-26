@echo off
title Installation du microservice Flask
echo.
echo ===================================
echo   Installation du microservice Flask
echo ===================================
echo.

:: Vérifier si Python est installé
python --version >nul 2>&1
if errorlevel 1 (
    echo [ERREUR] Python n'est pas installé , Installe Python 3.10  avant de continuer.
    exit /b 1
)

:: Supprimer l'ancien environnement virtuel s'il existe
if exist venv (
    echo [INFO] Suppression de l'ancien environnement virtuel...
    rmdir /s /q venv
)

:: Créer un nouvel environnement virtuel
echo [INFO] Création de l'environnement virtuel (venv)...
python -m venv venv

:: Activer l'environnement virtuel
echo [INFO] Activation de l'environnement virtuel...
call venv\Scripts\activate

:: Mettre pip à jour
echo [INFO] Mise à jour de pip...
python -m pip install --upgrade pip

:: Installer dlib via wheel local
if exist "%~dp0dlib-19.22.99-cp310-cp310-win_amd64.whl" (
    echo [INFO] Installation de dlib depuis le fichier local .whl...
    pip install "%~dp0dlib-19.22.99-cp310-cp310-win_amd64.whl"
)

:: Vérifier requirements.txt
if not exist requirements.txt (
    echo [ERREUR] Le fichier requirements.txt est introuvable.
    pause
    exit /b 1
)

:: Installer les dépendances
echo [INFO] Installation des dépendances depuis requirements.txt...
pip install -r requirements.txt

echo.
echo ===================================
echo   Installation terminée avec succès
echo ===================================
echo.
echo Pour lancer le service :
echo   1. activer l'environnement : venv\Scripts\activate
echo   2. démarrer le serveur Flask : python app.py
echo.
echo ===================================
pause
