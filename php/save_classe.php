
<?php
session_start();
// sécurise accès admin

require_once '../includes/config.php';

$id = $_POST['id'] ;
$nom_classe = trim($_POST['nom_classe']);
$description = trim($_POST['description']);

if (!$nom_classe) {
    header('Location: gestion_classe.php?error=empty' . ($id ? '&id='.$id : ''));
    exit;
}

try {
    if ($id) {
        $stmt = $pdo->prepare("UPDATE classes SET nom_classe = ?, description = ? WHERE id = ?");
        $stmt->execute([$nom_classe, $description, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO classes (nom_classe, description) VALUES (?, ?)");
        $stmt->execute([$nom_classe, $description]);
    }
    header('Location: liste_classes.php?success=1');
} catch (PDOException $e) {
    header('Location: gestion_classe.php?error=duplicate' . ($id ? '&id='.$id : ''));
}
exit;
