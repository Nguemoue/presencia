
<?php
session_start();
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header('Location: ../login.html?error=acces');
    exit;
}
require_once '../includes/config.php';

$id = $_GET['id'];
if (!$id) { header('Location: liste_utilisateurs.php?error=id'); exit; }

// Récupérer les données de l’utilisateur
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: _utilisateurs.php?error=notfound'); exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier Utilisateur</title>
  <link rel="stylesheet" href="../assets/css/style.css" />
</head>
<body>
  <label class="toggle-switch" title="Basculer mode clair/sombre">
    <input type="checkbox" id="modeToggle">
    Mode sombre
  </label>
  <main>
    <div class="form-container">
      <h2>Modifier un utilisateur</h2>
      <form action="update_utilisateur.php" method="post" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="id" value="<?= $user['id'] ?>" />
        <label for="nom">Nom</label>
        <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($user['nom']) ?>" required />

        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required />

        <label for="email">Adresse email</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required />

        <label for="matricule">Matricule</label>
        <input type="text" name="matricule" id="matricule" value="<?= htmlspecialchars($user['matricule']) ?>" required />

        <label for="type">Rôle</label>
        <select name="type" id="type" required>
          <option value="etudiant"<?= $user['type']=='etudiant'?' selected':''; ?>>Étudiant</option>
          <option value="personnel"<?= $user['type']=='personnel'?' selected':''; ?>>Personnel</option>
          <option value="admin"<?= $user['type']=='admin'?' selected':''; ?>>Administrateur</option>
        </select>
       </select>

            <label for="classe_id">Classe / Service</label>
            <select name="classe_id" id="classe_id" required>
                <option value="" disabled selected>Choisir une classe</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= htmlspecialchars($classe['id']) ?>">
                        <?= htmlspecialchars($classe['nom_classe']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
         <label for="photo_reference">Photo référence (laisser vide pour ne pas changer)</label>
        <input type="file" name="photo_reference" id="photo_reference" accept="image/*" />

        <?php if ($user['photo_reference']): ?>
          <img src="../uploads/<?= htmlspecialchars($user['photo_reference']) ?>" style="height:40px;border-radius:8px;">
        <?php endif; ?>

        <button type="submit">Modifier</button>
      </form>
    </div>
  </main>
  <script src="../assets/js/app.js"></script>
</body>
</html>
