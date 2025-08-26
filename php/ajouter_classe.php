<?php
require_once '../includes/config.php';
$nom = $_POST['nom_classe'];
if ($nom) {
    $stmt = $pdo->prepare("INSERT INTO classes (nom_classe) VALUES (?)");
    $stmt->execute([$nom]);
    echo "ok";
} else {
    echo "error";
}
exit;
?>
