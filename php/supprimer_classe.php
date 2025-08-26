
<?php
session_start();
// sécurise accès admin

require_once '../includes/config.php';

$id = $_GET['id'] ;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM classes WHERE id = ?");
    $stmt->execute([$id]);
}
header('Location: liste_classes.php');
exit;
