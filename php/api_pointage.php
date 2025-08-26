<?php
session_start();
require_once '../includes/config.php';
$data = json_decode(file_get_contents('php://input'), true);

$imageData = $data['image'];

if(!$imageData){
 echo json_encode("EMpty image");
}

if (!isset($_SESSION['user_id'])) {
  echo json_encode([ "success" => false, "message" => "Vous devez être connecté(e)." ]);
  exit;
}
$user_id = $_SESSION['user_id'];

// Appel à l'API Python pour reconnaissance faciale (à développer)
$response = file_get_contents("http://127.0.0.1:500/face_recognition", false, stream_context_create([
  "http" => [
    "method"=>"POST",
    "header"=>"Content-Type: application/json",
    "content" => json_encode([ "user_id"=>$user_id, "image"=>$imageData ])
  ]
]));
$faceResult = json_decode($response,true);

if(!$faceResult['success']){
  echo json_encode([ "success"=>false, "message"=>"Reconnaissance faciale échouée ou non reconnue." ]);
  exit;
}

// Limitation une fois par jour
$periode = date('Y-m-d');
$stmt = $pdo->prepare("SELECT COUNT(*) FROM presence WHERE id_user = ? AND periode = ?");
$stmt->execute([$user_id,$periode]);
$count = $stmt->fetchColumn();
$limite = 2;
if($count >= $limite){
  echo json_encode([ "success"=>false, "message"=>"Tu as déjà pointé ta présence aujourd'hui !" ]);
  exit;
}

// Sauvegarde de l'image pour archive
function saveImage($base64img){
  $base64img = explode(',', $base64img)[1];
  $file = 'uploads/pointage_' . uniqid() . '.jpg';
  file_put_contents('../' . $file, base64_decode($base64img));
  return $file;
}

// Enregistrement
$stmt = $pdo->prepare("INSERT INTO presence (user_id, date_heure, id_periode, image_reference, statut) VALUES (?,NOW(),?,?,?)");
$stmt->execute([$user_id, $periode, saveImage($imageData), 'valide']);

echo json_encode([ "success"=>true, "date"=>date('d/m/Y H:i:s') ]);
?>
