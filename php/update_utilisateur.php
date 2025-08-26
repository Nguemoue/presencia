
<?php
session_start();
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header('Location: ../login.html?error=acces');
    exit;
}
require_once '../includes/config.php';

$id = $_POST['id'] ;
$nom = $_POST['nom'] ;
$prenom = $_POST['prenom'];
$email = $_POST['email'];
$matricule = $_POST['matricule'];
$type = $_POST['type'] ;
$classe_id = $_POST['classe_id'] ;
$photoName = null;

// Si nouvelle photo envoyée, upload et mise à jour
if (isset($_FILES['photo_reference']) && $_FILES['photo_reference']['error'] === 0) {
    $ext = pathinfo($_FILES['photo_reference']['name'], PATHINFO_EXTENSION);
    $photoName = uniqid('photo_') . '.' . $ext;
    move_uploaded_file($_FILES['photo_reference']['tmp_name'], "../uploads/$photoName");

    // Option : supprimer l'ancienne photo si différente
    $stmtPhoto = $pdo->prepare("SELECT photo_reference FROM users WHERE id=?");
    $stmtPhoto->execute([$id]);
    $oldPhoto = $stmtPhoto->fetchColumn();
    if ($oldPhoto && $oldPhoto !== $photoName && file_exists("../uploads/$oldPhoto")) {
        unlink("../uploads/$oldPhoto");
    }

    $stmt = $pdo->prepare(
        "UPDATE users SET nom=?, prenom=?, email=?, matricule=?, type=?, classe_id=?, photo_reference=? WHERE id=?"
    );
    $stmt->execute([$nom, $prenom, $email, $matricule, $type, $classe_ids, $photoName, $id]);
} else {
    $stmt = $pdo->prepare(
        "UPDATE users SET nom=?, prenom=?, email=?, matricule=?, type=?, classe_ids=? WHERE id=?"
    );
    $stmt->execute([$nom, $prenom, $email, $matricule, $type, $classe_ids, $id]);
}
header("Location: liste_utilisateurs.php?success=modification");
exit;
?>
