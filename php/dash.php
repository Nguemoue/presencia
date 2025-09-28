<?php
// Connexion à la base de données
require_once '../includes/config.php';

// Initialiser les variables de filtre
$filters = [
    'date_debut' => isset($_GET['date_debut']) ? $_GET['date_debut'] : '',
    'date_fin' => isset($_GET['date_fin']) ? $_GET['date_fin'] : '',
    'classe_id' => isset($_GET['classe_id']) ? $_GET['classe_id'] : '',
    'service' => isset($_GET['service']) ? $_GET['service'] : '',
    'type_utilisateur' => isset($_GET['type_utilisateur']) ? $_GET['type_utilisateur'] : '',
    'periode_id' => isset($_GET['periode_id']) ? $_GET['periode_id'] : ''
];

// Requête pour récupérer les statistiques globales
$query_stats = "
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN p.statut = 'present' THEN 1 ELSE 0 END) as presents,
        SUM(CASE WHEN p.statut = 'absent' THEN 1 ELSE 0 END) as absents,
        SUM(CASE WHEN p.statut = 'retard' THEN 1 ELSE 0 END) as retards
    FROM presence p
    INNER JOIN users u ON p.user_id = u.id
    WHERE 1
";

// Requête pour l'historique des présences
$query_historique = "
SELECT 
        u.nom, 
        u.prenom, 
        u.type as type_utilisateur,
        c.nom_classe,
        p.date_heure,
        per.nom_periode,
        per.heure_debut,
        per.heure_fin,
        CASE 
            WHEN TIMESTAMPDIFF(MINUTE, p.date_heure, NOW()) > 15 THEN 'retard'
            ELSE p.statut
        END as statut
    FROM presence p
    INNER JOIN users u ON p.user_id = u.id
    LEFT JOIN classes c ON u.classe_id = c.id
    INNER JOIN periode per ON p.id_periode = per.id_periode
    WHERE 1  
";

// Appliquer les filtres si ils sont définis
if (!empty($filters['date_debut'])) {
    $query_stats .= " AND DATE(p.date_heure) >= :date_debut";
    $query_historique .= " AND DATE(p.date_heure) >= :date_debut";
}

if (!empty($filters['date_fin'])) {
    $query_stats .= " AND DATE(p.date_heure) <= :date_fin";
    $query_historique .= " AND DATE(p.date_heure) <= :date_fin";
}

if (!empty($filters['classe_id'])) {
    $query_stats .= " AND u.classe_id = :classe_id";
    $query_historique .= " AND u.classe_id = :classe_id";
}

if (!empty($filters['type_utilisateur'])) {
    $query_stats .= " AND u.type = :type_utilisateur";
    $query_historique .= " AND u.type = :type_utilisateur";
}

if (!empty($filters['periode_id'])) {
    $query_stats .= " AND p.id_periode = :periode_id";
    $query_historique .= " AND p.id_periode = :periode_id";
}

