
<?php
session_start();
// sécurise accès admin

require_once '../includes/config.php';

$id = $_GET['id'];
$nom_classe = '';
$description = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
    $stmt->execute([$id]);
    $classe = $stmt->fetch();
    if (!$classe) $id = null;
    else {
        $nom_classe = $classe['nom_classe'];
        $description = $classe['description'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title><?= $id ? "Modifier" : "Ajouter" ?> une classe</title>
<style>
body { font-family: Arial,sans-serif; margin: 2rem; background: #f6f6ef; }
.form-container {
  max-width: 500px; margin:auto; background: #fff; padding: 1.5rem; border-radius: 12px; box-shadow: 0 10px 30px rgba(217,231,108,0.25);
}
label { display:block; margin: 1rem 0 0.3rem; font-weight: 600; }
input[type=text], textarea {
  width: 100%; padding: 8px; border-radius: 8px; border: 1px solid #ccc; box-sizing: border-box;
  font-size: 1rem;
}
textarea { resize: vertical; min-height: 100px; }
button {
  margin-top: 1rem; padding: 10px 16px; background: #d9e76c; border:none; border-radius: 50px; cursor:pointer; font-weight: 700; font-size: 1.1rem;
  transition: background-color 0.3s ease;
}
button:hover { background-color: #c7db4d; }
a { display: block; margin-top: 1rem; text-align: center; text-decoration:none; color: #666; }
a:hover { text-decoration: underline; }
</style>
</head>
<body>
<div class="form-container">
  <h2><?= $id ? "Modifier" : "Ajouter" ?> une classe</h2>
  <form action="save_classe.php" method="post" autocomplete="off">
    <?php if ($id): ?>
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>" />
    <?php endif; ?>
    <label for="nom_classe">Nom de la classe</label>
    <input type="text" name="nom_classe" id="nom_classe" value="<?= htmlspecialchars($nom_classe) ?>" required />

    <label for="description">Description</label>
    <textarea name="description" id="description"><?= htmlspecialchars($description) ?></textarea>

    <button type="submit"><?= $id ? "Modifier" : "Ajouter" ?></button>
  </form>
  <a href="liste_classes.php">← Retour à la liste des classes</a>
</div>
</body>
</html>
