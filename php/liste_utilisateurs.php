<?php
// Sécuriser: accès réservé à l'admin
session_start();
require_once '../includes/config.php';

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT * FROM users ORDER BY nom, prenom");
$users = $stmt->fetchAll();

// Récupération des classes
$stmt = $pdo->query("SELECT id, nom_classe FROM classes");
$all_classes = [];
foreach ($stmt as $row) {
    $all_classes[$row['id']] = $row['nom_classe'];
}

function libelles_ids($ids_str, $ref) {
    if (!$ids_str) return '';
    $ids = explode(',', $ids_str);
    $labels = [];
    foreach ($ids as $id) {
        $id = trim($id);
        if (isset($ref[$id])) {
            $labels[] = $ref[$id];
        }
    }
    return implode(', ', $labels);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8" />
<title>Gestion des Utilisateurs</title>
<link rel="stylesheet" href="../assets/css/style.css" />
<style>
/* Modernisation basique */
body { font-family: Arial, sans-serif; margin:20px; background:#f9f9f9; }
h2 { margin-bottom:1rem; }
table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
th, td { padding:10px; text-align:left; border-bottom:1px solid #ddd; }
th { background:#4CAF50; color:#fff; }
tr:hover { background:#f1f1f1; }
.input, select { padding:0.5rem; border:1px solid #ccc; border-radius:6px; }
.btn-connexion { background:#4CAF50; color:#fff; border:none; border-radius:6px; cursor:pointer; }
.btn-connexion:hover { background:#45a049; }
.pagination { margin-top:1rem; }
.pagination button { margin:0 3px; padding:5px 10px; border:none; border-radius:5px; cursor:pointer; }
.pagination button:hover { background:#ddd; }
#notification { position:fixed;top:20px;right:20px;padding:10px 20px;border-radius:8px;color:#fff;display:none;z-index:999; }
.no-photo { color:#888;font-style:italic; }
.filters { display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1rem; }
</style>
</head>
<body>

<label class="toggle-switch" title="Basculer mode clair/sombre">
    <input type="checkbox" id="modeToggle"> Mode sombre
</label>

<main>
<div class="form-container" style="max-width:1100px;">
    <h2>Liste des utilisateurs</h2>
    <a href="ajouter_utilisateur.php">
        <button id="btnAddUser" class="btn-connexion" style="margin-bottom:1rem;">Ajouter un utilisateur</button>
    </a>

    <!-- Filtres + Recherche -->
    <div class="filters">
        <input type="text" id="searchUser" placeholder="Rechercher un utilisateur..." class="input" style="flex:1;">

        <select id="filterRole" class="input">
            <option value="">-- Filtrer par rôle --</option>
            <option value="admin">Admin</option>
            <option value="personnel">Personnel</option>
            <option value="etudiant">Étudiant</option>
        </select>

        <select id="filterClasse" class="input">
            <option value="">-- Filtrer par classe/service --</option>
            <?php foreach ($all_classes as $id => $nom): ?>
                <option value="<?= htmlspecialchars($nom) ?>"><?= htmlspecialchars($nom) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Tableau -->
    <table id="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Matricule</th>
                <th>Rôle</th>
                <th>Classes/Services</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?= htmlspecialchars($user['nom']) ?></td>
            <td><?= htmlspecialchars($user['prenom']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= htmlspecialchars($user['matricule']) ?></td>
            <td><?= htmlspecialchars(ucfirst($user['type'])) ?></td>
            <td><?= htmlspecialchars(libelles_ids($user['classe_id'], $all_classes)) ?></td>
            <td>
                <?php if ($user['photo_reference']): ?>
                    <img src="../uploads/<?= htmlspecialchars($user['photo_reference']) ?>" style="height:40px;border-radius:8px;">
                <?php else: ?>
                    <span class="no-photo">Aucune</span>
                <?php endif; ?>
            </td>
            <td>
                <a href="modifier_utilisateur.php?id=<?= $user['id'] ?>" class="btn-connexion" style="padding:0.3rem 1rem;font-size:1rem;">Modifier</a>
                <a href="supprimer_utilisateur.php?id=<?= $user['id'] ?>" class="btn-connexion" style="background:#d9534f;color:#fff;padding:0.3rem 1rem;font-size:1rem;" onclick="return confirm('Confirmer la suppression?');">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination"></div>
</div>
</main>

<!-- Notifications -->
<div id="notification"></div>

<script>
// Recherche instantanée
document.getElementById("searchUser").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
});

// Filtre rôle
document.getElementById("filterRole").addEventListener("change", function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let role = row.cells[4].innerText.toLowerCase();
        row.style.display = (value === "" || role === value) ? "" : "none";
    });
});

// Filtre classe
document.getElementById("filterClasse").addEventListener("change", function() {
    let value = this.value.toLowerCase();
    document.querySelectorAll("tbody tr").forEach(row => {
        let classe = row.cells[5].innerText.toLowerCase();
        row.style.display = (value === "" || classe.includes(value)) ? "" : "none";
    });
});

// Pagination
function paginateTable(tableId, rowsPerPage = 8) {
    const table = document.querySelector(tableId);
    const rows = table.querySelectorAll("tbody tr");
    const pagination = document.querySelector(".pagination");
    let pageCount = Math.ceil(rows.length / rowsPerPage);
    let currentPage = 1;

    function showPage(page) {
        rows.forEach((row, index) => {
            row.style.display = (index >= (page-1)*rowsPerPage && index < page*rowsPerPage) ? "" : "none";
        });
    }

    function updatePagination() {
        pagination.innerHTML = "";
        for (let i = 1; i <= pageCount; i++) {
            let btn = document.createElement("button");
            btn.innerText = i;
            btn.className = "btn-connexion";
            btn.onclick = () => { currentPage = i; showPage(i); };
            pagination.appendChild(btn);
        }
    }

    showPage(currentPage);
    updatePagination();
}

document.addEventListener("DOMContentLoaded", () => {
    paginateTable("#table");
});

// Notifications
function showNotification(message, type="success") {
    const notif = document.getElementById("notification");
    notif.innerText = message;
    notif.style.background = (type === "success") ? "#28a745" : (type === "error" ? "#d9534f" : "#007bff");
    notif.style.display = "block";
    setTimeout(() => { notif.style.display = "none"; }, 3000);
}
</script>

<?php if(isset($_GET['success'])): ?>
<script>showNotification("Utilisateur ajouté avec succès!", "success");</script>
<?php endif; ?>
<?php if(isset($_GET['deleted'])): ?>
<script>showNotification("Utilisateur supprimé avec succès!", "success");</script>
<?php endif; ?>

</body>
</html>
