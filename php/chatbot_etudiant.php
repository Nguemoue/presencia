<?php
require '../includes/config.php'; // connexion PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $q = strtolower(trim($_POST["message"]));
    $id_user = isset($_POST["user_id"]) ? $_POST["user_id"] : null; // à adapter selon ton système

    // 🔹 Fonction de compatibilité PHP 5
    function str_contains_php5($haystack, $needle) {
        return strpos($haystack, $needle) !== false;
    }

    // 🔹 Vérifier la dernière présence
    if (str_contains_php5($q, 'dernière présence')) {
        $stmt = $pdo->prepare("SELECT date_heure FROM presence WHERE id = ? ORDER BY date_heure DESC LIMIT 1");
        $stmt->execute(array($id_user));
        $row = $stmt->fetch();

        if ($row) {
            echo "Voici 3 infos clés :
            <ul>
              <li>👤 Étudiant ID : $id_user</li>
              <li>📅 Dernier pointage : {$row['date_heure']}</li>
              <li>✅ Présence bien enregistrée</li>
            </ul>";
        } else {
            echo "Aucune présence trouvée.";
        }
    }

    // 🔹 Voir mes absences
    elseif (str_contains_php5($q, 'mes absences')) {
        $today = date("Y-m-d");
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM presence WHERE id = ? AND DATE(date_heure) = ?");
        $stmt->execute(array($id_user, $today));
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            echo "3 points à noter :
            <ul>
              <li>⚠️ Aucune présence trouvée aujourd'hui</li>
              <li>📅 Date : $today</li>
              <li>❌ Statut : Absent</li>
            </ul>";
        } else {
            echo "Tu as déjà marqué $count présence(s) aujourd'hui ✅";
        }
    }

    // 🔹 Vérifier si j’ai atteint la limite
    elseif (str_contains_php5($q, 'limite')) {
        $today = date("Y-m-d");
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM presence WHERE id = ? AND DATE(date_heure) = ?");
        $stmt->execute(array($id_user, $today));
        $count = $stmt->fetchColumn();

        echo "Infos clés sur ta limite :
        <ul>
          <li>📊 Pointages aujourd'hui : $count</li>
          <li>⏳ Maximum autorisé : 2</li>
          <li>".($count >= 2 ? "❌ Tu as atteint la limite" : "✅ Tu peux encore pointer")."</li>
        </ul>";
    }

    // 🔹 Questions fonctionnement étudiant
    elseif (str_contains_php5($q, 'comment marquer ma présence')) {
        echo "Voici 3 étapes simples :
        <ul>
          <li>1️⃣ Connecte-toi à l'application</li>
          <li>2️⃣ Saisis ton matricule ou utilise la caméra</li>
          <li>3️⃣ Vérifie le message de confirmation</li>
        </ul>";
    }

    elseif (str_contains_php5($q, 'comment voir mes présences')) {
        echo "Pour consulter tes présences :
        <ul>
          <li>📅 Va dans ton tableau de bord</li>
          <li>📊 Choisis une période (jour/semaine/mois)</li>
          <li>✅ Vérifie la liste affichée</li>
        </ul>";
    }

    else {
        echo "❓ Je ne comprends pas. Tu peux demander par ex. :
        <ul>
          <li>🕒 Dernière présence</li>
          <li>📅 Mes absences</li>
          <li>📊 Limite de 2 pointages</li>
          <li>ℹ️ Comment marquer ma présence</li>
        </ul>";
    }
}
?>