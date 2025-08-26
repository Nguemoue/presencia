function chargerClasses() {
  fetch('php/liste_classes.php').then(res => res.json()).then(data => {
    const tbody = document.getElementById('listeClasses');
    tbody.innerHTML = '';
    data.forEach(classe => {
      tbody.innerHTML += `
        <tr>
          <td>${classe.nom_classe}</td>
          <td>
            <input type="text" value="${classe.nom_classe}" id="mod${classe.id}" style="width:110px;">
            <button class="btn-connexion" onclick="modifierClasse(${classe.id})">Modifier</button>
            <button class="btn-supprimer" onclick="supprimerClasse(${classe.id})">Supprimer</button>
          </td>
        </tr>
      `;
    });
  });
}

function ajouterClasse() {
  const nom = document.getElementById('nomNouvelleClasse').value.trim();
  if (!nom) return;
  fetch('php/ajouter_classe.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'nom_classe=' + encodeURIComponent(nom)
  }).then(res => res.text()).then(() => {
    document.getElementById('nomNouvelleClasse').value = '';
    chargerClasses();
  });
}

function modifierClasse(id) {
  const nom = document.getElementById('mod'+id).value.trim();
  if (!nom) return;
  fetch('php/modifier_classe.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id='+id+'&nom_classe='+encodeURIComponent(nom)
  }).then(res => res.text()).then(() => {
    chargerClasses();
  });
}

function supprimerClasse(id) {
  if (!confirm('Confirmer la suppression ?')) return;
  fetch('php/supprimer_classe.php', {
    method: 'POST',
    headers: {'Content-Type':'application/x-www-form-urlencoded'},
    body: 'id='+id
  }).then(res => res.text()).then(() => {
    chargerClasses();
  });
}

// Au chargement
document.addEventListener('DOMContentLoaded', chargerClasses);
