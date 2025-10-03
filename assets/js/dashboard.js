let refreshInterval = 60 * 1000; // 1 minute

async function fetchDashboard() {
    let res = await fetch('api.php?action=dashboard');
    let data = await res.json();
    // MAJ valeurs cartes
    document.getElementById('presencesJour').innerText = data.presencesJour;
    document.getElementById('absencesJour').innerText = data.absencesJour;
    document.getElementById('retardsJour').innerText = data.retardsJour;
    document.getElementById('presencesMois').innerText = data.presencesMois;
    document.getElementById('lastUpdate').innerText = data.lastUpdate;
    // Init graphiques
    updateCharts(data);
    updateTable(data.tableData);
}

// Charts.js : Chart.js
function updateCharts(data){
    // Histogramme barPresence
    new Chart(document.getElementById('barPresence'), {
        type: 'bar',
        data: {
            labels: data.days,
            datasets: [
                { label: 'Présences', data: data.presenceCounts, backgroundColor: '#c0ef20' },
                { label: 'Absences', data: data.absenceCounts, backgroundColor: '#ffd400' },
            ]
        }
    });
    // Pie
    new Chart(document.getElementById('piePresence'), {
        type: 'pie',
        data: {
            labels: ['Présent', 'Absent', 'Retard'],
            datasets: [
                {
                    data: [data.presencesJour, data.absencesJour, data.retardsJour],
                    backgroundColor: ['#75f94c', '#ff4e4e', '#ffd400']
                }
            ]
        }
    });
}

// Table dynamique
function updateTable(tData){
    let table = "";
    tData.forEach(item=>{
        table += `<tr>
            <td>${item.nom}</td>
            <td>${item.classe}</td>
            <td>${item.date}</td>
            <td>${item.statut}</td>
            <td>${item.heure}</td>
        </tr>`;
    });
    document.getElementById('tableBody').innerHTML = table;
}

// Filtrage, recherche, export : à ajouter selon besoins

setInterval(fetchDashboard, refreshInterval);
window.onload = fetchDashboard;
