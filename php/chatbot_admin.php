<?php
require '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $q = strtolower(trim($_POST["message"]));

    // 🔹 Fonction de compatibilité pour str_contains
    function str_contains_php5($haystack, $needle) {
        return strpos($haystack, $needle) !== false;
    }

    // 🔹 Nombre total de présences
    if (str_contains_php5($q, 'nombre de présences')) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM presence");
        $count = $stmt->fetchColumn();

        echo "Résumé des présences :
        <ul>
          <li>📌 Total : $count</li>
          <li>📊 Source : Table 'presence'</li>
          <li>🔁 Mise à jour en temps réel</li>
        </ul>";
    }

    // 🔹 Liste des classes
    elseif (str_contains_php5($q, 'liste des classes')) {
        $stmt = $pdo->query("SELECT nom_classe FROM classes");
        $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Classes enregistrées :
        <ul>";
        foreach ($classes as $c) echo "<li>🏫 $c</li>";
        echo "</ul>";
    }

    // 🔹 Voir absents d’une classe
    elseif (str_contains_php5($q, 'absents')) {
        $today = date("Y-m-d");
        $stmt = $pdo->query("SELECT u.nom FROM users u 
                             LEFT JOIN presence p ON u.id = p.id AND DATE(p.date_heure) = '$today'
                             WHERE p.id IS NULL");
        $absents = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Absents aujourd'hui :
        <ul>";
        foreach ($absents as $a) echo "<li>❌ $a</li>";
        echo "</ul>";
    }

    // 🔹 Questions fonctionnement admin
    elseif (str_contains_php5($q, 'comment ajouter une classe')) {
        echo "Procédure en 3 étapes :
        <ul>
          <li>1️⃣ Accéder au menu 'Gestion des classes'</li>
          <li>2️⃣ Cliquer sur 'Ajouter une nouvelle classe'</li>
          <li>3️⃣ Remplir et enregistrer</li>
        </ul>";
    }

    elseif (str_contains_php5($q, 'comment inscrire un étudiant')) {
        echo "3 points essentiels :
        <ul>
          <li>1️⃣ Ouvrir 'Gestion des utilisateurs'</li>
          <li>2️⃣ Cliquer sur 'Ajouter étudiant'</li>
          <li>3️⃣ Associer à une classe et valider</li>
        </ul>";
    }

    else {
        echo "❓ Je ne comprends pas. Tu peux demander par ex. :
        <ul>
          <li>📊 Nombre de présences</li>
          <li>🏫 Liste des classes</li>
          <li>⚠️ Absents d’une classe</li>
          <li>ℹ️ Comment ajouter une classe</li>
        </ul>";
    }
}
?>