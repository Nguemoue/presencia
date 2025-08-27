<?php
require_once '../includes/config.php'; // contient $pdo (connexion PDO)

$message = null;

// Limites & formats image
define('MAX_IMAGE_BYTES', 5 * 1024 * 1024); // 5 Mo
$ALLOWED_EXT = array('jpg', 'jpeg', 'png');
$ALLOWED_MIME = array('image/jpeg', 'image/png');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération & nettoyage
    $nom          = isset($_POST['nom']) ? trim($_POST['nom']) : '';
    $prenom       = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
    $email        = isset($_POST['email']) ? trim($_POST['email']) : '';
    $matricule    = isset($_POST['matricule']) ? trim($_POST['matricule']) : '';
    $type         = isset($_POST['type']) ? $_POST['type'] : '';
    $classe_id    = isset($_POST['classe_id']) ? $_POST['classe_id'] : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

    // Validation
    if (
        $nom && $prenom &&
        filter_var($email, FILTER_VALIDATE_EMAIL) &&
        $matricule && $mot_de_passe &&
        in_array($type, array('etudiant','personnel','admin')) &&
        is_numeric($classe_id)
    ) {
        $hashedPassword = password_hash($mot_de_passe, PASSWORD_BCRYPT);

        $photoName = null;
        $photoPath = null;
        $hasPhoto  = (!empty($_FILES['photo_reference']['name']) && $_FILES['photo_reference']['error'] === UPLOAD_ERR_OK);

        // --- Gestion de la photo (facultative) ---
        if ($hasPhoto) {
            if ($_FILES['photo_reference']['size'] <= 0 || $_FILES['photo_reference']['size'] > MAX_IMAGE_BYTES) {
                $message = "⚠️ Image trop volumineuse (max 5MB).";
            } else {
                $ext = strtolower(pathinfo($_FILES['photo_reference']['name'], PATHINFO_EXTENSION));
                if (!in_array($ext, $ALLOWED_EXT)) {
                    $message = "⚠️ Format de photo invalide (jpg, jpeg, png uniquement).";
                } else {
                    $finfo = new finfo(FILEINFO_MIME_TYPE);
                    $mime  = $finfo->file($_FILES['photo_reference']['tmp_name']);
                    if (!in_array($mime, $ALLOWED_MIME)) {
                        $message = "⚠️ Type de contenu invalide (JPEG/PNG uniquement).";
                    } else {
                        $uploadDir = __DIR__ . '/../uploads/';
                        if (!is_dir($uploadDir)) {
                            @mkdir($uploadDir, 0777, true);
                        }
                        $photoName = uniqid('user_') . '.' . $ext;
                        $photoPath = $uploadDir . $photoName;
                        if (!move_uploaded_file($_FILES['photo_reference']['tmp_name'], $photoPath)) {
                            $message = "❌ Échec du stockage de la photo.";
                        }
                    }
                }
            }
        }

        if (!$message) {
            try {
                $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, email, matricule, mot_de_passe, type, classe_id, photo_reference, created_at)
                    VALUES (:nom, :prenom, :email, :matricule, :pwd, :type, :classe_id, :photo, NOW())");
                $stmt->execute(array(
                    ':nom'       => $nom,
                    ':prenom'    => $prenom,
                    ':email'     => $email,
                    ':matricule' => $matricule,
                    ':pwd'       => $hashedPassword,
                    ':type'      => $type,
                    ':classe_id' => $classe_id,
                    ':photo'     => $photoName
                ));

                $userId = (int)$pdo->lastInsertId();

                if ($photoPath) {
                    $flaskUrl = getenv('FLASK_REGISTER_URL');
                    if (!$flaskUrl) $flaskUrl = 'http://127.0.0.1:5000/register';

                    $mimeToSend = mime_content_type($photoPath);
                    if (!$mimeToSend) $mimeToSend = 'image/jpeg';

                    // Pour PHP 5.6, CURLFile existe, mais avec précaution
                    $cfile = class_exists('CURLFile') ? new CURLFile($photoPath, $mimeToSend, basename($photoPath)) : '@' . realpath($photoPath);

                    $postData = array(
                        'name'    => $nom . ' ' . $prenom,
                        'user_id' => (string)$userId,
                        'file'    => $cfile
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $flaskUrl);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

                    $response = curl_exec($ch);
                    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    $err      = curl_error($ch);
                    curl_close($ch);

                    if ($response === false) {
                        $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(array(':id' => $userId));
                        $message = "❌ Erreur de communication avec le service de reconnaissance. " . ($err ? $err : '');
                    } else {
                        $res = json_decode($response, true);
                        if ($httpcode !== 201 || empty($res['success'])) {
                            $pdo->prepare("DELETE FROM users WHERE id = :id")->execute(array(':id' => $userId));
                            $detail = is_array($res) ? (isset($res['message']) ? $res['message'] : (isset($res['error']) ? $res['error'] : 'Erreur API Flask')) : 'Réponse non valide';
                            $message = "❌ Échec de l'enregistrement facial : " . $detail;
                        } else {
                            $message = "✅ Utilisateur ajouté et visage encodé avec succès !";
                        }
                    }
                } else {
                    $message = "✅ Utilisateur ajouté (sans photo faciale).";
                }

            } catch (Exception $e) {
                $message = "❌ Erreur lors de l'ajout : " . htmlspecialchars($e->getMessage());
            }
        }
    } else {
        $message = "⚠️ Veuillez remplir tous les champs correctement.";
    }
}

// Chargement des classes
$classes = array();
try {
    $stmt = $pdo->query("SELECT id, nom_classe FROM classes ORDER BY nom_classe ASC");
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $classes = array();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout Utilisateur</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-container { max-width: 500px; margin: auto; }
        #message { margin-top: 10px; font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
<main>
    <div class="form-container">
        <h2>Ajouter un nouvel utilisateur</h2>
        <?php if ($message): ?>
            <?php $ok = strpos($message, '✅') ==0; ?>
            <div id="message" class="<?= $ok ? 'success' : 'error' ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" autocomplete="off">
            <label>Nom</label>
            <input type="text" name="nom" required>

            <label>Prénom</label>
            <input type="text" name="prenom" required>

            <label>Email</label>
            <input type="email" name="email" required>

            <label>Matricule</label>
            <input type="text" name="matricule" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" required>

            <label>Rôle</label>
            <select name="type" required>
                <option disabled selected>Choisir un rôle</option>
                <option value="etudiant">Étudiant</option>
                <option value="personnel">Personnel</option>
                <option value="admin">Administrateur</option>
            </select>

            <label>Classe</label>
            <select name="classe_id" required>
                <option disabled selected>Choisir une classe</option>
                <?php foreach ($classes as $classe): ?>
                    <option value="<?= htmlspecialchars((string)$classe['id']) ?>">
                        <?= htmlspecialchars($classe['nom_classe']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Photo référence (facultative)</label>
            <input type="file" name="photo_reference" accept="image/*">

            <button type="submit">Ajouter</button>
        </form>
    </div>
</main>
</body>
</html>
