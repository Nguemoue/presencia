<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Administrateur</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
  
  <div class="sidebar">
    <div class="profile">
      <img src="../assets/img/admin_avatar.png" alt="Admin" class="avatar">
      <span class="admin-name">Administrateur</span>
    </div>
    <nav>
      <a href="liste_utilisateurs.php" class="nav-link"><i class="fas fa-users"></i> Utilisateurs</a>
      <a href="liste_classes.php" class="nav-link"><i class="fas fa-chalkboard"></i> Classes</a>
      <a href="liste_classes.php" class="nav-link"><i class="fas fa-briefcase"></i> Services</a>
      <a href="dash.php" class="nav-link"><i class="fas fa-calendar-check"></i> Présences</a>
      <a href="chatt.php" class="nav-link"><i class="fas fa-cogs"></i> chatbot</a>
      <a href="logout.php" class="nav-link logout"><i class="fas fa-sign-out-alt"></i> Déconnexion</a>
    </nav>
  </div>

  <div class="main-content" id="mainContent">
    <header>
      <button id="toggleSidebar"><i class="fas fa-bars"></i></button>
      <h1>Dashboard Administrateur</h1>
    </header>
    <section class="dashboard-welcome">
      <h2>Bienvenue sur le panel d’administration</h2>
      <p>
        Gérez toutes les fonctionnalités de votre plateforme en toute simplicité.<br>
        Sélectionnez une rubrique dans le menu à gauche.
      </p>
      <div class="dashboard-cards">
        <div class="card" onclick="redirect('liste_utilisateurs.php')">
          <i class="fas fa-users"></i>
          <span>Utilisateurs</span>
        </div>
        <div class="card" onclick="redirect('liste_classes.php')">
          <i class="fas fa-chalkboard"></i>
          <span>Classes</span>
        </div>
        <div class="card" onclick="redirect('liste_classes.php')">
          <i class="fas fa-briefcase"></i>
          <span>Services</span>
        </div>
        <div class="card" onclick="redirect('dash.php')">
          <i class="fas fa-calendar-check"></i>
          <span>Présences</span>
        </div>
        <div class="card" onclick="redirect('chatt.php')">
          <i class="fas fa-cogs"></i>
          <span>chatbot</span>
        </div>
      </div>
    </section>
  </div>
<script>
function redirect(url) {
  // Animation fade out avant redirection
  const mainContent = document.getElementById('mainContent');
  mainContent.classList.add('fade-out');
  setTimeout(function() { window.location.href = url; }, 350);
}

document.getElementById('toggleSidebar').onclick = function() {
  document.body.classList.toggle('sidebar-collapsed');
};
</script>
</body>
</html>
