<?php
require_once '../includes/config.php';
$id = $_POST['id'];
$nom = $_POST['nom_classe'];
if ($id && $nom) {
    $stmt = $pdo->prepare("UPDATE classes SET nom_classe = ? WHERE id = ?");
    $stmt->execute([$nom, $id]);
    echo "ok";
} else {
    echo "error";
}
exit;
?>
