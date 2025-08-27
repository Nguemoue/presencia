
<?php
session_start();
require_once '../includes/config.php'; // Inclusion du fichier de connexion PDO


// Récupération et filtrage des données POST
$matricule = $_POST['matricule'];
$password = $_POST['password'] ;
$role = $_POST['role'] ;

if (!$matricule || !$password || !$role) {
    header('Location: ../login.html?error=champs');
    exit;
}

// Préparation de la requête PDO (connexion par matricule ET rôle)
$stmt = $pdo->prepare("
    SELECT id, nom, matricule, mot_de_passe, type, email
    FROM users 
    WHERE matricule = :matricule AND type = :role
    LIMIT 1
");

$stmt->execute([
    ':matricule' => $matricule,
    ':role' => $role,
]);

$user = $stmt->fetch();
if ($user && password_verify($password, $user['mot_de_passe'])) {
    // Authentification réussie : création de la session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nom']     = $user['nom'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['type']    = $user['type'];
    $_SESSION['matricule'] = $user['matricule'];
    
    // Redirection selon le rôle
   if ($user['type'] === 'etudiant') {
            header('Location: dashboard.html');
   }elseif($user['type'] === 'personnel') {
            header('Location: dashboard.html');
 }elseif($user['type'] === 'admin') {
            header('Location: dashbordAdmin.php');
 }
exit;
}else{
    echo"identifiants incorrects";
}
?>
