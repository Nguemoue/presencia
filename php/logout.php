<?php
session_start();
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Déconnexion</title>
  <meta http-equiv="refresh" content="4;url=../login.html"> <!-- Redirection automatique -->
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to right, #d4fc79, #96e6a1);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .box {
      background: #fff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      text-align: center;
    }
    h2 {
      color: #2ecc71;
    }
    p {
      color: #555;
    }
  </style>
</head>
<body>
  <div class="box">
    <h2>Déconnexion réussie ✅</h2>
    <p>Vous allez être redirigé vers la page de connexion...</p>
    <p><a href="../login.html">Cliquez ici si vous n'êtes pas redirigé automatiquement.</a></p>
  </div>
</body>
</html>
