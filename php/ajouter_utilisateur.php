<?php
require_once '../includes/config.php'; // connexion PDO dans $pdo

$message = null;

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom         = trim($_POST['nom'] );
    $prenom      = trim($_POST['prenom']);
    $email       = trim($_POST['email'] );
    $matricule   = trim($_POST['matricule']);
    $type        = $_POST['type'] ;
    $classe_id   = $_POST['classe_id'] ;
    $mot_de_passe = $_POST['mot_de_passe'] ;
    $photoName   = null;

    // Validation
    if (
        $nom && $prenom &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        $matricule && $mot_de_passe &&
        in_array($type, ['etudiant', 'personnel', 'admin']) &&
        is_numeric($classe_id)
    ) {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        // Gestion de l'upload photo
        if (!empty($_FILES['photo_reference']['name']) && $_FILES['photo_reference']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['photo_reference']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        }

        // Insertion en base de données
        if (!$message) {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (nom, prenom, email, matricule, mot_de_passe, type, classe_id, photo_reference)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([$nom, $prenom, $email, $matricule, $hashedPassword, $type, $classe_id, $photoName]);
                $message = "✅ Utilisateur ajouté avec succès !";
            } catch (Exception $e) {
                $message = "❌ Erreur lors de l'ajout : " . htmlspecialchars($e->getMessage());
            }
        }
    } else {
        $message = "⚠️ Veuillez remplir tous les champs correctement.";
    }
}

// Chargement des classes
$classes = [];
try {
    $stmt = $pdo->query("SELECT id, nom_classe FROM classes ORDER BY nom_classe ASC");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $classes = [];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ajout Utilisateur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        #message {
            margin-top: 10px;
            font-weight: bold;
            color: red;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
        }
    </style>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Nouvel utilisateur</h2>
        <?php if ($message): ?>
            <div id="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data" autocomplete="off">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" required>

            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" required>

            <label for="email">Adresse email</label>
            <input type="email" name="email" id="email" required>

            <label for="matricule">Matricule</label>
            <input type="text" name="matricule" id="matricule" required>

            <label for="mot_de_passe">Mot de passe</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>

            <label for="type">Rôle</label>
            <select name="type" id="type" required>
                <option value="" disabled selected>Choisir un rôle</option>
                <option value="etudiant">Étudiant</option>
                <option value="personnel">Personnel</option>
                <option value="admin">Administrateur</option>
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

            <label for="photo_reference">Photo référence</label>
            <input type="file" name="photo_reference" id="photo_reference" accept="image/*">

            <button type="submit">Ajouter</button>
        </form>
    </div>
</main>
</body>
</html>