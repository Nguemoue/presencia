# 🎭 Face Recognition Microservice (Flask + MySQL)

Ce projet est un microservice Flask qui :
- Permet d’enregistrer un visage avec un nom.
- Permet de comparer un visage uploadé avec ceux enregistrés en base de données (MySQL).

---

## 🚀 Installation (Windows)

### 1. Pré-requis
- **Python 3.10+**
- **MySQL** (local ou via Docker)
- **Visual Studio Build Tools** (si `dlib` ne s’installe pas correctement)
- **CMake** (pour compiler `dlib` et `face_recognition`)

Téléchargement CMake : https://cmake.org/download/

### 2. Cloner le projet
```bash
git clone https://github.com/toncompte/face_service.git
cd face_service
