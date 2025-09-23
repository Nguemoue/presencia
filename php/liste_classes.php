<?php
// Sécuriser: accès réservé à l'admin
session_start();
require_once '../includes/config.php';

// Récupération des classes
$stmt = $pdo->query("SELECT * FROM classes ORDER BY nom_classe");
$classes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Gestion des Classes / Services</title>
<link rel="stylesheet" href="../assets/css/style.css" />
<style>
    .search-filter {
        display:flex;
        gap:1rem;
        margin-bottom:1rem;
        flex-wrap:wrap;
    }
    .search-filter input, .search-filter select {
        padding:0.5rem;
        border:1px solid #ccc;
        border-radius:6px;
    }
    .search-filter button {
        padding:0.5rem 1rem;
        border:none;
        border-radius:6px;
        background:#28a745;
        color:#fff;
        cursor:pointer;
    }
    .search-filter button:hover {
        background:#218838;
    }
    th.sortable {
        cursor:pointer;
    }
    th.sortable:hover {
        text-decoration:underline;
    }
      
</style>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.getElementById("searchInput");
    const tableRows = document.querySelectorAll("#classesTable tbody tr");
    const ths = document.querySelectorAll("th.sortable");

    // Recherche instantanée
    searchInput.addEventListener("keyup", function() {
        const val = this.value.toLowerCase();
        tableRows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(val) ? "" : "none";
        });
    });

    // Tri dynamique
    ths.forEach((th, index) => {
        th.addEventListener("click", () => {
            const tbody = th.closest("table").querySelector("tbody");
            const rows = Array.from(tbody.querySelectorAll("tr"));
            const asc = th.dataset.asc === "true";
            rows.sort((a, b) => {
                const A = a.children[index].innerText.toLowerCase();
                const B = b.children[index].innerText.toLowerCase();
                return asc ? A.localeCompare(B) : B.localeCompare(A);
            });
            rows.forEach(r => tbody.appendChild(r));
            th.dataset.asc = !asc;
        });
    });
});
</script>
</head>
<body>
<main>
<div class="form-container" style="max-width:1000px;">
    <h2>Liste des classes / services</h2>
    <a href="gestion_classe.php"><button id="btnAddClass" class="btn-connexion" style="margin-bottom:1rem;">Ajouter une classe / service</button></a> 

    <div class="search-filter">
        <input type="text" id="searchInput" placeholder="Rechercher une classe/service...">
    </div>

    <table id="classesTable" style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th class="sortable">Nom</th>
                <th class="sortable">Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($classes as $classe): ?>
        <tr>
            <td><?= htmlspecialchars($classe['nom_classe']) ?></td>
            <td><?= htmlspecialchars($classe['description']) ?></td>
            <td>
                <a href="gestion_classe.php?id=<?= $classe['id'] ?>" class="btn-connexion" style="padding:0.3rem 1rem;font-size:1rem;">Modifier</a>
                <a href="supprimer_classe.php?id=<?= $classe['id'] ?>" class="btn-connexion" style="background:#d9534f;color:#fff;padding:0.3rem 1rem;font-size:1rem;" onclick="return confirm('Confirmer la suppression?');">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</main>
</body>
</html>
