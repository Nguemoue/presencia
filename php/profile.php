<?php
session_start();
require_once "../includes/config.php"; // adapte le chemin

if (!isset($_SESSION['user_id'])) {
    die("Utilisateur non connecté. Veuillez vous connecter.");
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT u.id, u.nom, u.prenom, u.email, u.type, u.face_encoding,
               u.matricule, u.created_at, c.nom_classe AS classe
        FROM users u
        LEFT JOIN classes c ON u.classe_id = c.id
        WHERE u.id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute(array($user_id));
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Utilisateur introuvable.");
}

// Photo encodée
$photo = "default-avatar.png";
if (!empty($user['face_encoding'])) {
    $photo = "data:image/jpeg;base64," . base64_encode($user['face_encoding']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Page principale</title>
<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f4f7f6;
    margin: 0;
    padding: 0;
  }

  header {
    background: #00c853;
    padding: 15px;
    text-align: right;
  }

  header button {
    background: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 30px;
    cursor: pointer;
    font-weight: bold;
    transition: 0.3s;
  }

  header button:hover {
    background: #b2ff59;
  }

  /* Backdrop modal */
  .modal-bg {
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    backdrop-filter: blur(8px);
    display: none;
    justify-content: center;
    align-items: center;
    animation: fadeIn 0.4s;
  }

  .modal {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(15px);
    border-radius: 20px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    padding: 30px;
    text-align: center;
    color: #fff;
    width: 350px;
    transform: scale(0.7);
    opacity: 0;
    animation: zoomIn 0.4s forwards;
  }

  .modal img {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid #fff;
    margin-bottom: 15px;
    object-fit: cover;
    animation: float 4s ease-in-out infinite;
  }

  .modal h3 {
    margin: 10px 0;
  }

  .close-btn {
    background: #ff5252;
    border: none;
    border-radius: 50%;
    color: #fff;
    width: 35px;
    height: 35px;
    font-size: 18px;
    cursor: pointer;
    position: absolute;
    top: 20px; right: 20px;
  }

  @keyframes fadeIn {
    from {opacity: 0;} to {opacity: 1;}
  }

  @keyframes zoomIn {
    to { transform: scale(1); opacity: 1; }
  }

  @keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
  }
</style>
</head>
<body>

<header>
  <button id="openProfile">👤 Profil</button>
</header>

<!-- Modal Profil -->
<div class="modal-bg" id="modalBg">
  <div class="modal">
    <button class="close-btn" id="closeModal">&times;</button>
    <img src="<?php echo $photo; ?>" alt="Photo de profil">
    <h3><?php echo htmlspecialchars($user['prenom'] . " " . $user['nom']); ?></h3>
    <p><strong>Email :</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Matricule :</strong> <?php echo !empty($user['matricule']) ? $user['matricule'] : '-'; ?></p>
    <p><strong>Classe :</strong> <?php echo !empty($user['classe']) ? $user['classe'] : '-'; ?></p>
    <p><strong>Rôle :</strong> <?php echo $user['type']; ?></p>
    <p><small>Membre depuis : <?php echo date("d/m/Y", strtotime($user['created_at'])); ?></small></p>
  </div>
</div>

<script>
  var openBtn = document.getElementById("openProfile");
  var modalBg = document.getElementById("modalBg");
  var closeBtn = document.getElementById("closeModal");

  openBtn.addEventListener("click", function(){
    modalBg.style.display = "flex";
  });

  closeBtn.addEventListener("click", function(){
    modalBg.style.display = "none";
  });

  // Fermer en cliquant à l'extérieur
  window.addEventListener("click", function(e){
    if(e.target == modalBg){
      modalBg.style.display = "none";
    }
  });
</script>

</body>
</html>
