
<?php
require_once '../includes/config.php'; // connexion PDO dans $pdo

// Si requête Ajax → traitement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');

    $nom         = trim($_POST['nom']);
    $prenom      = trim($_POST['prenom']);
    $email       = trim($_POST['email']);
    $matricule   = trim($_POST['matricule']);
    $type        = $_POST['type'];
    $classe_id   = $_POST['classe_id'];
    $mot_de_passe = $_POST['mot_de_passe'];
    $photoName   = null;

    // Validation
    if ($nom && $prenom && filter_var($email, FILTER_VALIDATE_EMAIL) && $matricule && $mot_de_passe && $type && $classe_id) {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        // Gestion de l'upload photo
        if (isset($_FILES['photo_reference']) && $_FILES['photo_reference']['error'] === 0) {
            $ext = strtolower(pathinfo($_FILES['photo_reference']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($ext, $allowed)) {
                $photoName = uniqid('photo_') . '.' . $ext;
                move_uploaded_file($_FILES['photo_reference']['tmp_name'], __DIR__ . "/uploads/$photoName");
            }
        }

        // Insertion
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO users (nom, prenom, email, matricule, mot_de_passe, type, classe_id, photo_reference)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([$nom, $prenom, $email, $matricule, $hashedPassword, $type, $classe_id, $photoName]);
            echo json_encode([
                "status" => "success",
                "message" => "✅ Utilisateur ajouté avec succès !",
                "redirect" => "liste_utilisateurs.php"
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "❌ Erreur : " . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(["status" => "error", "message" => "⚠️ Veuillez remplir tous les champs correctement."]);
        exit;
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
}
</style>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Nouvel utilisateur</h2>
        <div id="message"></div>
        <form id="userForm" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="ajax" value="1">

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

<script>
document.getElementById('userForm').addEventListener('submit', function(e) {
    e.preventDefault();
    let form = e.target;
    let formData = new FormData(form);

    fetch("", { 
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        let msg = document.getElementById('message');
        msg.style.color = data.status === "success" ? "green" : "red";
        msg.innerHTML = data.message;

        if (data.status === "success") {
            setTimeout(() => {
                window.location.href = data.redirect; // redirection vers dashboard admin
            }, 1500);
        }
    })
    .catch(err => {
        document.getElementById('message').style.color = "red";
        document.getElementById('message').innerHTML = "❌ Erreur de connexion au serveur.";
    });
});
</script>
</body>
</html>


