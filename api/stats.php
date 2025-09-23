<?php
// api/stats.php
header('Content-Type: application/json');
require_once '../includes/config.php';

// Récupération des filtres
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to   = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
$classe_id = isset($_GET['classe_id']) && $_GET['classe_id'] !== '' ? (int)$_GET['classe_id'] : null;

$whereClasse = $classe_id ? " AND u.classe_id = :classe_id" : "";

// ----------------------
// Totaux globales
// ----------------------
$sqlTotals = "
    SELECT
        SUM(CASE WHEN p.statut = 'present' THEN 1 ELSE 0 END) AS presents,
        SUM(CASE WHEN p.statut = 'absent' THEN 1 ELSE 0 END) AS absents,
        SUM(CASE WHEN p.statut = 'retard' THEN 1 ELSE 0 END) AS retards
    FROM presence p
    JOIN users u ON u.id = p.user_id
    WHERE p.date BETWEEN :from AND :to $whereClasse
";
$stmt = $pdo->prepare($sqlTotals);
$stmt->bindParam(':from', $from);
$stmt->bindParam(':to', $to);
if ($classe_id) $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
$stmt->execute();
$totals = $stmt->fetch(PDO::FETCH_ASSOC);

// ----------------------
// Présences du jour
// ----------------------
$today = date('Y-m-d');
$sqlToday = "
    SELECT COUNT(*) AS today_presents
    FROM presence p
    JOIN users u ON u.id = p.user_id
    WHERE p.date = :today
    AND p.statut = 'present'
    $whereClasse
";
$stmt = $pdo->prepare($sqlToday);
$stmt->bindParam(':today', $today);
if ($classe_id) $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
$stmt->execute();
$todayStats = $stmt->fetch(PDO::FETCH_ASSOC);

// ----------------------
// Statistiques par jour
// ----------------------
$sqlByDay = "
    SELECT p.date,
        SUM(CASE WHEN p.statut = 'present' THEN 1 ELSE 0 END) AS presents,
        SUM(CASE WHEN p.statut = 'absent' THEN 1 ELSE 0 END) AS absents,
        SUM(CASE WHEN p.statut = 'retard' THEN 1 ELSE 0 END) AS retards
    FROM presence p
    JOIN users u ON u.id = p.user_id
    WHERE p.date BETWEEN :from AND :to $whereClasse
    GROUP BY p.date
    ORDER BY p.date ASC
";
$stmt = $pdo->prepare($sqlByDay);
$stmt->bindParam(':from', $from);
$stmt->bindParam(':to', $to);
if ($classe_id) $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
$stmt->execute();
$byDay = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------------
// Répartition par classe
// ----------------------
$sqlByClass = "
    SELECT c.nom AS classe,
        SUM(CASE WHEN p.statut = 'present' THEN 1 ELSE 0 END) AS presents,
        SUM(CASE WHEN p.statut = 'absent' THEN 1 ELSE 0 END) AS absents,
        SUM(CASE WHEN p.statut = 'retard' THEN 1 ELSE 0 END) AS retards
    FROM presence p
    JOIN users u ON u.id = p.user_id
    JOIN classes c ON c.id = u.classe_id
    WHERE p.date BETWEEN :from AND :to
    GROUP BY c.nom
    ORDER BY c.nom
";
$stmt = $pdo->prepare($sqlByClass);
$stmt->bindParam(':from', $from);
$stmt->bindParam(':to', $to);
$stmt->execute();
$byClass = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------------
// Top 5 absences
// ----------------------
$sqlTopAbsences = "
    SELECT u.nom, u.prenom, c.nom AS classe,
        COUNT(*) AS total_absences
    FROM presence p
    JOIN users u ON u.id = p.user_id
    JOIN classes c ON c.id = u.classe_id
    WHERE p.date BETWEEN :from AND :to
    AND p.statut = 'absent' $whereClasse
    GROUP BY u.id, u.nom, u.prenom, c.nom
    ORDER BY total_absences DESC
    LIMIT 5
";
$stmt = $pdo->prepare($sqlTopAbsences);
$stmt->bindParam(':from', $from);
$stmt->bindParam(':to', $to);
if ($classe_id) $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
$stmt->execute();
$topAbsences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------------
// Liste détaillée (utile pour export CSV/Excel/PDF)
// ----------------------
$sqlDetailed = "
    SELECT u.id, u.nom, u.prenom, c.nom AS classe, p.date, p.statut
    FROM presence p
    JOIN users u ON u.id = p.user_id
    JOIN classes c ON c.id = u.classe_id
    WHERE p.date BETWEEN :from AND :to $whereClasse
    ORDER BY u.nom, u.prenom, p.date
";
$stmt = $pdo->prepare($sqlDetailed);
$stmt->bindParam(':from', $from);
$stmt->bindParam(':to', $to);
if ($classe_id) $stmt->bindParam(':classe_id', $classe_id, PDO::PARAM_INT);
$stmt->execute();
$detailed = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ----------------------
// Réponse JSON
// ----------------------
echo json_encode([
    "totals" => $totals,
    "today" => $todayStats,
    "byDay" => $byDay,
    "byClass" => $byClass,
    "topAbsences" => $topAbsences,
    "detailed" => $detailed
], JSON_PRETTY_PRINT);

?>