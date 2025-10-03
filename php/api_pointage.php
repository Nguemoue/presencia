<?php
session_start();
include("../includes/config.php");
header("Content-Type: application/json");
if(!isset($_SESSION["user_id"])){
    #user is not loggws
    header("Location: ../login.php");
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if(!$data ||!isset($data['image'])) {
    echo json_encode(["success" => false, "message" => "Aucune image reçue"]);
    exit;
}

// Extraire l'image base64
$imgData = $data['image'];
$imgData = str_replace(array('data:image/jpeg;base64,', ' '), array('', '+'), $imgData);
$decoded = base64_decode($imgData);

if(!$decoded) {
    echo json_encode(["success" => false, "message" => "Impossible de décoder l'image"]);
    exit;
}
$userId = $_SESSION["user_id"];
// Sauvegarder temporairement l’image
$tmpFile = sys_get_temp_dir(). "/capture_".uniqid().".jpg";
file_put_contents($tmpFile, $decoded);

// Appeler ton service Flask (par ex. http://127.0.0.1:5000/recognize)
$ch = curl_init("http://127.0.0.1:5000/recognize");
$cfile = new CURLFile($tmpFile, "image/jpeg", basename($tmpFile));
$postData = ['file' => $cfile, 'user_id' => $userId];

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

$response = curl_exec($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Supprimer le fichier temporaire
unlink($tmpFile);

if($httpcode === 200 && $response) {
    $res = json_decode($response, true);
    if(isset($res['match']) && $res['match'] === true) {
        
        // Récupérer la période en cours
        $stmtPeriode = $pdo->prepare("SELECT id_periode FROM periode WHERE CURRENT_TIME() BETWEEN heure_debut AND heure_fin LIMIT 1");
        $stmtPeriode->execute();
        $periode = $stmtPeriode->fetchColumn();

        if(!$periode) {
            echo json_encode(["success" => false, "message" => "Aucune periode active actuellement , le pointage est temporairement fermé😑"]);
            exit;
        }

        // Vérifier si déjà pointé cette période
        $stmtCheck = $pdo->prepare( "SELECT COUNT(*) FROM presence WHERE user_id = ? AND id_periode = ?");
        $stmtCheck->execute([$userId, $periode]);
        if($stmtCheck->fetchColumn() > 1) {
            echo json_encode(["success" => false, "message" => "Tu as déjà pointé cette période."]);
            exit;
        }

        // Insérer la présence normalement
       $stmt = $pdo->prepare("replace into presence (user_id,date_heure,id_periode,statut) 
        values(:userId,NOW(),(select id_periode from periode where current_time() between heure_debut and heure_fin limit 1 ),'present')
        ");
        $stmt->bindParam(":userId",$userId, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Pointage validé", "date" => date("Y-m-d H:i:s"), "person" => $userId]);
                exit;
    } else {
        echo json_encode(["success" => false, "message" => "Visage non reconnu"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Erreur API reconnaissance"]);
}

?>
