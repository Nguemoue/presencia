
<?php
session_start();
if (!isset($_SESSION['type']) || $_SESSION['type'] !== 'admin') {
    header('Location: ../login.html?error=acces');
    exit;	
}
require_once '../includes/config.php';

$id = $_GET['id'];
if ($id) {
    // Suppression de la photo
    $stmtPhoto = $pdo->prepare("SELECT photo_reference FROM users WHERE id=?");
    $stmtPhoto->execute([$id]);
    $photo = $stmtPhoto->fetchColumn();
    if ($photo && file_exists("../uploads/$photo")) {
        unlink("../uploads/$photo");
    }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);
}
header("Location: liste_utilisateurs.php?success=suppression");
exit;
?>
