<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Connexion - Gestion de Présence Universitaire</title>
    <link rel="stylesheet" href="assets/css/style.css" />
    <style>
      :root {
        --primary-color: #0066cc;
        --secondary-color: #005bb5;
        --text-color: #333;
        --bg-color: #f0f2f5;
        --card-bg: rgba(255, 255, 255, 0.9);
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        --focus-shadow: 0 0 0 3px rgba(0, 102, 204, 0.2);
        --hover-shadow: 0 8px 30px rgba(0, 102, 204, 0.25);
      }

      /* Dark mode styles */
      body.dark-mode {
        --text-color: #f0f0f0;
        --bg-color: #121212;
        --card-bg: rgba(30, 30, 30, 0.9);
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
      }

      body {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        background-color: var(--bg-color);
        color: var(--text-color);
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out;
      }

      /* Keyframes for animations */
      @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .toggle-switch {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 14px;
        color: var(--text-color);
      }
      
      main {
        width: 100%;
        max-width: 450px; /* Adjusted form size */
        padding: 20px;
        box-sizing: border-box;
      }

      .form-container {
        background: var(--card-bg);
        backdrop-filter: blur(8px);
        padding: 40px;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        transition: all 0.4s ease-in-out;
        animation: fadeIn 0.8s ease-out;
      }
      
      .form-container.shake {
        animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
      }

      .form-container h2 {
        margin-bottom: 25px;
        text-align: center;
        font-size: 2rem;
      }

      .input-group {
        margin-bottom: 20px;
      }

      label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        transition: color 0.4s ease-in-out;
      }

      input[type="text"],
      input[type="password"],
      select {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        outline: none;
        font-size: 1rem;
        transition: border-color 0.3s, box-shadow 0.3s, transform 0.2s;
        background-color: rgba(255, 255, 255, 0.6);
      }

      input:focus,
      select:focus {
        border-color: var(--primary-color);
        box-shadow: var(--focus-shadow);
        transform: translateY(-2px);
      }
      
      button[type="submit"] {
        width: 100%;
        padding: 14px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background-color 0.3s ease, transform 0.2s ease, box-shadow 0.3s;
      }

      button:hover {
        background-color: var(--secondary-color);
        transform: translateY(-3px);
        box-shadow: var(--hover-shadow);
      }

      @media (max-width: 480px) {
        .form-container {
          padding: 25px;
          border-radius: 12px;
        }
      }
    </style>
  </head>
  <body>
    <label class="toggle-switch" title="Basculer mode clair/sombre">
      <input type="checkbox" id="modeToggle" />
      Mode sombre
    </label>

    <main>
      <div class="form-container">
        <h2>Connexion</h2>
        <form action="php/connexion.php" method="post" id="loginForm">
          <div class="input-group">
            <label for="matricule">Matricule</label>
            <input type="text" id="matricule" name="matricule" required placeholder="Votre matricule" />
          </div>

          <div class="input-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required placeholder="Votre mot de passe" />
          </div>

          <div class="input-group">
            <label for="role">Rôle</label>
            <select name="role" id="role" required>
              <option value="" disabled selected>Choisissez votre rôle</option>
              <option value="etudiant">Étudiant</option>
              <option value="personnel">Personnel</option>
              <option value="admin">Administrateur</option>
            </select>
          </div>

          <button type="submit">Se connecter</button>
        </form>
      </div>
    </main>
    
    <script>
      // JavaScript pour le mode sombre et les animations
      const modeToggle = document.getElementById('modeToggle');
      const body = document.body;
      const form = document.getElementById('loginForm');

      modeToggle.addEventListener('change', () => {
        body.classList.toggle('dark-mode');
      });

      form.addEventListener('submit', (e) => {
        // Validation simple
        const matricule = document.getElementById('matricule').value;
        const password = document.getElementById('password').value;
        const role = document.getElementById('role').value;

        if (!matricule || !password || !role) {
          e.preventDefault();
          const formContainer = document.querySelector('.form-container');
          formContainer.classList.add('shake');
          
          // Remove the shake class after the animation ends
          formContainer.addEventListener('animationend', () => {
            formContainer.classList.remove('shake');
          }, { once: true });
        }
      });
    </script>
  </body>
</html>