// Exécuter les requêtes avec PDO
try {
    $stmt_stats = $pdo->prepare($query_stats);
    if (!empty($filters['date_debut'])) {
        $stmt_stats->bindParam(':date_debut', $filters['date_debut']);
    }
    if (!empty($filters['date_fin'])) {
        $stmt_stats->bindParam(':date_fin', $filters['date_fin']);
    }
    if (!empty($filters['classe_id'])) {
        $stmt_stats->bindParam(':classe_id', $filters['classe_id'], PDO::PARAM_INT);
    }
    if (!empty($filters['type_utilisateur'])) {
        $stmt_stats->bindParam(':type_utilisateur', $filters['type_utilisateur']);
    }
    if (!empty($filters['periode_id'])) {
        $stmt_stats->bindParam(':periode_id', $filters['periode_id'], PDO::PARAM_INT);
    }
    $stmt_stats->execute();
    $stats = $stmt_stats->fetch(PDO::FETCH_ASSOC);

    $stmt_historique = $pdo->prepare($query_historique);
    if (!empty($filters['date_debut'])) {
        $stmt_historique->bindParam(':date_debut', $filters['date_debut']);
    }
    if (!empty($filters['date_fin'])) {
        $stmt_historique->bindParam(':date_fin', $filters['date_fin']);
    }
    if (!empty($filters['classe_id'])) {
        $stmt_historique->bindParam(':classe_id', $filters['classe_id'], PDO::PARAM_INT);
    }
    if (!empty($filters['type_utilisateur'])) {
        $stmt_historique->bindParam(':type_utilisateur', $filters['type_utilisateur']);
    }
    if (!empty($filters['periode_id'])) {
        $stmt_historique->bindParam(':periode_id', $filters['periode_id'], PDO::PARAM_INT);
    }
    $stmt_historique->execute();
    $historique = $stmt_historique->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les classes pour le filtre
    $query_classes = "SELECT id, nom_classe FROM classes";
    $result_classes = $pdo->query($query_classes);
    $classes = $result_classes->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les périodes pour le filtre
    $query_periodes = "SELECT id_periode, nom_periode FROM periode";
    $result_periodes = $pdo->query($query_periodes);
    $periodes = $result_periodes->fetchAll(PDO::FETCH_ASSOC);

    // Calculer les pourcentages
    $total = $stats['total'];
    $presents_pourcentage = $total > 0 ? round(($stats['presents'] / $total) * 100, 2) : 0;
    $absents_pourcentage = $total > 0 ? round(($stats['absents'] / $total) * 100, 2) : 0;
    $retards_pourcentage = $total > 0 ? round(($stats['retards'] / $total) * 100, 2) : 0;

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - Gestion des Présences</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        :root {
            --primary: #43ee73ff;
            --secondary: #36d56bff;
            --success: #d6c2eeff;
            --danger: #f72585;
            --warning: #f77f00;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fb;
            color: #333;
            transition: all 0.3s ease;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: white;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            transition: all 0.3s ease;
            z-index: 1000;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--light-gray);
            margin-bottom: 20px;
        }
        
        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .sidebar-menu {
            list-style: none;
        }
        
        .sidebar-menu li {
            margin-bottom: 5px;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 12px 20px;
            text-decoration: none;
            color: var(--dark);
            transition: all 0.3s ease;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s ease;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .header h1 {
            font-size: 1.8rem;
            color: var(--primary);
        }
        
        .user-info {
            display: flex;
            align-items: center;
        }
        
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .filter-title {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-item {
            margin-bottom: 15px;
        }
        
        .filter-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: var(--gray);
        }
        
        .filter-item select, .filter-item input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--light-gray);
            border-radius: 5px;
            outline: none;
            transition: all 0.3s ease;
        }
        
        .filter-item select:focus, .filter-item input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: var(--primary);
            border: 1px solid var(--primary);
        }
        
        .btn-secondary:hover {
            background: rgba(67, 97, 238, 0.1);
        }
        
        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 1.5rem;
        }
        
        .present .stat-icon {
            background: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }
        
        .absent .stat-icon {
            background: rgba(247, 37, 133, 0.2);
            color: var(--danger);
        }
        
        .late .stat-icon {
            background: rgba(247, 127, 0, 0.2);
            color: var(--warning);
        }
        
        .total .stat-icon {
            background: rgba(67, 97, 238, 0.2);
            color: var(--primary);
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .present .stat-value {
            color: var(--success);
        }
        
        .absent .stat-value {
            color: var(--danger);
        }
        
        .late .stat-value {
            color: var(--warning);
        }
        
        .total .stat-value {
            color: var(--primary);
        }
        
        .stat-label {
            color: var(--gray);
            font-weight: 500;
        }
        
        /* Charts Section */
        .charts-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-title {
            font-size: 1.2rem;
            color: var(--dark);
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* History Section */
        .history-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 1.4rem;
            color: var(--dark);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--light-gray);
        }
        
        th {
            background-color: var(--light);
            font-weight: 600;
            color: var(--gray);
        }
        
        tr:hover {
            background-color: rgba(67, 97, 238, 0.05);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-present {
            background: rgba(76, 201, 240, 0.2);
            color: var(--success);
        }
        
        .status-absent {
            background: rgba(247, 37, 133, 0.2);
            color: var(--danger);
        }
        
        .status-late {
            background: rgba(247, 127, 0, 0.2);
            color: var(--warning);
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        
        .pagination button {
            margin: 0 5px;
            padding: 8px 15px;
            border: 1px solid var(--light-gray);
            background: white;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .pagination button.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .pagination button:hover:not(.active) {
            background: var(--light);
        }
        
        /* Export Section */
        .export-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }
        
        .export-options {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }
        
        .delay-1 { animation-delay: 0.1s; }
        .delay-2 { animation-delay: 0.2s; }
        .delay-3 { animation-delay: 0.3s; }
        .delay-4 { animation-delay: 0.4s; }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar:hover {
                width: 250px;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .sidebar-header h2, .sidebar-menu span {
                display: none;
            }
            
            .sidebar:hover .sidebar-header h2,
            .sidebar:hover .sidebar-menu span {
                display: block;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            
            .main-content {
                margin-left: 0;
                padding: 10px;
            }
            
            .sidebar {
                width: 0;
                padding: 0;
            }
            
            .sidebar.show {
                width: 250px;
                padding: 20px 0;
            }
            
            .menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 2000;
                background: var(--primary);
                color: white;
                width: 40px;
                height: 40px;
                border-radius: 5px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
        }
    </style>
</head>
<body>
    <div class="menu-toggle" id="menuToggle">
        <i>≡</i>
    </div>
    
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h2>PrésencePro</h2>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i>📊</i> <span>Tableau de bord</span></a></li>
                <li><a href="#"><i>⚙️</i> <span>Paramètres</span></a></li>
                <li><a href="dashbordAdmin.php"><i>🚪</i> <span>retour au dashbord Admin</span></a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header fade-in">
                <h1>Tableau de Bord des Présences</h1>
                <div class="user-info">
                    <img src="https://ui-avatars.com/api/?name=Admin+User&background=4361ee&color=fff" alt="User">
                    <div>
                        <div>Admin User</div>
                        <small>Administrateur</small>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section fade-in delay-1">
                <div class="filter-title">Filtres</div>
                <form method="GET" action="">
                    <div class="filter-grid">
                        <div class="filter-item">
                            <label for="date-debut">Date de début</label>
                            <input type="date" id="date-debut" name="date_debut" value="<?php echo $filters['date_debut']; ?>">
                        </div>
                        <div class="filter-item">
                            <label for="date-fin">Date de fin</label>
                            <input type="date" id="date-fin" name="date_fin" value="<?php echo $filters['date_fin']; ?>">
                        </div>
                        <div class="filter-item">
                            <label for="classe_id">Classe/service</label>
                            <select id="classe_id" name="classe_id">
                                <option value="">Toutes les classes</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?php echo $classe['id']; ?>" <?php echo $filters['classe_id'] == $classe['id'] ? 'selected' : ''; ?>>
                                        <?php echo $classe['nom_classe']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="filter-item">
                            <label for="type-utilisateur">Type d'utilisateur</label>
                            <select id="type-utilisateur" name="type_utilisateur">
                                <option value="">Tous les types</option>
                                <option value="etudiant" <?php echo $filters['type_utilisateur'] == 'etudiant' ? 'selected' : ''; ?>>Étudiant</option>
                                <option value="personnel" <?php echo $filters['type_utilisateur'] == 'personnel' ? 'selected' : ''; ?>>Personnel</option>
                                <option value="administrateur" <?php echo $filters['type_utilisateur'] == 'administrateur' ? 'selected' : ''; ?>>Administrateur</option>
                            </select>
                        </div>
                        <div class="filter-item">
                            <label for="periode_id">Période</label>
                            <select id="periode_id" name="periode_id">
                                <option value="">Toutes les périodes</option>
                                <?php foreach ($periodes as $periode): ?>
                                    <option value="<?php echo $periode['id_periode']; ?>" <?php echo $filters['periode_id'] == $periode['id_periode'] ? 'selected' : ''; ?>>
                                        <?php echo $periode['nom_periode']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">Appliquer les filtres</button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='?'">Réinitialiser</button>
                    </div>
                </form>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-grid">
                <div class="stat-card present fade-in delay-2">
                    <div class="stat-icon">✓</div>
                    <div class="stat-value"><?php echo $presents_pourcentage; ?>%</div>
                    <div class="stat-label">Taux de présence</div>
                    <small><?php echo $stats['presents']; ?> présences</small>
                </div>
                
                <div class="stat-card absent fade-in delay-2">
                    <div class="stat-icon">✗</div>
                    <div class="stat-value"><?php echo $absents_pourcentage; ?>%</div>
                    <div class="stat-label">Taux d'absence</div>
                    <small><?php echo $stats['absents']; ?> absences</small>
                </div>
                
                <div class="stat-card late fade-in delay-3">
                    <div class="stat-icon">⌛</div>
                    <div class="stat-value"><?php echo $retards_pourcentage; ?>%</div>
                    <div class="stat-label">Taux de retard</div>
                    <small><?php echo $stats['retards']; ?> retards</small>
                </div>
                
                <div class="stat-card total fade-in delay-3">
                    <div class="stat-icon">👥</div>
                    <div class="stat-value"><?php echo $total; ?></div>
                    <div class="stat-label">Total enregistrements</div>
                    <small>Sur la période sélectionnée</small>
                </div>
            </div>
            
            <!-- Charts Section -->
            <div class="charts-grid">
                <div class="chart-card fade-in delay-4">
                    <div class="chart-header">
                        <div class="chart-title">Répartition des statuts</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
                
                <div class="chart-card fade-in delay-4">
                    <div class="chart-header">
                        <div class="chart-title">Présences par statut</div>
                    </div>
                    <div class="chart-container">
                        <canvas id="barChart"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- History Section -->
            <div class="history-section">
                <div class="section-header">
                    <div class="section-title">Historique des présences</div>
                    <button class="btn btn-primary" id="exportPdf">Exporter PDF</button>
                </div>
                
                <table>
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Type</th>
                            <th>Classe/Service</th>
                            <th>Date</th>
                            <th>Période</th>
                            <th>Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($historique) > 0): ?>
                            <?php foreach ($historique as $row): ?>
                                <tr>
                                    <td><?php echo $row['nom']; ?></td>
                                    <td><?php echo $row['prenom']; ?></td>
                                    <td><?php echo ucfirst($row['type_utilisateur']); ?></td>
                                    <td><?php echo !empty($row['nom_classe']) ? $row['nom_classe'] : 'N/A'; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['date_heure'])); ?></td>
                                    <td><?php echo $row['nom_periode']; ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        if ($row['statut'] == 'present') {
                                            $status_class = 'status-present';
                                            $status_text = 'Présent';
                                        } elseif ($row['statut'] == 'absent') {
                                            $status_class = 'status-absent';
                                            $status_text = 'Absent';
                                        } elseif ($row['statut'] == 'retard') {
                                            $status_class = 'status-late';
                                            $status_text = 'Retard';
                                        }
                                        ?>
                                        <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Aucune donnée à afficher pour les filtres sélectionnés</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <div class="pagination">
                    <button>&laquo;</button>
                    <button class="active">1</button>
                    <button>2</button>
                    <button>3</button>
                    <button>&raquo;</button>
                </div>
            </div>
            
            <!-- Export Section -->
            <div class="export-section">
                <div class="section-header">
                    <div class="section-title">Exporter les rapports</div>
                </div>
                
                <div class="export-options">
                    <button class="btn btn-primary" id="exportPdfFull">Exporter en PDF</button>
                    
                </div>
            </div>
        </div>
    </div>

    <script>
        // Menu toggle for mobile
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });
        
        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Pie Chart
            const pieCtx = document.getElementById('pieChart').getContext('2d');
            const pieChart = new Chart(pieCtx, {
                type: 'pie',
                data: {
                    labels: ['Présent', 'Absent', 'Retard'],
                    datasets: [{
                        data: [<?php echo $stats['presents']; ?>, <?php echo $stats['absents']; ?>, <?php echo $stats['retards']; ?>],
                        backgroundColor: [
                            '#4cc9f0',
                            '#f72585',
                            '#f77f00'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
            
            // Bar Chart
            const barCtx = document.getElementById('barChart').getContext('2d');
            const barChart = new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: ['Présents', 'Absents', 'Retards'],
                    datasets: [{
                        label: 'Nombre',
                        data: [<?php echo $stats['presents']; ?>, <?php echo $stats['absents']; ?>, <?php echo $stats['retards']; ?>],
                        backgroundColor: [
                            '#4cc9f0',
                            '#f72585',
                            '#f77f00'
                        ],
                        borderWidth: 0,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    },
                    animation: {
                        duration: 2000,
                        easing: 'easeOutQuart'
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Add animations to elements when scrolling
            const animatedElements = document.querySelectorAll('.fade-in');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, {
                threshold: 0.1
            });
            
            animatedElements.forEach(element => {
                element.style.opacity = 0;
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(element);
            });
            
            // Export PDF functionality
            document.getElementById('exportPdf').addEventListener('click', function() {
                exportToPdf('history-section', 'historique_presences.pdf');
            });
            
            document.getElementById('exportPdfFull').addEventListener('click', function() {
                exportToPdf('.main-content', 'rapport_complet_presences.pdf');
            });
            
            function exportToPdf(elementClass, filename) {
                const { jsPDF } = window.jspdf;
                const element = document.querySelector(elementClass);
                
                html2canvas(element).then(canvas => {
                    const imgData = canvas.toDataURL('image/png');
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const imgProps = pdf.getImageProperties(imgData);
                    const pdfWidth = pdf.internal.pageSize.getWidth();
                    const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                    
                    pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, pdfHeight);
                    pdf.save(filename);
                });
            }
        });
    </script>
</body>
</html>